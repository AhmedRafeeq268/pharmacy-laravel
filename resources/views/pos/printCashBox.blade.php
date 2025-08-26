<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير إغلاق الصندوق</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            margin:0;
            padding: 20px;
            background: #fff;
        }
        .wrap {
            width: 100%;
            max-width: 100%;
            margin: auto;
        }
        .c {
            text-align:center;
        }
        .row {
            display:flex;
            justify-content:space-between;
            font-size:16px;
            margin:5px 0;
        }
        hr {
            border:0;
            border-top:1px dashed #aaa;
            margin:10px 0;
        }
        .b {
            font-weight:bold;
        }
        @media print {
            .no-print { display:none }
            body { margin:0; padding:0; }
            .wrap { width: 100%; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="c b">{{ $closing['store_name'] ?? 'اسم الصيدلية' }}</div>
    <div class="c">تقرير إغلاق الصندوق</div>
    <hr>

    <div class="row"><span>رقم الإغلاق</span><span>#{{ $closingId ?? '-' }}</span></div>
    <div class="row"><span>الكاشير</span><span>{{ $casherName ?? '-' }}</span></div>
    <div class="row"><span>من</span><span>{{ $opened_at ?? '-' }}</span></div>
    <div class="row"><span>إلى</span><span>{{ $closed_at ?? '-' }}</span></div>
    <hr>

    <div class="row b"><span>إجمالي المبيعات</span><span>{{ number_format($totalAmounts) }}</span></div>
    <div class="row"><span>مرتجعات</span><span>{{ number_format($totalReturns) }}</span></div>
    <div class="row"><span>خصومات</span><span>{{ number_format($totalDescounts) }}</span></div>
    <div class="row b"><span>صافي المبيعات</span><span>{{ number_format($netAmounts) }}</span></div>
    <hr>

    <div class="row"><span>رصيد بداية</span><span>{{ number_format($opening_balance) }}</span></div>
    <div class="row b"><span>المتوقع نقداً</span><span>{{ number_format($payCash) }}</span></div>
    <div class="row b"><span>المتوقع دين</span><span>{{ number_format($payDebt) }}</span></div>
    <div class="row b"><span>المتوقع فيزا</span><span>{{ number_format($payVisa) }}</span></div>
    <hr>

    <div class="c">شكراً لكم</div>
    <div class="c no-print"><button onclick="window.print()">طباعة</button></div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
