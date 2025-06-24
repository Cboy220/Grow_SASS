<?php

/** ---------------------------------------------------------------------------------------------------
 * [PROCESS SUBSCRIPTION PAYMENT - WEBHOOK]
 * This cronjob checks for webhooks marked as [subscription-payment-failed]. Covering all payment gateways.
 * It will process the webhook as follows:
 *
 *       - Send an email to the customer advising them to resolve the issue before suspension
 *
 * This cronjob does NOT change the status of the subscrition (in both admin and tenant) databases. That
 * task is done when the subscription's grace period has expired by the [subscription-expired] cronjob
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord\Subscriptions;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class SubscriptionPaymentFailedCron {

    public function __invoke(
        UserRepository $userrepo
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
         *   - Find web hooks waiting to be processes
         *   - Email customer to notify them that their payment failed
         *   - actual subscription will be cancelled automatically, usint the grace period expiration system
         */
        $limit = 5;
        if ($webhooks = \App\Models\Webhook::on('landlord')
            ->where('webhooks_crm_reference', 'subscription-payment-failed')
            ->where('webhooks_transaction_type', 'subscription')
            ->where('webhooks_status', 'new')->take($limit)->get()) {

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($webhooks as $webhook) {
                $webhook->update([
                    'webhooks_status' => 'processing',
                ]);
            }

            //loop and process each webhook in the batch
            foreach ($webhooks as $webhook) {

                //get the subscription from db
                if ($subscription = \App\Models\Landlord\Subscription::on('landlord')->Where('subscription_gateway_id', $webhook->webhooks_gateway_reference)->first()) {

                    Log::info("webhook - [Subscription-payment-failed] - found a valid webhook to process - subscription id (" . $webhook->webhooks_gateway_reference . ")", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);

                    /*--------------------------------------------------------
                     * send out emails
                     * ------------------------------------------------------*/
                    $data = [];

                    //get the plan
                    if ($package = \App\Models\Landlord\Package::on('landlord')->Where('package_id', $subscription->subscription_package_id)->first()) {
                        $data['plan_name'] = $package->package_name;
                    }

                    //email customer
                    $this->emailCustomer($data, $subscription);

                } else {
                    Log::info("webhook - [Subscription-payment-failed] - unable to find a corresnding subscription ($webhook->webhooks_gateway_reference) for this webhook - will now exit", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);
                }

                //mark webhook cronjob as done
                $webhook->update([
                    'webhooks_status' => 'completed',
                ]);

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
    public function emailCustomer($data, $subscription) {

        //email the customer
        if ($customer = \App\Models\Landlord\Tenant::on('landlord')->Where('tenant_id', $subscription->subscription_customerid)->first()) {
            //queue email
            $mail = new \App\Mail\Landlord\Customer\PaymentFailed($customer, $data, $subscription);
            $mail->build();
        }

    }
}