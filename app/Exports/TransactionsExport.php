<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionsExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
     protected $data;

    public function collection()
    {
        $orders = Order::select('customer_name', 'payment_type', 'table_number', 'total_price', 'status', 'created_at')->get();

        // Simpan dalam properti untuk kalkulasi total mingguan
        $this->data = $orders;

        // Ubah ke struktur baru dengan tambahan kolom waktu terformat
        return $orders->map(function ($order) {
            return [
                'customer_name'    => $order->customer_name,
                'payment_type'     => $order->payment_type,
                'table_number'     => $order->table_number,
                'total_price'      => $order->total_price,
                'status'           => $order->status,
                'created_at'       => Carbon::parse($order->created_at)->translatedFormat('d F Y H:i'),
            ];
        });
    }


    public function headings(): array
    {
        return ['Nama Pelanggan', 'Tipe Pembayaran', 'Nomor Meja', 'Total Harga', 'Status', 'Waktu Pemesanan'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Gaya untuk header
                $headerRange = 'A1:F1';
                $sheet->getDelegate()->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D1E7DD']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ]
                ]);

                foreach (range('A', 'F') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                // Hitung total
                $totalKeseluruhan = $this->data->sum('total_price');
                $totalMingguan = $this->data->filter(function ($order) {
                    return \Carbon\Carbon::parse($order->created_at)->greaterThanOrEqualTo(now()->subDays(7));
                })->sum('total_price');

                $lastRow = count($this->data) + 2;

                // Isi dan style total keseluruhan
                $sheet->setCellValue('A' . $lastRow, 'Total Pendapatan Keseluruhan:');
                $sheet->setCellValue('B' . $lastRow, $totalKeseluruhan);
                $sheet->getDelegate()->getStyle("A$lastRow:B$lastRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3CD']
                    ]
                ]);

                // Isi dan style total mingguan
                $sheet->setCellValue('A' . ($lastRow + 1), 'Total Pendapatan Mingguan:');
                $sheet->setCellValue('B' . ($lastRow + 1), $totalMingguan);
                $sheet->getDelegate()->getStyle("A" . ($lastRow + 1) . ":B" . ($lastRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8D7DA']
                    ]
                ]);

                // Tambahkan border ke seluruh data
                $sheet->getDelegate()->getStyle("A1:F" . ($lastRow + 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            },
        ];
    }
}
