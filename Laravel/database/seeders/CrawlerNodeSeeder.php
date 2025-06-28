<?php

namespace Database\Seeders;

use App\Models\CrawlerNode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrawlerNodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $protocols = ['http', 'https', 'socks5'];
        $statuses = ['active', 'inactive', 'banned', 'down'];

        for ($i = 0; $i < 100; $i++) {
            CrawlerNode::create([
                'name' => 'Node ' . ($i + 1),
                'ip_address' => fake()->ipv4(),
                'port' => fake()->numberBetween(8000, 9000),
                'protocol' => fake()->randomElement($protocols),
                'status' => fake()->randomElement($statuses),
                'last_used_at' => now()->subMinutes(fake()->numberBetween(1, 1000)),
                'is_verifyed' => fake()->boolean(),
                'location' => fake()->country(),
                'latency' => fake()->randomFloat(2, 10, 500),
            ]);
        }
    }
}
