<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Meja {{ $table->number }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">QR Code Meja {{ $table->number }}</h2>
        <p class="text-muted">Scan QR ini untuk akses menu pemesanan</p>
    </div>

    <div class="bg-white p-4 rounded shadow text-center mx-auto" style="max-width: 350px;">
        <div class="mb-3">
            {!! QrCode::size(200)->generate($url) !!}
        </div>
        <div class="small text-break text-muted">{{ $url }}</div>
    </div>

    <div class="text-center mt-4 no-print d-flex flex-column align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Cetak QR Code
        </button>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Meja
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
