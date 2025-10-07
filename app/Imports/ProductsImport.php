<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public $errors = [];

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->errors[] = "الملف فارغ.";
            return;
        }

        // نأخذ أول صف للتحقق من أسماء الأعمدة
        $firstRow = $rows->first()->toArray();

        // الأعمدة المطلوبة
        $requiredColumns = ['barcode', 'name', 'price_sell', 'unit_price'];

        // تحقق من الأعمدة
        foreach ($requiredColumns as $column) {
            if (!array_key_exists($column, $firstRow)) {
                $this->errors[] = "العمود '$column' مفقود أو اسمه خاطئ في ملف Excel.";
            }
        }

        // إذا هناك أعمدة مفقودة، نوقف الاستيراد
        if (!empty($this->errors)) {
            return;
        }

        // استيراد البيانات
        foreach ($rows as $index => $row) {
            // تخطي الصف إذا فارغ الباركود أو الاسم
            if (empty($row['barcode']) || empty($row['name'])) {
                $this->errors[] = "الصف " . ($index + 2) . ": الباركود أو اسم المنتج فارغ.";
                continue;
            }

            $product = Product::where('barcode', $row['barcode'])->first();

            $data = [
                'name'                => $row['name'],
                'manufacture_company' => $row['manufacture_company'] ?? null,
                'price_sell'          => $row['price_sell'] ?? 0,
                'unit_price'          => $row['unit_price'] ?? 0,
            ];

            if ($product) {
                $product->update($data);
            } else {
                Product::create(array_merge($data, ['barcode' => $row['barcode']]));
            }
        }
    }
}
