<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PosBillDetails;

class FillCostPriceAndProfit extends Command
{
    protected $signature = 'pos:fill-cost-profit';
    protected $description = 'Fill cost_price and profit for old pos_bill_details records';

    public function handle()
    {
        $this->info('🚀 جاري تحديث بيانات pos_bill_details ...');

        $details = PosBillDetails::with('product')->get();

        foreach ($details as $detail) {
            $costPrice = $detail->product->unit_price ?? 0;
            $profit = ($detail->unit_price - $costPrice) * $detail->quantity;

            $detail->update([
                'cost_price' => $costPrice,
                'profit' => $profit,
            ]);
        }

        $this->info('✅ تم تحديث جميع السجلات بنجاح!');
        return 0;
    }
}
