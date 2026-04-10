<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productIds = Product::query()->pluck('id')->all();

        if ($productIds === []) {
            return;
        }

        $reviews = [
            ['customer_name' => 'Mari', 'rating' => 5, 'comment' => 'Väga mugavad tooted ja kiire tarne.'],
            ['customer_name' => 'Andres', 'rating' => 4, 'comment' => 'Hea kvaliteet, hind võiks veidi soodsam olla.'],
            ['customer_name' => 'Helen', 'rating' => 5, 'comment' => 'Seljakott üllatas positiivselt, väga praktiline.'],
            ['customer_name' => 'Rando', 'rating' => 4, 'comment' => 'Matkakepid toimivad hästi ka keerulisel rajal.'],
            ['customer_name' => 'Triin', 'rating' => 5, 'comment' => 'Olen ostuga rahul, kasutan varustust iga nädal.'],
        ];

        foreach ($reviews as $index => $review) {
            Review::updateOrCreate(
                [
                    'product_id' => $productIds[$index % count($productIds)],
                    'customer_name' => $review['customer_name'],
                ],
                [
                    ...$review,
                    'product_id' => $productIds[$index % count($productIds)],
                ]
            );
        }
    }
}
