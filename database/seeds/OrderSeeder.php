<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->insert([
            'user_id' => '',
            'total_price' => '',
            'invoice_number' => '',
            'status' => '',
            
        ]);
    }
}
