<?php

/** ---------------------------------------------------------------------------------------------------
 * [PROCESS SUBSCRIPTION ACTIVATED - WEBHOOK]
 * This cronjob checks for webhooks marked as [subscription-activated]. Covering all payment gateways.
 * It will process the webhook as follows:
 *
 *       - marks the subscription as active in both landlod and tenant databases
 *       - updates the subscriptions next renewal date, in the landlord database
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord\Subscriptions;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class SubscriptionActivatedCron {

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

        //forget tenant
        Tenant::forgetCurrent();

        /**
         *   - Find web hooks waiting to be processes
         *   - Find the subscription and cancel it in the crm db
         *   - set the tenants account as [cancelled]
         */
        $limit = 5;
        if ($webhooks = \App\Models\Webhook::on('landlord')
            ->where('webhooks_crm_reference', 'subscription-activated')
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

                    Log::info("webhook - [Subscription-activated] - found a valid webhook to process - subscription id ($webhook->webhooks_gateway_reference) - will now pocess", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);

                    //update subscription as [active] in the landlord db
                    $subscription->subscription_status = 'active';
                    $subscription->subscription_gateway_name = strtolower($webhook->webhooks_source);
                    $subscription->subscription_date_next_renewal = $webhook->webhooks_next_due_date;
                    $subscription->save();

                    /** ----------------------------------------------------------------------
                     * update the tenants instance in their DB as [active]
                     * ---------------------------------------------------------------------*/
                    Tenant::forgetCurrent();
                    if ($customer = Tenant::Where('tenant_id', $subscription->subscription_customerid)->first()) {
                        try {
                            //swicth to this tenants DB
                            $customer->makeCurrent();

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
                            Log::error("webhook - [Subscription-activated] - error trying to set the tenants database as [active] - error: " . $e->getMessage(), ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id' => $webhook->subscription_id, 'tenant_id' => $subscription->subscription_customerid]);
                            $webhook->update([
                                'webhooks_status' => 'failed',
                                'webhooks_comment' => "unable to update the tenant database to status [active] - tenant id ($subscription->subscription_customerid)",
                            ]);
                            continue;
                        }
                    }
                } else {
                    Log::info("webhook - [Subscription-activated] - unable to find a corresnding subscription ($webhook->webhooks_gateway_reference) in the landlord database] - will now exit", ['process' => '[landlord-cronjob][subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhook]);
                }

                //mark webhook cronjob as done
                $webhook->update([
                    'webhooks_status' => 'completed',
                ]);

            }

        }

    }
}