<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@brewpos.com',
            'password' => Hash::make('admin123'),
        ]);

        // ── Products ──
        // Weights reflect real coffee shop sales: coffee drinks dominate
        $products = [
            // Coffee (high volume, mid price)
            ['name' => 'Iced Latte',          'category' => 'Coffee',     'price' => 90,  'cost' => 28, 'stock' => 100, 'weight' => 18, 'img' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80'],
            ['name' => 'Cappuccino',           'category' => 'Coffee',     'price' => 95,  'cost' => 30, 'stock' => 80,  'weight' => 14, 'img' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80'],
            ['name' => 'Caramel Macchiato',    'category' => 'Coffee',     'price' => 115, 'cost' => 38, 'stock' => 70,  'weight' => 12, 'img' => 'https://images.unsplash.com/photo-1485808191679-5f86510bd9d4?w=400&q=80'],
            ['name' => 'Americano',            'category' => 'Coffee',     'price' => 85,  'cost' => 22, 'stock' => 90,  'weight' => 10, 'img' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80'],
            ['name' => 'Espresso',             'category' => 'Coffee',     'price' => 75,  'cost' => 18, 'stock' => 80,  'weight' => 8,  'img' => 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&q=80'],
            ['name' => 'Cold Brew',            'category' => 'Coffee',     'price' => 110, 'cost' => 32, 'stock' => 60,  'weight' => 9,  'img' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&q=80'],
            // Non-Coffee (medium volume)
            ['name' => 'Matcha Latte',         'category' => 'Non-Coffee', 'price' => 110, 'cost' => 35, 'stock' => 60,  'weight' => 8,  'img' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80'],
            ['name' => 'Chocolate Frappe',     'category' => 'Non-Coffee', 'price' => 120, 'cost' => 40, 'stock' => 50,  'weight' => 7,  'img' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&q=80'],
            ['name' => 'Strawberry Smoothie',  'category' => 'Non-Coffee', 'price' => 105, 'cost' => 32, 'stock' => 40,  'weight' => 5,  'img' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=400&q=80'],
            // Pastry (lower volume, pairs with drinks)
            ['name' => 'Butter Croissant',     'category' => 'Pastry',     'price' => 75,  'cost' => 22, 'stock' => 40,  'weight' => 6,  'img' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80'],
            ['name' => 'Blueberry Muffin',     'category' => 'Pastry',     'price' => 65,  'cost' => 18, 'stock' => 35,  'weight' => 5,  'img' => 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400&q=80'],
            ['name' => 'Cheese Danish',        'category' => 'Pastry',     'price' => 80,  'cost' => 25, 'stock' => 30,  'weight' => 4,  'img' => 'https://images.unsplash.com/photo-1509365465985-25d11c17e812?w=400&q=80'],
            // Snacks (lowest volume)
            ['name' => 'Club Sandwich',        'category' => 'Snacks',     'price' => 145, 'cost' => 55, 'stock' => 25,  'weight' => 3,  'img' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&q=80'],
            ['name' => 'Caesar Salad',         'category' => 'Snacks',     'price' => 135, 'cost' => 48, 'stock' => 20,  'weight' => 2,  'img' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=400&q=80'],
        ];

        foreach ($products as $p) {
            $weight = $p['weight'];
            unset($p['weight']);
            Product::create(array_merge($p, ['status' => 'Active', 'sold' => 0]));
        }

        $allProducts = Product::all();

        // Build weighted product pool for realistic order distribution
        $weightedPool = [];
        foreach ($allProducts as $product) {
            $p = $products[array_search($product->name, array_column($products, 'name'))];
            $w = $p['weight'] ?? 5;
            for ($i = 0; $i < $w; $i++) {
                $weightedPool[] = $product;
            }
        }

        // ── Customers ──
        $customerDefs = [
            ['name' => 'Maria Santos',    'phone' => '09171234567', 'email' => 'maria@email.com'],
            ['name' => 'Jose Reyes',      'phone' => '09281234567', 'email' => null],
            ['name' => 'Ana Cruz',        'phone' => '09391234567', 'email' => 'ana@email.com'],
            ['name' => 'Pedro Lim',       'phone' => null,          'email' => 'pedro@email.com'],
            ['name' => 'Rosa Garcia',     'phone' => '09501234567', 'email' => null],
            ['name' => 'Carlo Mendoza',   'phone' => '09611234567', 'email' => 'carlo@email.com'],
            ['name' => 'Liza Tan',        'phone' => '09721234567', 'email' => 'liza@email.com'],
            ['name' => 'Mark Villanueva', 'phone' => '09831234567', 'email' => null],
        ];

        $customers = [];
        foreach ($customerDefs as $cd) {
            $customers[] = Customer::create(array_merge($cd, [
                'points' => 0, 'total_visits' => 0, 'total_spent' => 0, 'tier' => 'Bronze',
            ]));
        }

        // ── Generate Orders — 60 days ──
        $orderNumber = 1000;
        $now         = now();
        $soldCounts  = [];

        for ($daysAgo = 59; $daysAgo >= 0; $daysAgo--) {
            $date      = $now->copy()->subDays($daysAgo);
            $isWeekend = in_array($date->dayOfWeek, [0, 6]);

            // Realistic daily pattern: busier on weekends, morning/lunch/afternoon peaks
            $orderCount = $isWeekend ? rand(35, 50) : rand(22, 35);

            for ($i = 0; $i < $orderCount; $i++) {
                // Realistic hour distribution: 7am-9pm, peaks at 8-10am and 12-2pm
                $hourWeights = [7=>3,8=>10,9=>12,10=>10,11=>8,12=>12,13=>10,14=>8,15=>7,16=>6,17=>5,18=>4,19=>3,20=>2];
                $hour = $this->weightedRandom($hourWeights);
                $orderTime = $date->copy()->setTime($hour, rand(0, 59));

                // 1-3 items per order, mostly 1-2
                $itemCount = $this->weightedRandom([1 => 50, 2 => 35, 3 => 15]);
                $pickedProducts = [];
                for ($j = 0; $j < $itemCount; $j++) {
                    $pickedProducts[] = $weightedPool[array_rand($weightedPool)];
                }

                // Deduplicate — combine qty for same product
                $orderMap = [];
                foreach ($pickedProducts as $p) {
                    $orderMap[$p->id] = isset($orderMap[$p->id])
                        ? ['product' => $p, 'qty' => $orderMap[$p->id]['qty'] + 1]
                        : ['product' => $p, 'qty' => 1];
                }

                $subtotal = 0;
                foreach ($orderMap as $item) {
                    $subtotal += $item['product']->price * $item['qty'];
                }

                $tax      = round($subtotal * 0.10, 2);
                $total    = round($subtotal + $tax, 2);
                $received = ceil($total / 50) * 50;

                $order = Order::create([
                    'order_number'    => (string) ++$orderNumber,
                    'table_number'    => rand(1, 12),
                    'subtotal'        => $subtotal,
                    'tax'             => $tax,
                    'total'           => $total,
                    'payment_method'  => 'Cash',
                    'amount_received' => $received,
                    'change_given'    => $received - $total,
                    'created_at'      => $orderTime,
                    'updated_at'      => $orderTime,
                ]);

                foreach ($orderMap as $item) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item['product']->id,
                        'product_name' => $item['product']->name,
                        'price'        => $item['product']->price,
                        'quantity'     => $item['qty'],
                        'created_at'   => $orderTime,
                        'updated_at'   => $orderTime,
                    ]);
                    $soldCounts[$item['product']->id] = ($soldCounts[$item['product']->id] ?? 0) + $item['qty'];
                }

                // Attach to a customer — varied frequency per customer (3-15%)
                $attachRates = [15, 12, 4, 10, 6, 13, 8, 3]; // per customer index
                $custIndex   = array_rand($customers);
                if (rand(1, 100) <= $attachRates[$custIndex % count($attachRates)]) {
                    $customer = $customers[$custIndex];
                    $earned   = (int) floor($total / 10);
                    $customer->points       += $earned;
                    $customer->total_visits += 1;
                    $customer->total_spent  += $total;
                    $customer->tier          = \App\Models\Customer::tierFromPoints($customer->points);
                    $customer->save();
                }
            }
        }

        // Update sold counts on products
        foreach ($soldCounts as $productId => $qty) {
            Product::where('id', $productId)->update(['sold' => $qty]);
        }

        // ── Expenses — realistic monthly recurring + variable ──
        $recurringExpenses = [
            ['description' => 'Shop Space Rent',         'category' => 'Rent',        'amount' => 25000, 'day' => 1],
            ['description' => 'Monthly Electricity',     'category' => 'Utilities',   'amount' => 8500,  'day' => 5],
            ['description' => 'Water Bill',              'category' => 'Utilities',   'amount' => 1200,  'day' => 5],
            ['description' => 'Internet & WiFi',         'category' => 'Utilities',   'amount' => 1800,  'day' => 5],
            ['description' => 'Part-time Staff Wages',   'category' => 'Staff',       'amount' => 18000, 'day' => 15],
            ['description' => 'Full-time Barista Salary','category' => 'Staff',       'amount' => 22000, 'day' => 15],
        ];

        $variableExpenses = [
            ['description' => 'Coffee Beans Supply',     'category' => 'Stock',       'amount_min' => 12000, 'amount_max' => 16000],
            ['description' => 'Milk & Dairy Products',   'category' => 'Stock',       'amount_min' => 5000,  'amount_max' => 7000],
            ['description' => 'Pastry Ingredients',      'category' => 'Stock',       'amount_min' => 3000,  'amount_max' => 4500],
            ['description' => 'Packaging & Cups',        'category' => 'Stock',       'amount_min' => 2000,  'amount_max' => 3000],
            ['description' => 'Cleaning Supplies',       'category' => 'Maintenance', 'amount_min' => 800,   'amount_max' => 1200],
            ['description' => 'Equipment Maintenance',   'category' => 'Maintenance', 'amount_min' => 1500,  'amount_max' => 3500],
        ];

        // Seed 3 months of expenses
        for ($monthsAgo = 2; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate = $now->copy()->subMonths($monthsAgo);

            foreach ($recurringExpenses as $exp) {
                $date = $monthDate->copy()->startOfMonth()->addDays($exp['day'] - 1);
                if ($date->lte($now)) {
                    Expense::create([
                        'description' => $exp['description'],
                        'category'    => $exp['category'],
                        'amount'      => $exp['amount'],
                        'date'        => $date->toDateString(),
                        'status'      => 'Paid',
                    ]);
                }
            }

            // Variable expenses 2x per month (restocking)
            foreach ($variableExpenses as $exp) {
                for ($restock = 0; $restock < 2; $restock++) {
                    $day  = $restock === 0 ? rand(3, 10) : rand(16, 25);
                    $date = $monthDate->copy()->startOfMonth()->addDays($day - 1);
                    if ($date->lte($now)) {
                        Expense::create([
                            'description' => $exp['description'],
                            'category'    => $exp['category'],
                            'amount'      => rand($exp['amount_min'], $exp['amount_max']),
                            'date'        => $date->toDateString(),
                            'status'      => 'Paid',
                        ]);
                    }
                }
            }
        }
    }

    private function weightedRandom(array $weights): int|string
    {
        $total = array_sum($weights);
        $rand  = rand(1, $total);
        $cumulative = 0;
        foreach ($weights as $value => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) return $value;
        }
        return array_key_first($weights);
    }
}
