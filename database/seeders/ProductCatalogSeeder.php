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
                'popis' => 'Hard enamel bunny pin with polished metal edges and a secure rubber clutch. Great for jackets, tote bags, and pencil cases.',
                'zakladna_cena' => 4.50,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-1.png',
                'skladom' => 100,
            ],
            [
                'nazov' => 'Cute cat stickers',
                'popis' => 'Set of waterproof vinyl cat stickers. Scratch-resistant finish, easy peel backing, and ideal for laptops, notebooks, and phone cases.',
                'zakladna_cena' => 3.90,
                'kategoriaId' => $stickerCategoryId,
                'image' => 'images/Products/prod-img-2.png',
                'skladom' => 100,
            ],
            [
                'nazov' => 'Fish patches',
                'popis' => 'Embroidered fish patch with merrow border. Can be sewn or ironed on denim, backpacks, and caps.',
                'zakladna_cena' => 7.00,
                'kategoriaId' => $patchesCategoryId,
                'image' => 'images/Products/prod-img-3.png',
                'skladom' => 0,
            ],
            [
                'nazov' => 'Game over pin',
                'popis' => 'Retro arcade-inspired enamel pin. Lightweight, durable, and perfect for gaming-themed outfits and accessories.',
                'zakladna_cena' => 4.50,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-4.png',
                'skladom' => 0,
            ],
            [
                'nazov' => 'Possum sticker pack',
                'popis' => 'Pack of playful possum vinyl stickers in multiple poses. Matte laminate protects against light scratches and water splashes.',
                'zakladna_cena' => 4.20,
                'kategoriaId' => $stickerCategoryId,
                'image' => 'images/Products/prod-img-5.png',
                'skladom' => 50,
            ],
            [
                'nazov' => 'Bite Risk patch',
                'popis' => 'Warning-style embroidered patch with bold red triangle design. Durable threadwork and strong edge stitching for daily wear.',
                'zakladna_cena' => 6.80,
                'kategoriaId' => $patchesCategoryId,
                'image' => 'images/Products/prod-img-6.png',
                'skladom' => 30,
            ],
            [
                'nazov' => 'Do Every Stupid Thing pin',
                'popis' => 'Glossy metal pin with hand-drawn match and ribbon. Weather-resistant, lightweight and durable, perfect for any free spirits.',
                'zakladna_cena' => 4.70,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-7.png',
                'skladom' => 0,
            ],
            [
                'nazov' => 'Round seal plushie',
                'popis' => 'Ultra-soft round seal plushie with a squishy filling and velvety surface. Great as a desk buddy, nap pillow, or cozy room decor piece.',
                'zakladna_cena' => 24.90,
                'kategoriaId' => $plushCategoryId,
                'image' => 'images/Products/prod-img-8.png',
                'skladom' => 18,
            ],
            [
                'nazov' => 'HeLpMe sticker',
                'popis' => 'Colorful Adobe-style text sticker with a clean white contour cut. Printed on durable vinyl with weather-resistant laminate.',
                'zakladna_cena' => 2.60,
                'kategoriaId' => $stickerCategoryId,
                'image' => 'images/Products/prod-img-9.png',
                'skladom' => 69,
            ],
            [
                'nazov' => 'I tried it at home patch',
                'popis' => 'Bold embroidered patch with high-contrast red, black, and white artwork. Great statement piece for jackets, backpacks, and utility pouches.',
                'zakladna_cena' => 8.20,
                'kategoriaId' => $patchesCategoryId,
                'image' => 'images/Products/prod-img-10.png',
                'skladom' => 26,
            ],
            [
                'nazov' => 'Shark Kitty plush',
                'popis' => 'Premium plush featuring a cat in a shark hoodie. Super-soft fur, detailed embroidery, and a huggable size perfect for shelf display or bedtime cuddles.',
                'zakladna_cena' => 15.50,
                'kategoriaId' => $plushCategoryId,
                'image' => 'images/Products/prod-img-11.png',
                'skladom' => 0,
            ],
            [
                'nazov' => 'Warp Grid pin',
                'popis' => 'Minimal geometric enamel pin inspired by wireframe warp grids. Clean black-and-white design with polished metal border for a modern look.',
                'zakladna_cena' => 5.90,
                'kategoriaId' => $pinCategoryId,
                'image' => 'images/Products/prod-img-12.png',
                'skladom' => 35,
            ],
            [
                'nazov' => 'Dinosaur plushie',
                'popis' => 'Soft long-neck dinosaur plushie with weighted feet for stable display. Cozy velour texture and warm earth-tone spikes make it perfect for gifting.',
                'zakladna_cena' => 19.90,
                'kategoriaId' => $plushCategoryId,
                'image' => 'images/Products/prod-img-13.png',
                'skladom' => 16,
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
