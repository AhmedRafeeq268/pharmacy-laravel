<?php

namespace App\Http\Controllers;

use App\Models\CashBoxTransaction;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use App\Models\PosBillDetails;

class ReportController extends Controller
{
    // يفتح صفحة اختيار الفترة
    public function profitLossFilter()
    {
        return view('reports.profitLossFilter');
    }

    // يجيب التقرير
    public function profitLoss(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        $totalSales = PosBillDetails::whereBetween('created_at', [$from, $to])->sum('price');
        $totalProfit = PosBillDetails::whereBetween('created_at', [$from, $to])->sum('profit');
        $totalExpenses = CashBoxTransaction::where('type', 'expense')
                                           ->whereBetween('created_at', [$from, $to])
                                           ->sum('amount');

        $totalCostItemDamaged = DamagedItem::whereBetween('damaged_items.created_at', [$from, $to])
        ->join('products', 'damaged_items.product_id', '=', 'products.id')
        ->selectRaw('SUM(damaged_items.quantity * products.unit_price) as total')
        ->value('total') ?? 0;

        $netProfit = $totalProfit - abs($totalExpenses) - $totalCostItemDamaged;

        $productProfits = PosBillDetails::selectRaw('product_id, SUM(quantity) as total_qty, SUM(profit) as total_profit')
                        ->whereBetween('created_at', [$from, $to])
                        ->groupBy('product_id')
                        ->with('product')
                        ->get();

        return view('reports.profitLoss', compact(
            'from','to','totalSales','totalProfit','totalExpenses','netProfit','productProfits','totalCostItemDamaged'
        ));
    }


}
