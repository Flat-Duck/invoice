<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'company_id' => Company::factory(),
            'invoice_number' => 'INV-'.$date->format('Y').'-'.fake()->unique()->numberBetween(1000, 99999),
            'work_order' => 'WO-'.fake()->numberBetween(1000, 9999),
            'service_agreement' => fake()->optional()->numerify('SA-####'),
            'to_reference' => fake()->optional()->numerify('TO-####'),
            'invoice_date' => $date,
            'invoice_year' => (int) $date->format('Y'),
            'invoice_month' => (int) $date->format('n'),
            'amount' => fake()->randomFloat(2, 350, 25000),
            'exchange_rate' => fake()->randomFloat(6, 0.5, 5),
            'location' => fake()->randomElement(['Tripoli', 'Benghazi', 'Misrata', 'Sabha', 'Tobruk']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
