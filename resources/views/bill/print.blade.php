<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم #{{ $bill->id }}</title>
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
        <p>رقم الفاتورة: {{ $bill->id }}</p>
        <p>التاريخ: {{ $bill->created_at->format('Y-m-d H:i') }}</p>
        <p>الموظف: {{ $bill->employee_receipt }}</p>
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
            @php
                $totalDiscount = 0;
                $totalBeforDiscount = 0;
            @endphp
            @foreach($bill->details as $billDetail)
                <tr>
                    <td>{{ $billDetail->product->name ?? '-' }}</td>
                    <td>{{ $billDetail->quantity }}</td>
                    <td>{{ number_format($billDetail->cost, 2) }}</td>
                    <td>{{ number_format($billDetail->total, 2) }}</td>
                    @php
                        $totalDiscount += $billDetail->discount ?? 0;
                        $totalBeforDiscount += $billDetail->cost * $billDetail->quantity;
                    @endphp
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3">المجموع</td>
                <td>{{ number_format($bill->total_amount, 2) }}</td>
            </tr>

            <tr class="total">
                <td colspan="3">الخصم</td>
                <td>{{ number_format($totalDiscount, 2) }}</td>
            </tr>
            <tr class="total">
                <td colspan="3">الإجمالي بعد الخصم</td>
                <td>{{ number_format($bill->total_amount , 2) }}</td>
            </tr>

        </tfoot>
    </table>

    <div class="footer">
        <p>شكرًا لتعاملكم معنا!</p>
        <button class="no-print" onclick="window.print()">طباعة</button>
    </div>

</body>
<script>
    window.onload = function() {
        window.print();

        // بعد الطباعة، أعد التوجيه بعد 500 مللي ثانية (يمكن تعديلها)
        setTimeout(function() {
            window.location.href = "{{ route('billDetails.close', ['billId' => $bill->id]) }}";
        }, 500);
    };

</script>
</html>

