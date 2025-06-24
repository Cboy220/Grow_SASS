<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ClientAITestSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            // Create client
            $clientId = DB::table('clients')->insertGetId([
                'client_company_name' => $faker->company,
                'industry' => $faker->word,
                'client_status' => $faker->randomElement(['active', 'inactive']),
                'client_created' => $faker->dateTimeBetween('-2 years', 'now'),
                'client_description' => $faker->sentence,
                'client_website' => $faker->url,
                'client_phone' => $faker->phoneNumber,
                'client_vat' => strtoupper($faker->bothify('??########')),
                'client_categoryid' => 1, // You may want to seed categories too
            ]);

            // Create users (contacts)
            for ($j = 0; $j < 2; $j++) {
                DB::table('users')->insert([
                    'clientid' => $clientId,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'role_id' => 2,
                    'type' => 'client',
                ]);
            }

            // Create projects
            for ($j = 0; $j < 2; $j++) {
                $projectId = DB::table('projects')->insertGetId([
                    'project_clientid' => $clientId,
                    'project_title' => $faker->catchPhrase,
                    'project_status' => $faker->randomElement(['active', 'completed']),
                    'project_created' => $faker->dateTimeBetween('-2 years', 'now'),
                    'project_deadline' => $faker->dateTimeBetween('now', '+1 year'),
                ]);
            }

            // Create invoices
            for ($j = 0; $j < 2; $j++) {
                DB::table('invoices')->insert([
                    'bill_clientid' => $clientId,
                    'bill_invoiceid' => $faker->unique()->randomNumber(6),
                    'bill_final_amount' => $faker->randomFloat(2, 100, 10000),
                    'bill_status' => $faker->randomElement(['paid', 'unpaid', 'overdue']),
                    'bill_date' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }

            // Create payments
            for ($j = 0; $j < 2; $j++) {
                DB::table('payments')->insert([
                    'payment_clientid' => $clientId,
                    'payment_id' => $faker->unique()->randomNumber(6),
                    'payment_amount' => $faker->randomFloat(2, 100, 10000),
                    'payment_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'payment_method' => $faker->randomElement(['credit_card', 'bank_transfer', 'paypal']),
                ]);
            }

            // Create feedbacks and feedback_details
            for ($j = 0; $j < 2; $j++) {
                $feedbackId = DB::table('feedbacks')->insertGetId([
                    'client_id' => $clientId,
                    'feedback_score' => $faker->numberBetween(1, 10),
                    'comment' => $faker->sentence,
                    'feedback_created' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
                for ($k = 0; $k < 2; $k++) {
                    DB::table('feedback_details')->insert([
                        'feedback_id' => $feedbackId,
                        'feedback_query_id' => $faker->numberBetween(1, 5),
                        'value' => $faker->numberBetween(1, 10),
                        'title' => $faker->words(3, true),
                    ]);
                }
            }

            // Create client expectations
            for ($j = 0; $j < 2; $j++) {
                DB::table('client_expectations')->insert([
                    'client_id' => $clientId,
                    'title' => $faker->sentence(3),
                    'status' => $faker->randomElement(['pending', 'fulfilled']),
                    'due_date' => $faker->dateTimeBetween('now', '+6 months'),
                    'weight' => $faker->numberBetween(1, 10),
                    'expectation_created' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }

            // Create tickets
            for ($j = 0; $j < 2; $j++) {
                DB::table('tickets')->insert([
                    'ticket_clientid' => $clientId,
                    'ticket_id' => $faker->unique()->randomNumber(6),
                    'ticket_subject' => $faker->sentence(4),
                    'ticket_status' => $faker->randomElement(['open', 'closed']),
                    'ticket_created' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }

            // Create notes
            for ($j = 0; $j < 2; $j++) {
                DB::table('notes')->insert([
                    'noteresource_id' => $clientId,
                    'noteresource_type' => 'client',
                    'note_text' => $faker->sentence,
                    'note_created' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
} 