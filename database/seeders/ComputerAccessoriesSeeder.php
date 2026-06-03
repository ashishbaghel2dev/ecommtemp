<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductLabel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComputerAccessoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect($this->categories())->mapWithKeys(function (array $data, int $index) {
            $category = Category::withTrashed()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'image' => $data['image'],
                    'banner' => $data['image'],
                    'show_on_home' => true,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'meta_title' => $data['name'] . ' Online',
                    'meta_description' => $data['description'],
                    'deleted_at' => null,
                ]
            );

            return [$data['slug'] => $category];
        });

        $attributes = $this->seedAttributes($categories);
        $labels = $this->seedLabels();

        foreach ($this->products() as $data) {
            $category = $categories[$data['category']];

            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'],
                    'sale_price' => null,
                    'sale_start' => null,
                    'sale_end' => null,
                    'stock' => $data['stock'],
                    'manage_stock' => true,
                    'in_stock' => true,
                    'image' => $data['image'],
                    'is_featured' => $data['featured'],
                    'is_active' => true,
                    'meta_title' => $data['name'],
                    'meta_description' => $data['short_description'],
                    'type' => 'simple',
                    'view_count' => rand(12, 220),
                ]
            );

            $product->images()->updateOrCreate(
                ['image' => $data['image']],
                ['is_main' => true, 'sort_order' => 1]
            );

            $this->seedProductAttributes($product, $category, $attributes, $data);
            $product->labels()->syncWithoutDetaching(
                collect($data['labels'])->map(fn ($slug) => $labels[$slug]->id)->all()
            );
        }

        $this->seedReviews();
    }

    private function seedLabels(): array
    {
        return [
            'new-arrived' => ProductLabel::updateOrCreate(
                ['slug' => 'new-arrived'],
                ['name' => 'New Arrived', 'color' => '#0f766e', 'is_active' => true]
            ),
            'best-product' => ProductLabel::updateOrCreate(
                ['slug' => 'best-product'],
                ['name' => 'Best Product', 'color' => '#dc2626', 'is_active' => true]
            ),
            'hot-deal' => ProductLabel::updateOrCreate(
                ['slug' => 'hot-deal'],
                ['name' => 'Hot Deal', 'color' => '#ea580c', 'is_active' => true]
            ),
        ];
    }

    private function seedAttributes($categories): array
    {
        $result = [];

        foreach ($categories as $slug => $category) {
            foreach (['Brand', 'Warranty', 'Compatibility'] as $index => $name) {
                $attribute = Attribute::updateOrCreate(
                    ['code' => $slug . '-' . Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'type' => 'select',
                        'is_required' => false,
                        'is_filterable' => true,
                        'is_active' => true,
                    ]
                );

                $result[$category->id][$name] = $attribute;
            }
        }

        return $result;
    }

    private function seedProductAttributes(Product $product, Category $category, array $attributes, array $data): void
    {
        foreach (['Brand' => $data['brand'], 'Warranty' => $data['warranty'], 'Compatibility' => $data['compatibility']] as $name => $value) {
            $attribute = $attributes[$category->id][$name];
            $attributeValue = AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                ],
                [
                    'slug' => Str::slug($value),
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            $product->attributeValues()->updateOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'attribute_value_id' => $attributeValue->id,
                ],
                ['value' => null]
            );
        }
    }

    private function seedReviews(): void
    {
        $customers = collect([
            ['name' => 'Rahul Sharma', 'email' => 'rahul.demo@example.com'],
            ['name' => 'Priya Mehta', 'email' => 'priya.demo@example.com'],
            ['name' => 'Aman Verma', 'email' => 'aman.demo@example.com'],
            ['name' => 'Neha Gupta', 'email' => 'neha.demo@example.com'],
            ['name' => 'Vikram Singh', 'email' => 'vikram.demo@example.com'],
            ['name' => 'Anjali Rao', 'email' => 'anjali.demo@example.com'],
        ])->map(fn ($customer) => User::updateOrCreate(
            ['email' => $customer['email']],
            [
                'name' => $customer['name'],
                'password' => Hash::make('password'),
                'status' => true,
                'email_verified_at' => now(),
            ]
        ))->values();

        $reviews = [
            ['sku' => 'CPU-COOL-RGB-001', 'user' => 0, 'rating' => 5, 'title' => 'Cooling performance is solid', 'comment' => 'Installed this tower fan in my gaming PC and CPU temperature dropped nicely. RGB also looks clean inside the cabinet.'],
            ['sku' => 'CAB-HDMI-2M', 'user' => 1, 'rating' => 5, 'title' => 'Good cable for monitor setup', 'comment' => 'The HDMI cable feels durable and worked perfectly with my office monitor. Picture quality is clear with no flicker.'],
            ['sku' => 'KEY-WIRELESS-SLIM', 'user' => 2, 'rating' => 4, 'title' => 'Comfortable daily keyboard', 'comment' => 'Keys are quiet and the slim size saves desk space. Good choice for home office typing.'],
            ['sku' => 'MOU-RGB-GAME', 'user' => 3, 'rating' => 5, 'title' => 'Smooth tracking', 'comment' => 'The mouse has a nice grip and the DPI control is useful for gaming as well as normal work.'],
            ['sku' => 'NET-WIFI-1200', 'user' => 4, 'rating' => 5, 'title' => 'WiFi signal improved', 'comment' => 'My desktop now gets stable internet without long LAN wiring. Setup was simple and speed is much better.'],
            ['sku' => 'NET-BT-50', 'user' => 5, 'rating' => 4, 'title' => 'Useful tiny Bluetooth adapter', 'comment' => 'Connected my wireless headphones and keyboard without issue. Small adapter and easy to carry.'],
        ];

        foreach ($reviews as $review) {
            $product = Product::where('sku', $review['sku'])->first();
            $user = $customers[$review['user']] ?? $customers->first();

            if (!$product || !$user) {
                continue;
            }

            Review::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                ],
                [
                    'title' => $review['title'],
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                    'is_verified_purchase' => true,
                    'helpful_votes' => rand(3, 22),
                    'unhelpful_votes' => 0,
                    'status' => 'approved',
                ]
            );
        }
    }

    private function categories(): array
    {
        return [
            [
                'name' => 'CPU Accessories',
                'slug' => 'cpu-accessories',
                'description' => 'Cooling fans, thermal paste, heatsinks and processor care accessories.',
                'image' => 'products/images/cpu-accessories.svg',
            ],
            [
                'name' => 'Cabinets',
                'slug' => 'cabinets',
                'description' => 'Computer cabinets and cases for gaming, office and compact builds.',
                'image' => 'products/images/cabinets.svg',
            ],
            [
                'name' => 'Wires & Cables',
                'slug' => 'wires-cables',
                'description' => 'HDMI, USB, LAN, SATA and power cables for everyday setup needs.',
                'image' => 'products/images/wires-cables.svg',
            ],
            [
                'name' => 'Keyboards',
                'slug' => 'keyboards',
                'description' => 'Wired, wireless and mechanical keyboards for work and gaming.',
                'image' => 'products/images/keyboards.svg',
            ],
            [
                'name' => 'Mice',
                'slug' => 'mice',
                'description' => 'Ergonomic, wireless and gaming mice with smooth tracking.',
                'image' => 'products/images/mice.svg',
            ],
            [
                'name' => 'Network Adapters',
                'slug' => 'network-adapters',
                'description' => 'WiFi, Bluetooth and network adapters for laptop and desktop PCs.',
                'image' => 'products/images/network-adapters.svg',
            ],
            [
                'name' => 'Storage Accessories',
                'slug' => 'storage-accessories',
                'description' => 'SSD enclosures, card readers and drive accessories.',
                'image' => 'products/images/storage-accessories.svg',
            ],
            [
                'name' => 'Power & Chargers',
                'slug' => 'power-chargers',
                'description' => 'Power strips, adapters, laptop chargers and surge protectors.',
                'image' => 'products/images/power-chargers.svg',
            ],
        ];
    }

    private function products(): array
    {
        return [
            $this->product('CPU Cooler RGB Tower Fan', 'cpu-accessories', 'CPU-COOL-RGB-001', 2499, 1999, 32, true, 'CoolTech', '1 year', 'Intel and AMD sockets', 'Dual heat-pipe RGB tower cooler for stable processor temperatures.', ['best-product', 'hot-deal']),
            $this->product('Premium Thermal Paste 4g', 'cpu-accessories', 'CPU-PASTE-004G', 399, 299, 120, true, 'ThermoPro', '6 months', 'All processors', 'High conductivity thermal compound for CPU and GPU heatsinks.', ['new-arrived']),
            $this->product('CPU Fan 120mm Silent', 'cpu-accessories', 'CPU-FAN-120-SIL', 699, 549, 75, false, 'AirFlux', '1 year', 'Standard 120mm mounts', 'Quiet 120mm cooling fan with strong airflow and low vibration.', ['new-arrived']),
            $this->product('Anti Static Cleaning Brush Kit', 'cpu-accessories', 'CPU-BRUSH-KIT', 349, 249, 90, false, 'CleanMate', 'No warranty', 'PCBs and cabinets', 'Soft anti-static brushes for cleaning dust around components.', ['hot-deal']),

            $this->product('Mid Tower Gaming Cabinet', 'cabinets', 'CAB-MID-GAME-01', 3999, 3299, 18, true, 'BuildX', '1 year', 'ATX, Micro ATX, Mini ITX', 'Tempered glass cabinet with mesh front and good cable routing.', ['best-product']),
            $this->product('Compact Office CPU Cabinet', 'cabinets', 'CAB-OFFICE-MATX', 2499, 2099, 24, false, 'CaseLine', '1 year', 'Micro ATX and Mini ITX', 'Space-saving cabinet for office desktop builds.', ['new-arrived']),
            $this->product('High Airflow Mesh Cabinet', 'cabinets', 'CAB-AIR-MESH', 4599, 3899, 12, true, 'AirBox', '1 year', 'ATX builds', 'Mesh front case with fan mounts for performance builds.', ['best-product', 'hot-deal']),

            $this->product('HDMI Cable 2 Meter', 'wires-cables', 'CAB-HDMI-2M', 299, 199, 200, true, 'WireHub', '6 months', 'TV, monitor and laptop', 'Durable HDMI cable for monitor and display connections.', ['best-product']),
            $this->product('USB Type C Fast Cable', 'wires-cables', 'CAB-USBC-FAST', 399, 249, 180, false, 'WireHub', '6 months', 'USB-C devices', 'Braided Type C cable for charging and data transfer.', ['new-arrived']),
            $this->product('SATA Data Cable Pack of 2', 'wires-cables', 'CAB-SATA-P2', 199, 149, 160, false, 'DataLink', 'No warranty', 'SATA HDD and SSD', 'Reliable SATA cables for connecting internal drives.', ['hot-deal']),
            $this->product('CAT6 LAN Cable 5 Meter', 'wires-cables', 'CAB-CAT6-5M', 449, 349, 110, true, 'NetCord', '6 months', 'Routers, PCs and switches', 'High speed Ethernet cable for stable networking.', ['best-product']),

            $this->product('Wireless Keyboard Slim', 'keyboards', 'KEY-WIRELESS-SLIM', 1499, 1199, 45, true, 'KeyPro', '1 year', 'Windows, macOS and Linux', 'Slim wireless keyboard with quiet keys and compact layout.', ['new-arrived', 'best-product']),
            $this->product('Mechanical Keyboard Blue Switch', 'keyboards', 'KEY-MECH-BLUE', 3499, 2899, 20, true, 'GameType', '1 year', 'USB desktops and laptops', 'Clicky mechanical keyboard with backlight for gaming and typing.', ['best-product']),
            $this->product('USB Office Keyboard', 'keyboards', 'KEY-USB-OFFICE', 799, 599, 80, false, 'OfficeMate', '1 year', 'USB PCs and laptops', 'Full-size wired keyboard for daily office use.', ['new-arrived']),

            $this->product('Wireless Mouse 2.4GHz', 'mice', 'MOU-WIRELESS-24G', 899, 699, 70, true, 'ClickPro', '1 year', 'Windows, macOS and Linux', 'Comfortable wireless mouse with USB receiver.', ['new-arrived']),
            $this->product('RGB Gaming Mouse', 'mice', 'MOU-RGB-GAME', 1499, 1099, 38, true, 'GameClick', '1 year', 'USB desktops and laptops', 'Gaming mouse with RGB lighting and adjustable DPI.', ['best-product', 'hot-deal']),
            $this->product('Ergonomic Vertical Mouse', 'mice', 'MOU-ERG-VERT', 1799, 1399, 25, false, 'ErgoEase', '1 year', 'USB and wireless setups', 'Vertical mouse designed to reduce wrist strain.', ['new-arrived']),

            $this->product('USB WiFi Adapter 300Mbps', 'network-adapters', 'NET-WIFI-300', 799, 599, 95, true, 'NetMate', '1 year', 'Windows and Linux', 'Compact USB WiFi adapter for desktop and laptop connectivity.', ['best-product']),
            $this->product('Dual Band WiFi Adapter 1200Mbps', 'network-adapters', 'NET-WIFI-1200', 1699, 1299, 50, true, 'NetMate', '1 year', 'Windows, macOS and Linux', 'Dual band WiFi adapter with strong signal reception.', ['new-arrived', 'best-product']),
            $this->product('Bluetooth 5.0 USB Adapter', 'network-adapters', 'NET-BT-50', 699, 499, 130, true, 'BlueLink', '1 year', 'Windows PCs', 'Tiny Bluetooth adapter for speakers, keyboard, mouse and headphones.', ['new-arrived']),
            $this->product('USB Ethernet Adapter', 'network-adapters', 'NET-USB-LAN', 999, 749, 60, false, 'DataLink', '1 year', 'USB laptops and desktops', 'USB to LAN adapter for stable wired internet.', ['hot-deal']),

            $this->product('SSD Enclosure USB 3.0', 'storage-accessories', 'STO-SSD-ENC-30', 1299, 999, 42, true, 'StoreBox', '1 year', '2.5 inch SATA SSD and HDD', 'Portable enclosure for 2.5 inch SATA drives.', ['best-product']),
            $this->product('Memory Card Reader 4 in 1', 'storage-accessories', 'STO-CARD-4IN1', 499, 349, 85, false, 'ReadPro', '6 months', 'SD, microSD and USB devices', 'Compact multi-card reader for quick file transfers.', ['new-arrived']),

            $this->product('Surge Protector Power Strip', 'power-chargers', 'PWR-SURGE-4PLUG', 1299, 949, 35, true, 'PowerSafe', '1 year', 'PCs, monitors and printers', 'Four socket power strip with surge protection.', ['best-product', 'hot-deal']),
            $this->product('Universal Laptop Charger 65W', 'power-chargers', 'PWR-LAP-65W', 1999, 1599, 28, false, 'ChargeMax', '1 year', 'Most 65W laptops', 'Universal laptop adapter with multiple connector pins.', ['new-arrived']),
        ];
    }

    private function product(string $name, string $category, string $sku, int $price, int $discountPrice, int $stock, bool $featured, string $brand, string $warranty, string $compatibility, string $shortDescription, array $labels): array
    {
        return [
            'name' => $name,
            'category' => $category,
            'sku' => $sku,
            'slug' => Str::slug($name),
            'price' => $price,
            'discount_price' => $discountPrice,
            'stock' => $stock,
            'featured' => $featured,
            'brand' => $brand,
            'warranty' => $warranty,
            'compatibility' => $compatibility,
            'short_description' => $shortDescription,
            'description' => $shortDescription . ' This is demo catalog data for a computer accessories online shop.',
            'image' => 'products/images/' . $category . '.svg',
            'labels' => $labels,
        ];
    }
}
