<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Laptop Dell XPS 15',
                'type' => 'Laptop',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'HP Laser Printer M404',
                'type' => 'Printer',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'Cisco Router 2901',
                'type' => 'Networking',
                'unit' => 'Piece',
                'status' => 'inactive',
            ],
            [
                'name' => 'Epson Projector EB-S41',
                'type' => 'Projector',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'Samsung Monitor 24 inch',
                'type' => 'Monitor',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'Logitech Wireless Keyboard',
                'type' => 'Peripheral',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'Logitech Wireless Mouse',
                'type' => 'Peripheral',
                'unit' => 'Piece',
                'status' => 'active',
            ],
            [
                'name' => 'Uninterruptible Power Supply (UPS) APC 1000VA',
                'type' => 'Power',
                'unit' => 'Piece',
                'status' => 'inactive',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
