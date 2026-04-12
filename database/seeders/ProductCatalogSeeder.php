<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $stickerCategoryId = $this->firstOrCreateCategory('Stickers');
        $pinCategoryId = $this->firstOrCreateCategory('Pins');
        $plushCategoryId = $this->firstOrCreateCategory('Plushies');
        $patchesCategoryId = $this->firstOrCreateCategory('Patches');

        $products = [
            [
                'nazov' => 'Bunny pin',
                'popis' => 'Durable enamel bunny pin.',
                'zakladna_cena' => 4.50,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-1.png',
                'skladom' => 100,
            ],
            [
                'nazov' => 'Cute cat stickers',
                'popis' => 'Collectible cute cat stickers.',
                'zakladna_cena' => 3.90,
                'kategoriaId' => $stickerCategoryId,
                'image' => 'images/Products/prod-img-2.png',
                'skladom' => 100,
            ],
            [
                'nazov' => 'Fish patches',
                'popis' => 'Dead fish sewable patches.',
                'zakladna_cena' => 7.00,
                'kategoriaId' => $patchesCategoryId,
                'image' => 'images/Products/prod-img-3.png',
                'skladom' => 0,
            ],
            [
                'nazov' => 'Game over pin',
                'popis' => 'Retro gaming enamel pin.',
                'zakladna_cena' => 4.50,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-4.png',
                'skladom' => 0,
            ],
        ];

        foreach ($products as $productData) {
            $existingProduct = DB::table('Produkt')
                ->where('nazov', $productData['nazov'])
                ->first();

            if ($existingProduct) {
                $productId = (int) $existingProduct->id;

                DB::table('Produkt')
                    ->where('id', $productId)
                    ->update([
                        'popis' => $productData['popis'],
                        'zakladna_cena' => $productData['zakladna_cena'],
                        'kategoriaId' => $productData['kategoriaId'],
                        'aktivny' => true,
                    ]);
            } else {
                $productId = DB::table('Produkt')->insertGetId([
                    'nazov' => $productData['nazov'],
                    'popis' => $productData['popis'],
                    'zakladna_cena' => $productData['zakladna_cena'],
                    'kategoriaId' => $productData['kategoriaId'],
                    'aktivny' => true,
                    'created_at' => now(),
                ]);
            }

            DB::table('VariantProduktu')->updateOrInsert(
                [
                    'produktId' => $productId,
                    'nazov' => 'Default',
                ],
                [
                    'cena' => $productData['zakladna_cena'],
                    'skladom' => $productData['skladom'] ?? 100,
                    'aktivny' => true,
                ]
            );

            DB::table('ProduktovyObrazok')->updateOrInsert(
                [
                    'produktId' => $productId,
                    'poradie' => 1,
                ],
                [
                    'url' => $productData['image'],
                ]
            );
        }
    }

    private function firstOrCreateCategory(string $name): int
    {
        $existing = DB::table('Kategoria')
            ->where('nazov', $name)
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return DB::table('Kategoria')->insertGetId([
            'nazov' => $name,
            'parentId' => null,
        ]);
    }
}
