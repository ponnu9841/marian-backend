<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('id');

        foreach ($categoryIds as $categoryId) {
            for ($i = 1; $i <= 5; $i++) {
                $name = fake()->unique()->name();
                DB::table('sub_categories')->insert([
                    'category_id' => $categoryId,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'status' => rand(true, false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
