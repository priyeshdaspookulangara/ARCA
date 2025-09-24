<?php

namespace Modules\Fina\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartsOfAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fina_charts_of_accounts')->insert([
            'name' => 'Default Chart of Accounts',
            'country_code' => 'US',
        ]);
    }
}
