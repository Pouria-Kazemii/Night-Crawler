<?php

namespace Database\Seeders;

use App\Models\CrawlerNode;
use Illuminate\Database\Seeder;

class CrawlerNodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        CrawlerNode::insert([
            [
                'name' => 'localhost 1',
                'ip_address' => '127.0.0.1',
                'port' => 5000,
                'protocol' => 'http',
                'status' => 'active',
                'last_used_at' => now()->subMinutes(fake()->numberBetween(1, 1000)),
                'is_verifyed' => true,
                'location' => fake()->country(),
                'latency' => fake()->randomFloat(2, 10, 500),
            ],
            [
                'name' => 'localhost 2',
                'ip_address' => '127.0.0.1',
                'port' => 5001,
                'protocol' => 'http',
                'status' => 'active',
                'last_used_at' => now()->subMinutes(fake()->numberBetween(1, 1000)),
                'is_verifyed' => true,
                'location' => fake()->country(),
                'latency' => fake()->randomFloat(2, 10, 500),
            ]
        ]);
    }
}
