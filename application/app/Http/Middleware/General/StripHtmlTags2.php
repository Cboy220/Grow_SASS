<?php

namespace App\Http\Middleware\General;

use Closure;

class StripHtmlTagsX {
    protected $fields_to_strip = [];

    public function __construct() {
        $this->fields_to_strip = $this->getFieldsToStrip();
    }

    public function handle($request, Closure $next) {
        // Only apply the middleware to POST and PUT requests
        if ($request->isMethod('post') || $request->isMethod('put')) {

            //get only the fields that are in our array
            $input = $request->only($this->fields_to_strip);

            // Loop through each input fields in the request and strip the html
            foreach ($input as $key => $value) {
                $input[$key] = strip_tags($value);
            }

            // Merge the modified input with the input from the original request
            $request->merge($input);
        }

        return $next($request);
    }

    /**
     * Returns an array of input fields that should have their HTML tags stripped.
     *
     * @return array
     */
    protected function getFieldsToStrip() {
        return [
            'account_name',
            'billing_cycle',
            'changed_package_id',
            'contact_email',
            'contact_name',
            'email',
            'email_address',
            'emailtemplate_status',
            'emailtemplate_subject',
            'expiry_date',
            'faq_title',
            'first_name',
            'free_trial',
            'free_trial_days',
            'frontend_data_1',
            'frontend_data_2',
            'frontend_data_10',
            'frontend_data_11',
            'frontend_data_12',
            'frontend_data_13',
            'frontend_data_3',
            'frontend_data_4',
            'frontend_data_5',
            'frontend_data_6',
            'frontend_data_7',
            'frontend_data_8',
            'frontend_data_9',
            'full_name',
            'last_name',
            'link_internal',
            'link_manual',
            'link_target',
            'more_information',
            'package_amount_monthly',
            'package_amount_yearly',
            'package_featured',
            'package_limits_clients',
            'package_limits_projects',
            'package_limits_team',
            'package_module_contracts',
            'package_module_estimates',
            'package_module_expense',
            'package_module_invoices',
            'package_module_knowledgebase',
            'package_module_leads',
            'package_module_proposals',
            'package_module_reminders',
            'package_module_subscriptions',
            'package_module_tasks',
            'package_module_tickets',
            'package_module_calendar',
            'package_module_timetracking',
            'package_name',
            'package_subscription_options',
            'password',
            'payment_amount',
            'payment_date',
            'payment_gateway',
            'payment_tenant_id',
            'payment_transaction_id',
            'plan',
            'search_query',
            'settings_company_address_line_1',
            'settings_company_city',
            'settings_company_country',
            'settings_company_name',
            'settings_company_state',
            'settings_company_telephone',
            'settings_company_zipcode',
            'settings_email_from_address',
            'settings_email_from_name',
            'settings_email_server_type',
            'settings_email_smtp_encryption',
            'settings_email_smtp_host',
            'settings_email_smtp_password',
            'settings_email_smtp_port',
            'settings_email_smtp_username',
            'settings_free_trial',
            'settings_free_trial_days',
            'settings_frontend_status',
            'settings_gateways_default_product_description',
            'settings_gateways_default_product_name',
            'settings_modules_messages',
            'settings_offline_payments_display_name',
            'settings_offline_payments_status',
            'settings_paypal_display_name',
            'settings_paypal_live_client_id',
            'settings_paypal_live_secret_key',
            'settings_paypal_mode',
            'settings_paypal_sandbox_client_id',
            'settings_paypal_sandbox_secret_key',
            'settings_paypal_status',
            'settings_purchase_code',
            'settings_stripe_display_name',
            'settings_stripe_ipn_url',
            'settings_stripe_public_key',
            'settings_stripe_reset_plans',
            'settings_stripe_secret_key',
            'settings_stripe_status',
            'settings_stripe_webhooks_key',
            'settings_system_currency_code',
            'settings_system_currency_position',
            'settings_system_currency_symbol',
            'settings_system_date_format',
            'settings_system_datepicker_format',
            'settings_system_decimal_separator',
            'settings_system_renewal_grace_period',
            'settings_system_thousand_separator',
            'settings_system_timezone',
            'subscription_payment_method',
            'subscription_renewal_date',
            'subscription_renewal_options',
            'subscription_renewal_period',
            'subscription_status',
            'settings_code_meta_title',
            'settings_code_meta_description',
            'settings_code_head',
            'settings_code_meta_description',
            
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
            'foo',
        ];
    }
}
