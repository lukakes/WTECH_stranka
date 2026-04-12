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

        $products = [
            [
                'nazov' => 'Jotaro sticker',
                'popis' => 'Durable anime-themed sticker.',
                'zakladna_cena' => 3.90,
                'kategoriaId' => $stickerCategoryId,
                'image' => 'images/Products/prod-img-1.png',
            ],
            [
                'nazov' => 'Pikachu pin',
                'popis' => 'Collectible enamel pin.',
                'zakladna_cena' => 4.90,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-2.png',
            ],
            [
                'nazov' => 'Cat plush',
                'popis' => 'Soft plush companion.',
                'zakladna_cena' => 14.90,
                'kategoriaId' => $plushCategoryId,
                'image' => 'images/Products/prod-img-3.png',
            ],
            [
                'nazov' => 'Game over pin',
                'popis' => 'Retro gaming enamel pin.',
                'zakladna_cena' => 3.90,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-4.png',
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
                    'skladom' => 100,
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
