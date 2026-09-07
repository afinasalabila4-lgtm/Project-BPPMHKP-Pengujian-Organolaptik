<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentTemplate;
use App\Models\Product;


class AssessmentTemplateSeeder extends Seeder
{

    public function run(): void
    {

        // Ambil SEMUA produk yang belum memiliki template
        $productsWithoutTemplate = Product::whereDoesntHave('assessmentTemplates')->get();

        if ($productsWithoutTemplate->isEmpty()) {
            $this->command->info('Semua produk sudah memiliki template. Skip.');
            return;
        }

        foreach ($productsWithoutTemplate as $product) {
            AssessmentTemplate::create([
                'product_id' => $product->id,
                'nama_template' => 'Uji Organoleptik Produk ' . $product->nama_produk,
            ]);
        }

    }

}

