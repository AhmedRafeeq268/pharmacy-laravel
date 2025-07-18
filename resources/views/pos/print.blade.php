<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم #{{ $posBill->id }}</title>
    <style>
        body { direction: rtl; font-family: 'Tahoma'; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        .no-border { border: none; }
        .header, .footer { text-align: center; margin: 20px 0; }
        .total { font-weight: bold; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>صيدلية الشيخ ناصر</h2>
        <p>رقم الفاتورة: {{ $posBill->id }}</p>
        <p>التاريخ: {{ $posBill->created_at->format('Y-m-d H:i') }}</p>
        <p>الموظف: {{ $posBill->employee->name ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر للوحدة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posBill->details as $posBillDetail)
                <tr>
                    <td>{{ $posBillDetail->product->name ?? '-' }}</td>
                    <td>{{ $posBillDetail->quantity }}</td>
                    <td>{{ number_format($posBillDetail->unit_price, 2) }}</td>
                    <td>{{ number_format($posBillDetail->unit_price * $posBillDetail->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3">المجموع</td>
                <td>{{ number_format($posBill->total_amount, 2) }}</td>
            </tr>

            <tr class="total">
                <td colspan="3">الخصم</td>
                <td>{{ number_format($posBill->discount, 2) }}</td>
            </tr>
            <tr class="total">
                <td colspan="3">الإجمالي بعد الخصم</td>
                <td>{{ number_format($posBill->total_amount - $posBill->discount, 2) }}</td>
            </tr>

        </tfoot>
    </table>

    <div class="footer">
        <p>شكرًا لتعاملكم معنا!</p>
        <button class="no-print" onclick="window.print()">طباعة</button>
    </div>

</body>
<script>
    window.print();
</script>
</html>

