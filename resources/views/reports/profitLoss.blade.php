<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.reports.profit_loss_report') }}</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            margin:0;
            padding: 20px;
            background: #fff;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .wrap {
            width: 100%;
            max-width: 100%;
            margin: auto;
        }
        .c { text-align:center; }
        .row { display:flex; justify-content:space-between; font-size:16px; margin:5px 0; }
        hr { border:0; border-top:1px dashed #aaa; margin:10px 0; }
        .b { font-weight:bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 15px; }
        th, td { border: 1px solid #aaa; padding: 5px; text-align: center; }
        @media print {
            .no-print { display:none }
            body { margin:0; padding:0; }
            .wrap { width: 100%; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="c b" style="margin-bottom: 20px;">{{ __('messages.reports.store_name') }}</div>
    <div class="c" style="margin-bottom: 30px;">{{ __('messages.reports.profit_loss_report') }}</div>

    <hr>

    <div class="row"><span>{{ __('messages.reports.from') }}</span><span>{{ $from }}</span></div>
    <div class="row"><span>{{ __('messages.reports.to') }}</span><span>{{ $to }}</span></div>

    <hr>

    <div class="row b"><span>{{ __('messages.reports.total_sales') }}</span><span>{{ number_format($totalSales) }}</span></div>
    <div class="row"><span>{{ __('messages.reports.total_profit') }}</span><span>{{ number_format($totalProfit) }}</span></div>
    <div class="row"><span>{{ __('messages.reports.expenses') }}</span><span>{{ number_format($totalExpenses) }}</span></div>
    <div class="row"><span>{{ __('messages.reports.damaged') }}</span><span>{{ number_format($totalCostItemDamaged) }}</span></div>
    <div class="row"><span>{{ __('messages.reports.discounts') }}</span><span>{{ number_format($totalDiscount) }}</span></div>
    <div class="row b"><span>{{ __('messages.reports.net_profit') }}</span><span>{{ number_format($netProfit) }}</span></div>

    <hr>

    <div class="c b">{{ __('messages.reports.product_profit_detail') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.reports.product') }}</th>
                <th>{{ __('messages.reports.quantity_sold') }}</th>
                <th>{{ __('messages.reports.total_product_profit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productProfits as $item)
                <tr>
                    <td>{{ $item->product->name ?? __('messages.reports.unknown') }}</td>
                    <td>{{ $item->total_qty }}</td>
                    <td>{{ number_format($item->total_profit) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <div class="c">{{ __('messages.reports.thanks') }}</div>
    <div class="c no-print"><button onclick="window.print()">{{ __('messages.reports.print') }}</button></div>
</div>

<script>
    // يفتح الطباعة مباشرة عند تحميل الصفحة
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
