<?php

namespace Database\Seeders;

use Faker\Provider\Lorem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('id');
        $brandIds = DB::table('brands')->pluck('id');
        $products = DB::table('products')->select('id', 'track_qty')->get();

        // foreach ($categoryIds as $key => $categoryId) {
        //     $subCategoryIds = DB::table('sub_categories')->select('id')->where('category_id', $categoryId)->get()->pluck('id');
        //     $randomNumbers = $subCategoryIds->random(count($subCategoryIds));

        //     for ($i = 1; $i <= 100; $i++) {
        //         $name = fake()->unique()->name();
        //         DB::table('products')->insert([
        //             'title' => $name,
        //             'slug' => Str::slug($name),
        //             'price' => rand(10000, 20000),
        //             'compare_price' => rand(10000, 20000),
        //             'track_qty' => rand(1, 0),
        //             'category_id' => $categoryId,
        //             'sub_category_id' => $randomNumbers[rand(0, count($randomNumbers) - 1)],
        //             'brand_id' => rand(1, count($brandIds)),
        //             'created_at' => now(),
        //             'updated_at' => now()
        //         ]);
        //     }
        // }

        foreach ($products as $product) {

            $inventory = [
                'product_id' => $product->id,
                'sku' => 'SKU' . fake()->unique()->regexify('[A-Z0-9]{8}'),
                'barcode' => fake()->unique()->regexify('[A-Z0-9]{8}'),
                'created_at' => now(),
                'updated_at' => now()

            ];
            if ($product->track_qty) {
                $inventory['qty'] = rand(10, 100);
            }

            DB::table('inventories')->insert($inventory);

            DB::table('product_details')->insert([
                'product_id' => $product->id,
                'description' => "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?",
                'short_description' => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut, minima voluptates debitis, accusamus ea delectus, iusto fuga quaerat earum neque nam necessitatibus cumque tempore optio architecto accusantium excepturi ipsam ullam!",
                'shipping_returns' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex reiciendis, hic fugiat modi vel qui rem repellat corporis perspiciatis dolore? Esse odio laborum voluptas maxime quis porro? Fuga, laboriosam molestias.Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste architecto necessitatibus, ad error, fugit beatae quasi earum doloribus ipsa accusantium sequi eum ex similique corporis, illo laborum sunt possimus esse?",
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        
    }
}
