<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $receipt->receipt_number }} - Brewly Cafe</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .ticket {
            width: 300px;
            max-width: 300px;
            background: #fff;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-dashed { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #222;
            color: #fff;
            text-align: center;
            border: none;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket { border: none; box-shadow: none; width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="ticket">
        <button class="btn-print no-print" onclick="window.print()">🖨️ Cetak Struk (Print Receipt)</button>

        <div class="text-center">
            <h2 style="margin: 0; font-size: 16px;">BREWLY CAFE & ROASTERY</h2>
            <div style="font-size: 10px;">Jl. Kopi Harum No. 88, Jakarta Selatan</div>
            <div style="font-size: 10px;">Telp: (021) 7890-1234</div>
        </div>

        <div class="border-dashed"></div>

        <div class="row">
            <span>No:</span>
            <span class="font-bold">{{ $receipt->receipt_number }}</span>
        </div>
        <div class="row">
            <span>Waktu:</span>
            <span>{{ $receipt->issued_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="row">
            <span>Kasir:</span>
            <span>{{ $receipt->issuedByStaff->name }}</span>
        </div>
        <div class="row">
            <span>Pelanggan:</span>
            <span>{{ $receipt->payment->order->customer_name ?: 'Pelanggan' }}</span>
        </div>
        <div class="row">
            <span>Tipe:</span>
            <span>{{ $receipt->payment->order->order_type === 'DINE_IN' ? 'DINE IN (Meja ' . ($receipt->payment->order->table?->table_number ?? '-') . ')' : 'TAKE AWAY' }}</span>
        </div>

        <div class="border-dashed"></div>

        @foreach($receipt->payment->order->items as $item)
            <div class="row font-bold">
                <span>{{ $item->menu->name }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="row" style="font-size: 10px; color: #555;">
                <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                @if($item->note)
                    <span>({{ $item->note }})</span>
                @endif
            </div>
        @endforeach

        <div class="border-dashed"></div>

        <div class="row">
            <span>Subtotal:</span>
            <span>{{ number_format($receipt->payment->order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="row font-bold">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($receipt->payment->order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="row">
            <span>Metode:</span>
            <span>{{ $receipt->payment->method }}</span>
        </div>
        <div class="row">
            <span>Tunai / Bayar:</span>
            <span>{{ number_format($receipt->payment->amount_paid, 0, ',', '.') }}</span>
        </div>
        <div class="row font-bold">
            <span>Kembalian:</span>
            <span>{{ number_format($receipt->payment->change_amount, 0, ',', '.') }}</span>
        </div>

        <div class="border-dashed"></div>

        <div class="text-center" style="margin-top: 10px; font-size: 10px;">
            <div>*** TERIMA KASIH ***</div>
            <div>Silakan Datang Kembali!</div>
            <div style="margin-top: 5px;">WiFi: Brewly_Guest | Pass: kopienak123</div>
        </div>
    </div>

</body>
</html>

