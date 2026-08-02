<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Company::factory(12)->create()->each(
            fn (Company $company) => Invoice::factory(random_int(3, 10))->for($company)->create()
        );
    }
}
