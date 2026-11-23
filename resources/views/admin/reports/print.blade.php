<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan ({{ $startDate }} s/d {{ $endDate }}) - Brewly Cafe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 25px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        .meta-box {
            display: flex;
            justify-content: space-between;
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 7px 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }
        .summary-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            background: #fafafa;
        }
        .summary-title { font-size: 9px; text-transform: uppercase; color: #777; margin-bottom: 4px; }
        .summary-val { font-size: 14px; font-weight: bold; color: #111; }
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 30px;
        }
        .sign-col { text-align: center; width: 180px; }
        .sign-line { border-bottom: 1px solid #333; margin-top: 60px; margin-bottom: 5px; }
        .btn-print {
            padding: 8px 16px;
            background: #222;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF Laporan</button>
    </div>

    <div class="header">
        <h1>BREWLY CAFE & ROASTERY</h1>
        <p>Jl. Kopi Harum No. 88, Jakarta Selatan • Telp: (021) 7890-1234</p>
        <p><strong>LAPORAN RESMI REKAPITULASI PENJUALAN</strong></p>
    </div>

    <div class="meta-box">
        <div>
            <span>Periode Laporan: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</strong></span>
        </div>
        <div>
            <span>Dicetak Pada: <strong>{{ now()->format('d/m/Y H:i') }}</strong> oleh <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong></span>
        </div>
    </div>

    <!-- Summary KPI -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-title">Total Pendapatan Bersih</div>
            <div class="summary-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Total Transaksi Lunas</div>
            <div class="summary-val">{{ $totalOrdersCount }} Transaksi</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Penjualan Tunai (Cash)</div>
            <div class="summary-val">Rp {{ number_format($cashTotal, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Penjualan QRIS & Transfer</div>
            <div class="summary-val">Rp {{ number_format($qrisTotal + $transferTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Table of Orders -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>Waktu Transaksi</th>
                <th>No. Order</th>
                <th>No. Struk</th>
                <th>Nama Pelanggan</th>
                <th>Tipe / Meja</th>
                <th>Metode Bayar</th>
                <th>Kasir</th>
                <th class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $ord)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $ord->ordered_at->format('d/m/Y H:i') }}</td>
                    <td>#{{ $ord->order_id }}</td>
                    <td>{{ $ord->payment?->receipt?->receipt_number ?? '-' }}</td>
                    <td>{{ $ord->customer_name ?: 'Pelanggan' }}</td>
                    <td>{{ $ord->order_type === 'DINE_IN' ? 'Dine In (Meja ' . ($ord->table?->table_number ?? '-') . ')' : 'Take Away' }}</td>
                    <td>{{ $ord->payment?->method ?? 'CASH' }}</td>
                    <td>{{ $ord->payment?->confirmedByStaff?->name ?? 'Kasir' }}</td>
                    <td class="text-right font-bold">{{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
            <tr style="background-color: #f7f7f7; font-weight: bold;">
                <td colspan="8" class="text-right">TOTAL KESELURUHAN PENDAPATAN:</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-area">
        <div class="sign-col">
            <p>Dibuat Oleh,</p>
            <div class="sign-line"></div>
            <p><strong>{{ auth()->user()->name ?? 'Kasir / Staf' }}</strong></p>
            <p style="font-size: 10px; color: #666;">Staf Kafe Brewly</p>
        </div>

        <div class="sign-col">
            <p>Disetujui Oleh,</p>
            <div class="sign-line"></div>
            <p><strong>Manager Kafe / Owner</strong></p>
            <p style="font-size: 10px; color: #666;">Pimpinan Brewly</p>
        </div>
    </div>

</body>
</html>

