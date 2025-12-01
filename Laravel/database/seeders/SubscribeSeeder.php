<?php

namespace Database\Seeders;

use App\Models\Subscribe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscribeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscribe::insert([
            [
                'title' => 'اشتراک پلاتینیوم _ 1 ماهه',
                'uct' => 'P-1',
                'available_token_count' => 'unlimit',
                'expired_time' => '1 ماه'
            ],
            [
                'title' => 'اشتراک پلاتینیوم _ 3 ماهه',
                'uct' => 'P-3',
                'available_token_count' => 'unlimit',
                'expired_time' => '3 ماه'
            ],
            [
                'title' => 'اشتراک پلاتینیوم _ 6 ماهه',
                'uct' => 'P-6',
                'available_token_count' => 'unlimit',
                'expired_time' => '6 ماه'
            ],
            [
                'title' => 'اشتراک پلاتینیوم _ 12 ماهه',
                'uct' => 'P-12',
                'available_token_count' => 'unlimit',
                'expired_time' => '12 ماه'

            ],
        ]);
    }
}
