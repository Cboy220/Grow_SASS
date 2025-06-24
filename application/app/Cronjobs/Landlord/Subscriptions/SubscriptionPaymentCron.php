<?php

/** ---------------------------------------------------------------------------------------------------
 * [PROCESS SUBSCRIPTION PAYMENT - WEBHOOK]
 * This cronjob checks for webhooks marked as [subscription-payment]. Covering all payment gateways.
 * It will process the webhook as follows:
 *
 *       - add a new payment for the customer in the database\
 *       - set the subsccription as active in both landlord & tenant databases
 *       - Send thank you for your payment email to client
 *       - Send new payment received email to admin
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord\Subscriptions;
use Illuminate\Support\Facades\Mail;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class SubscriptionPaymentCron {

    public function __invoke(
    ) {

        //[MT] - landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();

        /**
         *   - Find webhhoks waiting to be completed
         *   - mark the appropriate invoice as paid
         *   - ecords timeline event & notifications
         *   - Send thank you for your payment email to client
         *   - Send new payment received email to admin
         *   - Limit 20 emails at a time (for performance)
         */
        //Get the emails marked as [pdf] and [invoice] - limit 5
        $limit = 5;
        if ($webhooks = \App\Models\Landlord\Webhook::on('landlord')
            ->where('webhooks_crm_reference', 'subscription-payment')
            ->where('webhooks_transaction_type', 'subscription')
            ->where('webhooks_attempts', '<=', 3)
            ->where('webhooks_status', 'new')->take($limit)->get()) {

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($webhooks as $webhook) {
                $webhook->update([
                    'webhooks_status' => 'processing',
                ]);
            }

            //loop and process each webhook in the batch
            foreach ($webhooks as $webhook) {

                //check if there is a corresponding subscription for the payment session
                if (!$subscription = \App\Models\Landlord\Subscription::on('landlord')->Where('subscription_gateway_id', $webhook->webhooks_gateway_reference)->first()) {

                    Log::info("webhook - [subscription-payment] - unable to find a corresnding subscription ($webhook->webhooks_gateway_reference) for thie webhook [subscription-payment] - will now exit", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);

                    //we have reached max number of attempts
                    if ($webhook->webhooks_attempts == 3) {
                        //log error
                        Log::error("no corresponding ($webhook->webhooks_gateway_reference) was found", ['process' => '[landlord-cronjob][subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        $webhook->update([
                            'webhooks_status' => 'failed',
                            'webhooks_comment' => "no corresponding subscription ($webhook->webhooks_gateway_reference) was found",
                        ]);
                    } else {
                        Log::info("webhook - [subscription-payment] - the subscription ($webhook->webhooks_gateway_reference) could not be found. it may still be in checkout (thank you page) process. will try again later", ['process' => '[landlord-cronjob][subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        $webhook->update([
                            'webhooks_attempts' => $webhook->webhooks_attempts + 1,
                            'webhooks_comment' => "the subscription could not be found. it may still be in checkout (thank you page) process. will try again later",
                        ]);
                    }

                    //skip to next webhook in the batch
                    continue;
                }

                Log::info("webhook - [subscription-payment] found a valid webhook to process - subscription id ($webhook->webhooks_gateway_reference)", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);

                //check that his has not already been recorded
                if (\App\Models\Landlord\Payment::on('landlord')->Where('payment_transaction_id', $webhook->webhooks_transaction_id)->exists()) {
                    Log::info("webhook - [subscription-payment] - a payment for this webhook already exists in the database. will now skip", ['process' => '[landlord-cronjob][subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    $webhook->update([
                        'webhooks_status' => 'completed',
                    ]);
                    continue;
                }

                /** -------------------------------------------------------------
                 * set the tenant's account as paid and send emails
                 * ------------------------------------------------------------*/
                if ($customer = Tenant::Where('tenant_id', $subscription->subscription_customerid)->first()) {

                    //create new payment
                    $payment = new \App\Models\Landlord\Payment();
                    $payment->setConnection('landlord');
                    $payment->payment_date = $webhook->webhooks_payment_date;
                    $payment->payment_tenant_id = $subscription->subscription_customerid;
                    $payment->payment_amount = $webhook->webhooks_amount;
                    $payment->payment_transaction_id = $webhook->webhooks_transaction_id;
                    $payment->payment_subscription_id = $subscription->subscription_id;
                    $payment->payment_gateway = ucwords($webhook->webhooks_source);
                    $payment->save();

                    //update subscription renewal dates
                    $subscription->subscription_date_renewed = $webhook->webhooks_payment_date;
                    $subscription->subscription_date_next_renewal = $webhook->webhooks_next_due_date;
                    $subscription->subscription_status = 'active';
                    $subscription->save();

                    //mark webhook cronjob as done
                    $webhook->update([
                        'webhooks_status' => 'completed',
                    ]);

                    /** ----------------------------------------------------------------------
                     * update the tenants instance in their DB as [active]
                     * ---------------------------------------------------------------------*/
                    Tenant::forgetCurrent();
                    if ($tenant = Tenant::Where('tenant_id', $subscription->subscription_customerid)->first()) {
                        try {
                            //swicth to this tenants DB
                            $tenant->makeCurrent();

                            //update customers account as cancelled
                            \App\Models\Landlord\Settings::on('tenant')->where('settings_id', 1)
                                ->update([
                                    'settings_saas_status' => 'active',
                                ]);

                            //mark customer as cancelled in landlord db
                            \App\Models\Landlord\Tenant::on('landlord')->where('tenant_id', $subscription->subscription_customerid)
                                ->update([
                                    'tenant_status' => 'active',
                                ]);

                            //forget tenant
                            Tenant::forgetCurrent();

                        } catch (Exception $e) {
                            Log::error("webhook - [subscription-payment] - error trying to set the tenants database as [active] - error: " . $e->getMessage(), ['process' => '[landlord-cronjob][subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id' => $webhook->subscription_id, 'tenant_id' => $subscription->subscription_customerid]);
                            $webhook->update([
                                'webhooks_status' => 'failed',
                                'webhooks_comment' => "unable to update the tenant database to status [active] - tenant id ($subscription->subscription_customerid)",
                            ]);
                            continue;
                        }
                    }

                    /** ----------------------------------------------
                     * send thank you email to customer & admin
                     * ----------------------------------------------*/
                    $data = [
                        'customer_name' => $customer->tenant_name,
                        'customer_id' => $customer->tenant_id,
                        'payment_gateway' => 'Paypal',
                        'amount' => runtimeMoneyFormat($payment->payment_amount),
                    ];

                    //email customer
                    $this->emailCustomer($data, $payment, $subscription);

                    //email admin
                    $this->emailAdmin($data, $payment, $subscription);

                }
            }
        }
    }

    /**
     * Queue an email to the customer
     *
     * @param  array  $data
     * @param  obj  $subscription
     * @return null
     */
    public function emailCustomer($data, $payment, $subscription) {

        //email the customer
        if ($customer = \App\Models\Landlord\Tenant::on('landlord')->Where('tenant_id', $subscription->subscription_customerid)->first()) {
            //queue email
            $mail = new \App\Mail\Landlord\Customer\PaymentConfirmation($customer, $data, $payment);
            $mail->build();
        }

    }

    /**
     * Queue an email to the admin
     *
     * @param  array  $data
     * @param  obj  $subscription
     * @return null
     */
    public function emailAdmin($data, $payment, $subscription) {

        //email admin users
        if ($admins = \App\Models\User::On('landlord')->Where('type', 'admin')->get()) {
            //queue email
            foreach ($admins as $user) {
                $mail = new \App\Mail\Landlord\Admin\NewPayment($user, $data, $payment);
                $mail->build();
            }
        }
    }

}