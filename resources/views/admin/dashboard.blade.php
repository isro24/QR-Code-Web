@extends('layouts.admin')

@section('content')
<h2 class="text-3xl font-bold mb-6">Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-semibold mb-2">Total Produk</h3>
        <p class="text-3xl">{{ $totalProducts ?? 0 }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-semibold mb-2">Total Pesanan</h3>
        <p class="text-3xl">{{ $totalTransactions ?? 0 }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-semibold mb-2">Pesanan Tertunda</h3>
        <p class="text-3xl">{{ $pendingTransactions ?? 0 }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-green-50 p-6 rounded shadow border-l-4 border-green-600">
        <h3 class="text-xl font-semibold text-green-700 mb-2">Pendapatan Hari Ini</h3>
        <p class="text-3xl text-green-800 font-bold">
            Rp {{ number_format($todayRevenue, 0, ',', '.') }}
        </p>
    </div>

    <div class="bg-green-50 p-6 rounded shadow border-l-4 border-green-600">
        <h3 class="text-xl font-semibold text-green-700 mb-2">Pendapatan Minggu Ini</h3>
        <p class="text-3xl text-green-800 font-bold">
            Rp {{ number_format($weekRevenue, 0, ',', '.') }}
        </p>
    </div>

    <div class="mt-10 bg-white p-6 rounded shadow">
        <h3 class="text-xl font-semibold mb-4">Grafik Pendapatan 7 Hari Terakhir</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueLast7Days->pluck('date')) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($revenueLast7Days->pluck('total')) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgba(5, 150, 105, 1)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString('id-ID')
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
