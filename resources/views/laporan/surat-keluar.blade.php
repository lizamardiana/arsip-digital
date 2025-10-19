<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Surat Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: right; border-top: 1px solid #333; padding-top: 20px; }
        .no-data { text-align: center; padding: 50px; color: #6c757d; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>LAPORAN SURAT KELUAR</h2>
            <h4>SISTEM ARSIP SURAT DIGITAL</h4>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
            <p>Dibuat pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
        </div>

        <!-- Tombol Print -->
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Laporan
            </button>
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </button>
        </div>

        <!-- Tabel Data -->
        @if($data->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Nomor Surat</th>
                    <th width="12%">Tanggal Surat</th>
                    <th width="12%">Tanggal Kirim</th>
                    <th width="15%">Tujuan</th>
                    <th width="20%">Perihal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_surat }}</td>
                    <td>{{ $item->tanggal_surat->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_kirim->format('d/m/Y') }}</td>
                    <td>{{ $item->tujuan }}</td>
                    <td>{{ $item->perihal }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            <i class="fas fa-paper-plane fa-4x mb-3"></i>
            <h4>Tidak Ada Data Surat Keluar</h4>
            <p>Tidak ditemukan surat keluar pada periode yang dipilih.</p>
            <p class="text-muted">Periode: {{ $startDate }} hingga {{ $endDate }}</p>
        </div>
        @endif

        <!-- Footer -->
        @if($data->count() > 0)
        <div class="footer">
            <p>Total Surat Keluar: <strong>{{ $data->count() }}</strong></p>
            <p>Dicetak oleh: {{ auth()->user()->name }}</p>
        </div>
        @endif
    </div>

    <!-- Font Awesome untuk icon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        @if($data->count() > 0)
        window.onload = function() {
            // Optional: auto print
            // window.print();
        }
        @endif
    </script>
</body>
</html>