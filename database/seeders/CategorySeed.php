<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Konsumsi', 'Pembersih'];
        $data = [];
        foreach ($name as $value) {
            $data[] = [
                'name' => $value,
                'label' => strtoupper($value),
                'created_at' => now()
            ];
        }

        Category::insert($data);
    }
}
