@extends('layouts.master')
@section('title', __('messages.dashboard.title'))

@section('content')
@include('layouts.partials.sweet_alert')

<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">

            {{-- العنوان الرئيسي --}}
            <h1 class="heading_title mb-4" style="margin-top: 60px;">
                @lang('messages.dashboard.title')
            </h1>

            {{-- البطاقات --}}
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card p-3 bg-blue-100 rounded shadow-sm text-center">
                        <h5>@lang('messages.dashboard.sales_today')</h5>
                        <p class="fw-bold fs-5 text-primary">{{ number_format($salesToday,2) }} ₪</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-green-100 rounded shadow-sm text-center">
                        <h5>@lang('messages.dashboard.bills_today')</h5>
                        <p class="fw-bold fs-5 text-success">{{ $billsToday }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-red-100 rounded shadow-sm text-center">
                        <h5>@lang('messages.dashboard.total_debts')</h5>
                        <p class="fw-bold fs-5 text-danger">{{ number_format($totalDebts,2) }} ₪</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-yellow-100 rounded shadow-sm text-center">
                        <h5>@lang('messages.dashboard.low_stock')</h5>
                        <p class="fw-bold fs-5 text-warning">{{ $lowStock }} @lang('messages.dashboard.items')</p>
                    </div>
                </div>
            </div>

            {{-- آخر 10 فواتير --}}
            <h3 class="mt-5 mb-3">@lang('messages.dashboard.recent_bills')</h3>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>@lang('messages.bill.bill_number')</th>
                            <th>@lang('messages.customer.customer_name')</th>
                            <th>@lang('messages.bill.net_amount')</th>
                            <th>@lang('messages.bill.bill_date')</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($recentBills as $bill)
                            <tr>
                                <td>{{ $bill->id }}</td>
                                <td>{{ $bill->customer->name ?? __('messages.customer.general_customer') }}</td>
                                <td>{{ number_format($bill->net_amount,2) }} ₪</td>
                                <td>{{ $bill->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- المبيعات آخر 7 أيام --}}
            <div class="mt-5">
                <h3 class="mb-3">@lang('messages.dashboard.sales_last7days')</h3>

                <!-- أزرار التبديل -->
                <div class="text-center mb-3">
                    <button class="btn btn-primary btn-sm" onclick="updateSalesChartType('bar')">مخطط عمودي</button>
                    <button class="btn btn-success btn-sm" onclick="updateSalesChartType('line')">مخطط خطي</button>
                    <button class="btn btn-warning btn-sm" onclick="updateSalesChartType('pie')">مخطط دائري</button>
                </div>

                <!-- الرسم البياني -->
                <div id="salesChart"></div>
            </div>

            {{-- الديون حسب العملاء --}}
            <div class="mt-5">
                <h3 class="mb-3">@lang('messages.dashboard.debts_by_customers')</h3>

                <!-- أزرار التبديل -->
                <div class="text-center mb-3">
                    <button class="btn btn-primary btn-sm" onclick="updateDebtsChartType('bar')">مخطط عمودي</button>
                    <button class="btn btn-success btn-sm" onclick="updateDebtsChartType('line')">مخطط خطي</button>
                    <button class="btn btn-warning btn-sm" onclick="updateDebtsChartType('pie')">مخطط دائري</button>
                </div>

                <!-- الرسم البياني -->
                <div id="debtsChart"></div>
            </div>

        </div>
    </div>
</div>
<!--/End Main content container-->

{{-- تضمين مكتبة ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // بيانات من السيرفر
    const salesData = @json($salesData);
    const salesLabels = @json($salesLabels);

    const debtsData = @json($debtsData);
    const debtsLabels = @json($debtsLabels);

    // لوج للتأكد من المحتوى (افتح DevTools -> Console)
    console.log('salesLabels:', salesLabels);
    console.log('salesData:', salesData);
    console.log('debtsLabels:', debtsLabels);
    console.log('debtsData:', debtsData);

    let salesChart = null;
    let debtsChart = null;

    function buildOptions(type, labels, data, seriesName, titleText, xTitle, yTitle, colors = []) {
        if (type === 'pie' || type === 'donut') {
            // للمخطط الدائري: series يجب أن تكون array of numbers و labels منفصلة
            return {
                chart: { type, height: 350, toolbar: { show: false } },
                series: data, // <-- مصفوفة أرقام
                labels: labels,
                legend: { position: 'bottom' },
                dataLabels: { enabled: true },
                title: { text: titleText, align: 'center' },
                colors
            };
        }

        // للمخططات العمودية/الخطية
        return {
            chart: { type, height: 350, toolbar: { show: false } },
            series: [{ name: seriesName, data }], // <-- object مع data
            xaxis: { categories: labels, title: { text: xTitle } },
            yaxis: { title: { text: yTitle } },
            dataLabels: { enabled: true },
            title: { text: titleText, align: 'center' },
            colors
        };
    }

    function renderSalesChart(type = 'bar') {
        try {
            // تحقق من تطابق الأطوال عند نوع pie
            if ((type === 'pie' || type === 'donut') && salesLabels.length !== salesData.length) {
                console.warn('تحذير: عدد التسميات لا يساوي عدد القيم للمخطط الدائري (sales).');
            }

            const options = buildOptions(
                type,
                salesLabels,
                salesData,
                'المبيعات',
                'مبيعات آخر 7 أيام',
                'التاريخ',
                'قيمة المبيعات (₪)',
                ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            );

            if (salesChart) salesChart.destroy();
            salesChart = new ApexCharts(document.querySelector('#salesChart'), options);
            salesChart.render();
        } catch (err) {
            console.error('خطأ في رسم مخطط المبيعات:', err);
            document.querySelector('#salesChart').innerHTML = '<div class="p-3 text-danger">خطأ في عرض مخطط المبيعات — راجع console.</div>';
        }
    }

    function renderDebtsChart(type = 'bar') {
        try {
            if ((type === 'pie' || type === 'donut') && debtsLabels.length !== debtsData.length) {
                console.warn('تحذير: عدد التسميات لا يساوي عدد القيم للمخطط الدائري (debts).');
            }

            const options = buildOptions(
                type,
                debtsLabels,
                debtsData,
                'الديون',
                'الديون حسب العملاء',
                'الزبائن',
                'المبلغ المتبقي (₪)',
                ['#e74a3b', '#36b9cc', '#f6c23e', '#4e73df', '#1cc88a']
            );

            if (debtsChart) debtsChart.destroy();
            debtsChart = new ApexCharts(document.querySelector('#debtsChart'), options);
            debtsChart.render();
        } catch (err) {
            console.error('خطأ في رسم مخطط الديون:', err);
            document.querySelector('#debtsChart').innerHTML = '<div class="p-3 text-danger">خطأ في عرض مخطط الديون — راجع console.</div>';
        }
    }

    function updateSalesChartType(type) { renderSalesChart(type); }
    function updateDebtsChartType(type) { renderDebtsChart(type); }

    // بدء
    document.addEventListener('DOMContentLoaded', function () {
        renderSalesChart('bar'); // افتراضي
        renderDebtsChart('bar');
    });
</script>
@endsection
