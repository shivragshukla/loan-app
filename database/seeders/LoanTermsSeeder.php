<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanTerm;

class LoanTermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoanTerm::create([
            'name' => 'Short Term',
            'rate' => '20',
            'period' => '1-12 Months',
            'penalty' => '-',
        ]);
        LoanTerm::create([
            'name' => 'Intermediate Term',
            'rate' => '15',
            'period' => '1-5 Years',
            'penalty' => '-',
        ]);
        LoanTerm::create([
            'name' => 'Long Term',
            'rate' => '10',
            'period' => '5-10 Years',
            'penalty' => '-',
        ]);
    }
}
