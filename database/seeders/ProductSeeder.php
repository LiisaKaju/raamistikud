<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Matkasaapad', 'description' => 'Veekindlad ja hingavad saapad pikkadeks matkadeks.', 'price' => 129.00, 'sku' => 'MTK-BOO-001', 'stock_quantity' => 24],
            ['name' => 'Seljakott 35L', 'description' => 'Ergonoomiline päevamatka seljakott vihmakattega.', 'price' => 89.00, 'sku' => 'MTK-BAG-002', 'stock_quantity' => 30],
            ['name' => 'Matkakepid', 'description' => 'Reguleeritavad alumiiniumist matkakepid paarina.', 'price' => 39.90, 'sku' => 'MTK-POL-003', 'stock_quantity' => 45],
            ['name' => 'Termos 1L', 'description' => 'Roostevabast terasest termos, hoiab joogi kaua soojana.', 'price' => 24.90, 'sku' => 'MTK-THM-004', 'stock_quantity' => 50],
            ['name' => 'Pealamp', 'description' => 'USB-laetav LED pealamp 350 lm, mitme valgusreziimiga.', 'price' => 29.90, 'sku' => 'MTK-LMP-005', 'stock_quantity' => 40],
            ['name' => 'Matkamatt', 'description' => 'Kerge ja kokkupandav matkamatt telkimiseks.', 'price' => 34.00, 'sku' => 'MTK-MAT-006', 'stock_quantity' => 28],
            ['name' => 'Vihmakeep', 'description' => 'Kompaktne ja kerge vihmakeep ootamatuks sajuks.', 'price' => 19.90, 'sku' => 'MTK-RAI-007', 'stock_quantity' => 65],
            ['name' => 'Veefilter pudelile', 'description' => 'Kaasaskantav filter matkapudelile puhta vee jaoks.', 'price' => 44.00, 'sku' => 'MTK-FLT-008', 'stock_quantity' => 20],
            ['name' => 'Esmaabikomplekt', 'description' => 'Kerge matkale sobiv esmaabikomplekt.', 'price' => 17.50, 'sku' => 'MTK-FST-009', 'stock_quantity' => 55],
            ['name' => 'Meriinovillased matkasokid', 'description' => 'Soojad ja kiiresti kuivavad sokid pikaks rajapäevaks.', 'price' => 14.90, 'sku' => 'MTK-SOC-010', 'stock_quantity' => 80],
            ['name' => 'Tulekivi komplekt', 'description' => 'Vastupidav tulealustuskomplekt ellujaamisvarustuseks.', 'price' => 12.90, 'sku' => 'MTK-FIR-011', 'stock_quantity' => 35],
            ['name' => 'Matkapudel 750ml', 'description' => 'Kerge BPA-vaba joogipudel igapäevaseks matkaks.', 'price' => 11.90, 'sku' => 'MTK-BTL-012', 'stock_quantity' => 70],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }

        Product::whereNotIn('sku', collect($products)->pluck('sku'))->delete();
    }
}
