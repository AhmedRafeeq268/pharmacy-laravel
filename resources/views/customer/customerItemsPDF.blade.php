<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>@lang('messages.reports.customer_report')</title>
    <style>
        @font-face {
        font-family: 'Amiri';
        src: url("{{ public_path('fonts/Amiri-Regular.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
        }

        @font-face {
            font-family: 'Amiri';
            src: url("{{ public_path('fonts/Amiri-Bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'Amiri', DejaVu Sans, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            unicode-bidi: embed;
            margin: 30px;
            background-color: #f9f9f9;
        }

        /* هوية الصيدلية */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1, .header p {
            margin: 0;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            font-size: 14px;
            color: #555;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px 8px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        td {
            color: #333;
        }

        /* أسفل الصفحة للرقم */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-top: 20px;
        }

        @media print {
            body { margin: 10px; }
            table { font-size: 12px; }
        }
    </style>
</head>
<body>
    <!-- هوية الصيدلية -->
    <div class="header">
        {{-- <img src="{{ asset('images/pharmacy_logo.png') }}" alt="Logo"> --}}
        <h1>@lang('messages.reports.Sheikh_nasser_pharmacy')</h1>
        <p>
        @if(app()->getLocale() == 'ar')
            العنوان:  مفترق الشيخ ناصر ، المدينة:خانيونس | الهاتف: 0591234567-
        @else
            Address: Sheikh Nasser Junction , City: Khan Younis | Phone: 059-1234567
        @endif
        </p>
    </div>

    <h2>@lang('messages.reports.customer_report')</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('messages.customer.customer_name')</th>
                <th>@lang('messages.customer.phone')</th>
                <th>@lang('messages.customer.email')</th>
                <th>@lang('messages.customer.address')</th>
                <th>@lang('messages.customer.id_card')</th>
                <th>@lang('messages.customer.address_details')</th>
                <th>@lang('messages.created_at')</th>
            </tr>
        </thead>
        <tbody>
            @php $id = 1; @endphp
            @foreach($customersItems as $item)
                <tr>
                    <td>{{ $id++ }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->address }}</td>
                    <td>{{ $item->id_card }}</td>
                    <td>{{ $item->address_details }}</td>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        @if(app()->getLocale() == 'ar')
            &copy; {{ date('Y') }}  جميع الحقوق محفوظة - صيدلية الشيخ ناصر
        @else
            &copy; {{ date('Y') }} All rights reserved - Sheikh Nasser Pharmacy
        @endif
    </div>
</body>
</html>
