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
        $defaultUserId = 1; // Use existing user as creator
        $defaultCategoryId = 1; // Use existing category
        $defaultRoleId = 2; // Use existing role for client users
        $defaultTicketCategoryId = 9; // Use existing ticket category

        // Fetch all feedback_queries and their ranges
        $feedbackQueries = DB::table('feedback_queries')->select('feedback_query_id', 'range')->get()->toArray();
        $feedbackQueries = array_map(function($q) { return (array)$q; }, $feedbackQueries);

        for ($i = 0; $i < 10; $i++) {
            // Create client
            $clientId = DB::table('clients')->insertGetId([
                'client_importid' => $faker->uuid,
                'client_created' => $faker->dateTimeBetween('-2 years', 'now'),
                'client_updated' => $faker->dateTimeBetween('-2 years', 'now'),
                'client_creatorid' => $defaultUserId,
                'client_created_from_leadid' => 0,
                'client_categoryid' => $defaultCategoryId,
                'client_company_name' => $faker->company,
                'client_description' => $faker->sentence,
                'client_phone' => $faker->phoneNumber,
                'client_website' => $faker->url,
                'client_vat' => strtoupper($faker->bothify('??########')),
                'client_status' => $faker->randomElement(['active', 'suspended']),
            ]);

            // Create users (contacts)
            $ownerIndex = rand(0, 1); // Randomly pick one user to be the owner
            for ($j = 0; $j < 2; $j++) {
                DB::table('users')->insert([
                    'created' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated' => $faker->dateTimeBetween('-2 years', 'now'),
                    'creatorid' => $defaultUserId,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone' => $faker->phoneNumber,
                    'clientid' => $clientId,
                    'type' => 'client',
                    'status' => 'active',
                    'role_id' => $defaultRoleId,
                    'account_owner' => ($j === $ownerIndex) ? 'yes' : 'no',
                ]);
            }

            // Create projects
            for ($j = 0; $j < 2; $j++) {
                $projectId = DB::table('projects')->insertGetId([
                    'project_created' => $faker->dateTimeBetween('-2 years', 'now'),
                    'project_updated' => $faker->dateTimeBetween('-2 years', 'now'),
                    'project_clientid' => $clientId,
                    'project_creatorid' => $defaultUserId,
                    'project_categoryid' => $defaultCategoryId,
                    'project_title' => $faker->catchPhrase,
                    'project_date_due' => $faker->dateTimeBetween('now', '+1 year'),
                    'project_status' => $faker->randomElement(['not_started', 'completed']),
                ]);

                // Create invoices
                for ($k = 0; $k < 2; $k++) {
                    $invoiceId = DB::table('invoices')->insertGetId([
                        'bill_clientid' => $clientId,
                        'bill_projectid' => $projectId,
                        'bill_creatorid' => $defaultUserId,
                        'bill_categoryid' => 4,
                        'bill_date' => $faker->dateTimeBetween('-1 year', 'now'),
                        'bill_final_amount' => $faker->randomFloat(2, 100, 10000),
                        'bill_status' => $faker->randomElement(['paid', 'due', 'overdue']),
                    ]);

                    // Create payments for invoice
                    DB::table('payments')->insert([
                        'payment_created' => $faker->dateTimeBetween('-1 year', 'now'),
                        'payment_creatorid' => $defaultUserId,
                        'payment_clientid' => $clientId,
                        'payment_projectid' => $projectId,
                        'payment_invoiceid' => $invoiceId,
                        'payment_amount' => $faker->randomFloat(2, 100, 10000),
                        'payment_type' => 'invoice',
                    ]);
                }
            }

            // Create feedbacks and feedback_details
            for ($j = 0; $j < 10; $j++) {
                $feedbackId = DB::table('feedbacks')->insertGetId([
                    'client_id' => $clientId,
                    'feedback_date' => $faker->date(),
                    'comment' => $faker->sentence,
                    'feedback_created' => $faker->dateTimeBetween('-1 year', 'now'),
                    'feedback_updated' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
                for ($k = 0; $k < 2; $k++) {
                    // Pick a random feedback_query and use its range
                    $feedbackQuery = $feedbackQueries[array_rand($feedbackQueries)];
                    DB::table('feedback_details')->insert([
                        'feedback_id' => $feedbackId,
                        'feedback_query_id' => $feedbackQuery['feedback_query_id'],
                        'value' => $faker->numberBetween(1, $feedbackQuery['range']),
                        'feedback_detail_created' => $faker->dateTimeBetween('-1 year', 'now'),
                        'feedback_detail_updated' => $faker->dateTimeBetween('-1 year', 'now'),
                    ]);
                }
            }

            // Create client expectations
            for ($j = 0; $j < 2; $j++) {
                DB::table('client_expectations')->insert([
                    'client_id' => $clientId,
                    'title' => $faker->sentence(3),
                    'content' => $faker->sentence,
                    'weight' => $faker->randomFloat(2, 1, 10),
                    'due_date' => $faker->dateTimeBetween('now', '+6 months'),
                    'status' => $faker->randomElement(['pending', 'fulfilled']),
                    'expectation_created' => $faker->dateTimeBetween('-1 year', 'now'),
                    'expectation_updated' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }

            // Create tickets
            for ($j = 0; $j < 2; $j++) {
                DB::table('tickets')->insert([
                    'ticket_created' => $faker->dateTimeBetween('-1 year', 'now'),
                    'ticket_creatorid' => $defaultUserId,
                    'ticket_categoryid' => $defaultTicketCategoryId,
                    'ticket_clientid' => $clientId,
                    'ticket_subject' => $faker->sentence(4),
                    'ticket_priority' => $faker->randomElement(['normal', 'high', 'urgent']),
                    'ticket_status' => $faker->numberBetween(1, 4),
                    'ticket_source' => 'web',
                ]);
            }

            // Create notes (morph to client)
            for ($j = 0; $j < 2; $j++) {
                DB::table('notes')->insert([
                    'note_created' => $faker->dateTimeBetween('-1 year', 'now'),
                    'note_creatorid' => $defaultUserId,
                    'note_title' => $faker->sentence(3),
                    'note_description' => $faker->sentence,
                    'note_visibility' => 'public',
                    'noteresource_type' => 'client',
                    'noteresource_id' => $clientId,
                ]);
            }
        }
    }
} 