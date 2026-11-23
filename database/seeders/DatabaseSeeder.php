<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\Category;
use App\Models\Menu;
use App\Models\CafeTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\Receipt;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Staff
        $admin = Staff::create([
            'name' => 'Admin Brewly',
            'email' => 'admin@brewly.com',
            'password_hash' => Hash::make('password123'),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        $cashier = Staff::create([
            'name' => 'Siti Kasir',
            'email' => 'cashier@brewly.com',
            'password_hash' => Hash::make('password123'),
            'role' => 'CASHIER',
            'is_active' => true,
        ]);

        $kitchen = Staff::create([
            'name' => 'Chef Budi',
            'email' => 'kitchen@brewly.com',
            'password_hash' => Hash::make('password123'),
            'role' => 'KITCHEN',
            'is_active' => true,
        ]);

        // 2. Cafe Tables
        $tables = [
            ['table_number' => 'T-01', 'capacity' => 2, 'status' => 'AVAILABLE'],
            ['table_number' => 'T-02', 'capacity' => 2, 'status' => 'AVAILABLE'],
            ['table_number' => 'T-03', 'capacity' => 4, 'status' => 'OCCUPIED'],
            ['table_number' => 'T-04', 'capacity' => 4, 'status' => 'AVAILABLE'],
            ['table_number' => 'T-05', 'capacity' => 6, 'status' => 'OCCUPIED'],
            ['table_number' => 'T-06', 'capacity' => 2, 'status' => 'AVAILABLE'],
            ['table_number' => 'T-07', 'capacity' => 4, 'status' => 'AVAILABLE'],
            ['table_number' => 'T-08', 'capacity' => 8, 'status' => 'AVAILABLE'],
        ];

        foreach ($tables as $t) {
            CafeTable::create($t);
        }

        // 3. Categories
        $catCoffee = Category::create([
            'name' => 'Coffee & Espresso',
            'description' => 'Biji kopi pilihan Nusantara dan racikan barista berpengalaman.',
        ]);

        $catNonCoffee = Category::create([
            'name' => 'Non-Coffee & Artisan Tea',
            'description' => 'Minuman segar, matcha authentic, dan teh rempah hangat.',
        ]);

        $catPastry = Category::create([
            'name' => 'Bakery & Pastry',
            'description' => 'Freshly baked croissants, pastries, and artisanal cakes.',
        ]);

        $catMain = Category::create([
            'name' => 'Main Course',
            'description' => 'Makanan berat lezat untuk menemani waktu santai Anda.',
        ]);

        $catSnacks = Category::create([
            'name' => 'Snacks & Bites',
            'description' => 'Camilan renyah dan gurih pas untuk sharing.',
        ]);

        // 4. Menus
        $menus = [
            // Coffee
            [
                'category_id' => $catCoffee->category_id,
                'name' => 'Brewly Signature Aren Latte',
                'description' => 'Espresso ganda dipadu susu creamy dan gula aren murni aromatik.',
                'price' => 24000,
                'image_url' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catCoffee->category_id,
                'name' => 'Caramel Macchiato Ice',
                'description' => 'Espresso bold dengan vanilla syrup, susu segar, dan drizzle saus karamel.',
                'price' => 28000,
                'image_url' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catCoffee->category_id,
                'name' => 'Classic Americano (Hot/Ice)',
                'description' => 'Single origin Arabica espresso dicampur air mineral segar, clean dan fruity.',
                'price' => 19000,
                'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catCoffee->category_id,
                'name' => 'Spanish Latte Supreme',
                'description' => 'Kombinasi espresso, fresh milk, dan condensed milk kental manis legit.',
                'price' => 26000,
                'image_url' => 'https://images.unsplash.com/photo-1577968897966-3d4325b36b61?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],

            // Non-Coffee
            [
                'category_id' => $catNonCoffee->category_id,
                'name' => 'Kyoto Matcha Latte Ice',
                'description' => 'Bubuk matcha murni Uji Jepang dengan susu segar lembut dan foam creamy.',
                'price' => 27000,
                'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catNonCoffee->category_id,
                'name' => 'Choco Belgian Hazelnut',
                'description' => 'Cokelat Belgia pekat berpadu aroma kacang hazelnut gurih nikmat.',
                'price' => 26000,
                'image_url' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catNonCoffee->category_id,
                'name' => 'Peach Blossom Iced Tea',
                'description' => 'Teh hitam seduh dingin dengan sirup buah persik asli dan selasih menyegarkan.',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],

            // Pastry
            [
                'category_id' => $catPastry->category_id,
                'name' => 'Butter Croissant Crispy',
                'description' => 'French butter croissant flaky berlapis renyah di luar, lembut di dalam.',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catPastry->category_id,
                'name' => 'Pain au Chocolat',
                'description' => 'Pastry gulung berisikan lelehan dark chocolate import premium.',
                'price' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],

            // Main Course
            [
                'category_id' => $catMain->category_id,
                'name' => 'Spaghetti Aglio Olio Smoked Beef',
                'description' => 'Pasta al dente ditumis bawang putih, cabai rawit, minyak zaitun, dan potongan smoked beef gurih.',
                'price' => 38000,
                'image_url' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catMain->category_id,
                'name' => 'Chicken Katsu Curry Rice',
                'description' => 'Ayam katsu crispy renyah disajikan dengan kuah kari khas Jepang kental dan wortel kentang manis.',
                'price' => 42000,
                'image_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],

            // Snacks
            [
                'category_id' => $catSnacks->category_id,
                'name' => 'Truffle Fries with Garlic Dip',
                'description' => 'Kentang goreng renyah wangi minyak jamur truffle disajikan dengan saus keju mayones bawang putih.',
                'price' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
            [
                'category_id' => $catSnacks->category_id,
                'name' => 'Crispy Chicken Wings BBQ',
                'description' => 'Sayap ayam goreng empuk berbalut bumbu saus barbekyu asap pedas manis gurih.',
                'price' => 32000,
                'image_url' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=600&auto=format&fit=crop&q=80',
                'is_available' => true,
            ],
        ];

        $createdMenus = [];
        foreach ($menus as $m) {
            $createdMenus[] = Menu::create($m);
        }

        // 5. Sample Initial Orders & Transactions for Demonstration
        // Order 1: Completed & Paid
        $order1 = Order::create([
            'customer_name' => 'Dimas Pratama',
            'table_id' => 1,
            'order_type' => 'DINE_IN',
            'status' => 'COMPLETED',
            'total_amount' => 46000,
            'note' => 'Gula aren sedikit saja.',
            'ordered_at' => Carbon::now()->subHours(2),
            'completed_at' => Carbon::now()->subHours(1),
        ]);

        OrderItem::create([
            'order_id' => $order1->order_id,
            'menu_id' => $createdMenus[0]->menu_id, // Aren Latte
            'quantity' => 1,
            'unit_price' => 24000,
            'subtotal' => 24000,
            'note' => 'Less sweet',
        ]);

        OrderItem::create([
            'order_id' => $order1->order_id,
            'menu_id' => $createdMenus[7]->menu_id, // Butter Croissant
            'quantity' => 1,
            'unit_price' => 22000,
            'subtotal' => 22000,
            'note' => 'Dihangatkan',
        ]);

        OrderStatusLog::create([
            'order_id' => $order1->order_id,
            'changed_by' => null,
            'old_status' => null,
            'new_status' => 'PENDING',
            'changed_at' => Carbon::now()->subHours(2),
        ]);
        OrderStatusLog::create([
            'order_id' => $order1->order_id,
            'changed_by' => $kitchen->staff_id,
            'old_status' => 'PENDING',
            'new_status' => 'ACCEPTED',
            'changed_at' => Carbon::now()->subHours(2)->addMinutes(5),
        ]);
        OrderStatusLog::create([
            'order_id' => $order1->order_id,
            'changed_by' => $kitchen->staff_id,
            'old_status' => 'ACCEPTED',
            'new_status' => 'COMPLETED',
            'changed_at' => Carbon::now()->subHours(1),
        ]);

        $pay1 = Payment::create([
            'order_id' => $order1->order_id,
            'confirmed_by' => $cashier->staff_id,
            'method' => 'QRIS',
            'status' => 'PAID',
            'amount_paid' => 46000,
            'change_amount' => 0,
            'paid_at' => Carbon::now()->subHours(2)->addMinutes(10),
        ]);

        Receipt::create([
            'payment_id' => $pay1->payment_id,
            'issued_by' => $cashier->staff_id,
            'receipt_number' => 'RCP-' . Carbon::now()->format('Ymd') . '-0001',
            'issued_at' => Carbon::now()->subHours(2)->addMinutes(10),
        ]);

        // Order 2: Active Preparing in Kitchen
        $order2 = Order::create([
            'customer_name' => 'Clarissa',
            'table_id' => 3,
            'order_type' => 'DINE_IN',
            'status' => 'PREPARING',
            'total_amount' => 69000,
            'note' => 'Matcha es dipisah kalau bisa.',
            'ordered_at' => Carbon::now()->subMinutes(25),
            'completed_at' => null,
        ]);

        OrderItem::create([
            'order_id' => $order2->order_id,
            'menu_id' => $createdMenus[4]->menu_id, // Kyoto Matcha Latte
            'quantity' => 1,
            'unit_price' => 27000,
            'subtotal' => 27000,
            'note' => 'Ice separated',
        ]);

        OrderItem::create([
            'order_id' => $order2->order_id,
            'menu_id' => $createdMenus[10]->menu_id, // Chicken Katsu Curry
            'quantity' => 1,
            'unit_price' => 42000,
            'subtotal' => 42000,
            'note' => 'Pedas sedang',
        ]);

        OrderStatusLog::create([
            'order_id' => $order2->order_id,
            'changed_by' => null,
            'old_status' => null,
            'new_status' => 'PENDING',
            'changed_at' => Carbon::now()->subMinutes(25),
        ]);
        OrderStatusLog::create([
            'order_id' => $order2->order_id,
            'changed_by' => $kitchen->staff_id,
            'old_status' => 'PENDING',
            'new_status' => 'ACCEPTED',
            'changed_at' => Carbon::now()->subMinutes(20),
        ]);
        OrderStatusLog::create([
            'order_id' => $order2->order_id,
            'changed_by' => $kitchen->staff_id,
            'old_status' => 'ACCEPTED',
            'new_status' => 'PREPARING',
            'changed_at' => Carbon::now()->subMinutes(15),
        ]);

        $pay2 = Payment::create([
            'order_id' => $order2->order_id,
            'confirmed_by' => $cashier->staff_id,
            'method' => 'CASH',
            'status' => 'PAID',
            'amount_paid' => 100000,
            'change_amount' => 31000,
            'paid_at' => Carbon::now()->subMinutes(22),
        ]);

        Receipt::create([
            'payment_id' => $pay2->payment_id,
            'issued_by' => $cashier->staff_id,
            'receipt_number' => 'RCP-' . Carbon::now()->format('Ymd') . '-0002',
            'issued_at' => Carbon::now()->subMinutes(22),
        ]);

        // Order 3: Pending Order (Awaiting payment & Kitchen acceptance)
        $order3 = Order::create([
            'customer_name' => 'Rian Hidayat',
            'table_id' => null,
            'order_type' => 'TAKE_AWAY',
            'status' => 'PENDING',
            'total_amount' => 50000,
            'note' => 'Bungkus rapi, mau dibawa ke kantor.',
            'ordered_at' => Carbon::now()->subMinutes(5),
            'completed_at' => null,
        ]);

        OrderItem::create([
            'order_id' => $order3->order_id,
            'menu_id' => $createdMenus[1]->menu_id, // Caramel Macchiato
            'quantity' => 1,
            'unit_price' => 28000,
            'subtotal' => 28000,
            'note' => 'Extra caramel',
        ]);

        OrderItem::create([
            'order_id' => $order3->order_id,
            'menu_id' => $createdMenus[6]->menu_id, // Peach Blossom Tea
            'quantity' => 1,
            'unit_price' => 22000,
            'subtotal' => 22000,
            'note' => 'Normal ice',
        ]);

        OrderStatusLog::create([
            'order_id' => $order3->order_id,
            'changed_by' => null,
            'old_status' => null,
            'new_status' => 'PENDING',
            'changed_at' => Carbon::now()->subMinutes(5),
        ]);

        Payment::create([
            'order_id' => $order3->order_id,
            'confirmed_by' => null,
            'method' => 'CASH',
            'status' => 'PENDING',
            'amount_paid' => 0,
            'change_amount' => 0,
            'paid_at' => null,
        ]);
    }
}
