<?php

namespace Modules\Fina\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fina_tax_codes')->insert([
            ['code' => 'VAT10', 'rate' => 10.00],
            ['code' => 'VAT20', 'rate' => 20.00],
        ]);
    }
}
