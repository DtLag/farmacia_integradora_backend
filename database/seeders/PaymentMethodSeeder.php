<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            'Efectivo',
            'Tarjeta',
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['method_name' => $method],
                ['slug' => Str::slug($method)]
            );
        }
    }
}