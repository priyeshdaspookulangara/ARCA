<?php

namespace Modules\Fina\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class FinaDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ChartsOfAccountsTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(TaxCodesTableSeeder::class);
    }
}
