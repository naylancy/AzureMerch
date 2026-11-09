<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\Hash;

class AzureMerchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Akses laporan analitik, audit log, dan performa finansial.'],
            ['name' => 'kasir', 'display_name' => 'Kasir (Cashier)', 'description' => 'Akses POS Point of Sale kasir penjualan retail, scan barcode & cetak struk.'],
            ['name' => 'gudang', 'display_name' => 'Staf Gudang (Warehouse)', 'description' => 'Akses CRUD produk, manajemen stok masuk (inbound), dan penyesuaian opname.'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // 2. Seed Users
        $users = [
            [
                'name' => 'Min Yoongi (Admin)',
                'username' => 'admin',
                'email' => 'admin@azuremerch.com',
                'role' => 'admin',
                'phone' => '0812-3456-7890',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Kim Jennie (Kasir)',
                'username' => 'kasir',
                'email' => 'kasir@azuremerch.com',
                'role' => 'kasir',
                'phone' => '0813-9876-5432',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Bang Chan (Gudang)',
                'username' => 'gudang',
                'email' => 'gudang@azuremerch.com',
                'role' => 'gudang',
                'phone' => '0811-2233-4455',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['username' => $u['username']], $u);
        }

        $warehouseUser = User::where('role', 'gudang')->first();

        // 3. Seed Categories
        $categoriesData = [
            [
                'name' => 'Albums',
                'slug' => 'albums',
                'code' => 'ALB',
                'description' => 'Official K-pop music albums, digipacks, vinyls, and jewel case editions.',
                'icon' => 'disc',
            ],
            [
                'name' => 'Photocards',
                'slug' => 'photocards',
                'code' => 'PC',
                'description' => 'Rare collectible photocards, lucky draws, soundwave, and pre-order benefits (POB).',
                'icon' => 'image',
            ],
            [
                'name' => 'Dolls & Plushies',
                'slug' => 'dolls-plushies',
                'code' => 'DLL',
                'description' => 'Original idol character plush dolls (BT21, SKZOO, TRUZ, Lovelys).',
                'icon' => 'smile',
            ],
            [
                'name' => 'Keychains & Charms',
                'slug' => 'keychains-charms',
                'code' => 'KCH',
                'description' => 'Acrylic charms, lightstick strap keychains, toploaders, and bag pendants.',
                'icon' => 'key',
            ],
            [
                'name' => 'Apparel',
                'slug' => 'apparel',
                'code' => 'APP',
                'description' => 'Concert tour merchandise t-shirts, oversized hoodies, jerseys, and caps.',
                'icon' => 'shirt',
            ],
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'code' => 'FTW',
                'description' => 'Official collaboration sneakers, canvas shoes, and idol-inspired street footwear.',
                'icon' => 'footprints',
            ],
            [
                'name' => 'Collectibles & Lightsticks',
                'slug' => 'collectibles',
                'code' => 'COL',
                'description' => 'Official concert lightsticks, season greetings, trading card binders, and banners.',
                'icon' => 'zap',
            ],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $c) {
            $cat = Category::firstOrCreate(['slug' => $c['slug']], $c);
            $categoryMap[$c['code']] = $cat->id;
        }

        // 4. Seed Products
        $productsData = [
            // Albums
            [
                'category_id' => $categoryMap['ALB'],
                'sku' => 'ALB-BTS-001',
                'name' => 'BTS - Proof (Standard Edition 3-CD Set)',
                'barcode' => '8809755508891',
                'cost_price' => 750000,
                'selling_price' => 950000,
                'stock' => 24,
                'min_stock_alert' => 5,
                'group_artist' => 'BTS',
                'description' => 'Official anthology album includes 3 CDs, the art of Proof booklet, photograph booklet, epilogue, and photocard set.',
                'image_url' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['ALB'],
                'sku' => 'ALB-BP-002',
                'name' => 'BLACKPINK - 2nd Album BORN PINK (Box Set Gray Ver.)',
                'barcode' => '8809848758837',
                'cost_price' => 320000,
                'selling_price' => 450000,
                'stock' => 18,
                'min_stock_alert' => 5,
                'group_artist' => 'BLACKPINK',
                'description' => 'Full-length album with 80p photobook, accordion lyrics paper, random photocard, and postcard.',
                'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['ALB'],
                'sku' => 'ALB-SKZ-003',
                'name' => 'Stray Kids - 5-STAR (3rd Full Album Limited Edition)',
                'barcode' => '8809755509201',
                'cost_price' => 300000,
                'selling_price' => 420000,
                'stock' => 30,
                'min_stock_alert' => 6,
                'group_artist' => 'Stray Kids',
                'description' => 'Features Title track S-Class with limited special mini poster and 4-cut film photocard.',
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['ALB'],
                'sku' => 'ALB-NWJ-004',
                'name' => "NewJeans - 2nd EP 'Get Up' Bunny Beach Bag Ver.",
                'barcode' => '8809755509423',
                'cost_price' => 340000,
                'selling_price' => 480000,
                'stock' => 15,
                'min_stock_alert' => 4,
                'group_artist' => 'NewJeans',
                'description' => 'Includes cute woven Beach Bag, 3 photobooks, lyric book, postcard set, and sticker pack.',
                'image_url' => 'https://images.unsplash.com/photo-1526478806334-5fd488fcaabc?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['ALB'],
                'sku' => 'ALB-SVT-005',
                'name' => "SEVENTEEN - 10th Mini Album 'FML' (Fallen, Misfit, Lost Ver.)",
                'barcode' => '8809755509119',
                'cost_price' => 280000,
                'selling_price' => 395000,
                'stock' => 22,
                'min_stock_alert' => 5,
                'group_artist' => 'SEVENTEEN',
                'description' => 'Features double title Super & F*ck My Life with full Carat photobook.',
                'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=500&q=80',
            ],

            // Photocards
            [
                'category_id' => $categoryMap['PC'],
                'sku' => 'PC-BTS-001',
                'name' => 'BTS - Jungkook Golden Lucky Draw Soundwave Holo PC',
                'barcode' => '8809000100011',
                'cost_price' => 150000,
                'selling_price' => 295000,
                'stock' => 12,
                'min_stock_alert' => 3,
                'group_artist' => 'BTS',
                'description' => 'Ultra-rare official soundwave lucky draw photocard featuring iridescent foil stamp.',
                'image_url' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['PC'],
                'sku' => 'PC-BP-002',
                'name' => 'BLACKPINK - Jennie Gentle Monster Limited Photocard',
                'barcode' => '8809000100028',
                'cost_price' => 180000,
                'selling_price' => 350000,
                'stock' => 7,
                'min_stock_alert' => 2,
                'group_artist' => 'BLACKPINK',
                'description' => 'Exclusive collaboration photocard featuring Jennie in Jentle Salon eyewear.',
                'image_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['PC'],
                'sku' => 'PC-SKZ-003',
                'name' => 'Stray Kids - Felix 5-STAR Target Exclusive POB PC',
                'barcode' => '8809000100035',
                'cost_price' => 110000,
                'selling_price' => 220000,
                'stock' => 14,
                'min_stock_alert' => 3,
                'group_artist' => 'Stray Kids',
                'description' => 'Official USA retail exclusive bonus photocard with glossy finish.',
                'image_url' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['PC'],
                'sku' => 'PC-NWJ-004',
                'name' => 'NewJeans - Haerin Bunnies Camp Fanmeeting Special PC',
                'barcode' => '8809000100042',
                'cost_price' => 95000,
                'selling_price' => 195000,
                'stock' => 9,
                'min_stock_alert' => 3,
                'group_artist' => 'NewJeans',
                'description' => 'Official Seoul Olympic Park Fanmeeting exclusive photocard with cat ear pose.',
                'image_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=500&q=80',
            ],

            // Dolls & Plushies
            [
                'category_id' => $categoryMap['DLL'],
                'sku' => 'DLL-BT21-001',
                'name' => 'BT21 - Cooky Standing Plush Doll 30cm (Official Line Friends)',
                'barcode' => '8809823401018',
                'cost_price' => 290000,
                'selling_price' => 420000,
                'stock' => 16,
                'min_stock_alert' => 4,
                'group_artist' => 'BTS',
                'description' => 'Authentic LINE FRIENDS BT21 Cooky pink rabbit standing plush with soft embroidery.',
                'image_url' => 'https://images.unsplash.com/photo-1559454403-b8fb88521f11?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['DLL'],
                'sku' => 'DLL-SKZ-002',
                'name' => 'SKZOO - BbokAri Original Plush Doll (Felix Character)',
                'barcode' => '8809823401025',
                'cost_price' => 320000,
                'selling_price' => 480000,
                'stock' => 11,
                'min_stock_alert' => 4,
                'group_artist' => 'Stray Kids',
                'description' => 'JYP Official MD cute yellow chick plush with removable hoodie outfit.',
                'image_url' => 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['DLL'],
                'sku' => 'DLL-SKZ-003',
                'name' => 'SKZOO - Wolf Chan Original Plush Doll (Bang Chan Character)',
                'barcode' => '8809823401032',
                'cost_price' => 320000,
                'selling_price' => 480000,
                'stock' => 13,
                'min_stock_alert' => 4,
                'group_artist' => 'Stray Kids',
                'description' => 'JYP Official MD wolf plush doll with signature jacket and tag.',
                'image_url' => 'https://images.unsplash.com/photo-1558877385-81a1c7e67d72?auto=format&fit=crop&w=500&q=80',
            ],

            // Keychains & Charms
            [
                'category_id' => $categoryMap['KCH'],
                'sku' => 'KCH-TW-001',
                'name' => 'TWICE - Lovelys Plush Keychain (Navely Sky Blue)',
                'barcode' => '8809600100014',
                'cost_price' => 85000,
                'selling_price' => 150000,
                'stock' => 25,
                'min_stock_alert' => 5,
                'group_artist' => 'TWICE',
                'description' => 'Mini plush keyring with sturdy lobster claw clasp, fits bag zipper or lightstick strap.',
                'image_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['KCH'],
                'sku' => 'KCH-COL-002',
                'name' => 'Azure Holographic Acrylic Photocard Holder Keyring',
                'barcode' => '8809600100021',
                'cost_price' => 45000,
                'selling_price' => 85000,
                'stock' => 45,
                'min_stock_alert' => 10,
                'group_artist' => 'Multi-Fandom',
                'description' => 'Premium UV-resistant hard acrylic toploader frame with silver chain and cute pastel charms.',
                'image_url' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=500&q=80',
            ],

            // Apparel
            [
                'category_id' => $categoryMap['APP'],
                'sku' => 'APP-BTS-001',
                'name' => 'BTS - Yet To Come in Busan Oversized Tour Hoodie (Navy, Size L)',
                'barcode' => '8809400200017',
                'cost_price' => 550000,
                'selling_price' => 890000,
                'stock' => 10,
                'min_stock_alert' => 3,
                'group_artist' => 'BTS',
                'description' => 'Heavyweight 400gsm cotton fleece with embroidered concert crest and kangaroo pocket.',
                'image_url' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['APP'],
                'sku' => 'APP-BP-002',
                'name' => 'BLACKPINK - Born Pink World Tour Vintage Wash T-Shirt (M)',
                'barcode' => '8809400200024',
                'cost_price' => 280000,
                'selling_price' => 460000,
                'stock' => 14,
                'min_stock_alert' => 4,
                'group_artist' => 'BLACKPINK',
                'description' => 'Acid-washed 100% cotton tee with distressed tour dates printed on the back.',
                'image_url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['APP'],
                'sku' => 'APP-NWJ-003',
                'name' => "NewJeans - 'Ditto' 90s Retro Track Raglan Tee (L)",
                'barcode' => '8809400200031',
                'cost_price' => 230000,
                'selling_price' => 380000,
                'stock' => 18,
                'min_stock_alert' => 5,
                'group_artist' => 'NewJeans',
                'description' => 'Y2K vintage aesthetic raglan sports jersey with ribbed neckline and breathable cotton.',
                'image_url' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=500&q=80',
            ],

            // Footwear
            [
                'category_id' => $categoryMap['FTW'],
                'sku' => 'FTW-BTS-001',
                'name' => 'BTS x PUMA - Turin Patent Leather K-Pop Sneakers (Size 41)',
                'barcode' => '8809300500012',
                'cost_price' => 850000,
                'selling_price' => 1350000,
                'stock' => 8,
                'min_stock_alert' => 2,
                'group_artist' => 'BTS',
                'description' => 'Limited edition collaborative sneaker with BTS debut date flower logo embroidered on heel.',
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['FTW'],
                'sku' => 'FTW-BP-002',
                'name' => 'BLACKPINK x Adidas - Superstar Floral Edition Canvas (Size 39)',
                'barcode' => '8809300500029',
                'cost_price' => 920000,
                'selling_price' => 1490000,
                'stock' => 6,
                'min_stock_alert' => 2,
                'group_artist' => 'BLACKPINK',
                'description' => 'Classic shell-toe low tops featuring subtle pastel pink accents and metallic lace jewels.',
                'image_url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=500&q=80',
            ],

            // Collectibles & Lightsticks
            [
                'category_id' => $categoryMap['COL'],
                'sku' => 'COL-NWJ-001',
                'name' => "NewJeans - Official Lightstick 'Binky Bong' (Bluetooth Enabled)",
                'barcode' => '8809700300015',
                'cost_price' => 620000,
                'selling_price' => 850000,
                'stock' => 20,
                'min_stock_alert' => 5,
                'group_artist' => 'NewJeans',
                'description' => 'Iconic rabbit-shaped lightstick with interchangeable bunny eye parts and concert sync.',
                'image_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['COL'],
                'sku' => 'COL-BTS-002',
                'name' => 'BTS - Official Light Stick Special Edition (ARMY Bomb Ver. 4)',
                'barcode' => '8809700300022',
                'cost_price' => 650000,
                'selling_price' => 890000,
                'stock' => 25,
                'min_stock_alert' => 5,
                'group_artist' => 'BTS',
                'description' => 'Equipped with Bluetooth 5.0 central control sync, strap, and 7 official member photocard set.',
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['COL'],
                'sku' => 'COL-SKZ-003',
                'name' => 'Stray Kids - Official Lightstick Nachimbong Ver. 2',
                'barcode' => '8809700300039',
                'cost_price' => 680000,
                'selling_price' => 920000,
                'stock' => 14,
                'min_stock_alert' => 4,
                'group_artist' => 'Stray Kids',
                'description' => 'Features rotating compass centerpiece, OLED screen with member custom graphics, and Bluetooth.',
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'category_id' => $categoryMap['COL'],
                'sku' => 'COL-BIN-004',
                'name' => 'Azure Premium K-Pop Photocard Collect Book (9-Pocket Holographic)',
                'barcode' => '8809700300046',
                'cost_price' => 110000,
                'selling_price' => 210000,
                'stock' => 35,
                'min_stock_alert' => 8,
                'group_artist' => 'Multi-Fandom',
                'description' => 'Acid-free, PVC-free archival sleeves. Holds up to 360 standard photocards safely.',
                'image_url' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=500&q=80',
            ],
        ];

        foreach ($productsData as $p) {
            $product = Product::firstOrCreate(['sku' => $p['sku']], $p);

            // Log initial stock as inbound
            StockLog::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'reference_number' => 'INIT-IMPORT-2026',
                ],
                [
                    'user_id' => $warehouseUser ? $warehouseUser->id : 1,
                    'type' => 'inbound',
                    'quantity_change' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'notes' => 'Penerimaan kontainer stok awal dari supplier Seoul, Korea Selatan (HYBE / YG Select / JYP MD).',
                ]
            );
        }
    }
}
