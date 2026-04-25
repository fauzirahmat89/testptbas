<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'NEW CUSTOMER'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'LOYAL CUSTOMER'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'status' => 'NEW CUSTOMER'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
