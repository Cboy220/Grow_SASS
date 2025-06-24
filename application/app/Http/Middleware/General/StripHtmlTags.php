<?php

namespace App\Http\Middleware\General;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StripHtmlTags {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {

        //skip for specified 'named routes';
        if (in_array($request->route()->getName(), $this->excludedRoutes())) {
            return $next($request);
        }

        //skip for specified 'url patterns';
        if (in_array(request()->segment(1), $this->excludedURLs())) {
            return $next($request);
        }

        // Check if the request is a POST or PUT request
        if ($request->isMethod('post') || $request->isMethod('put')) {
            // Loop through each input field
            foreach ($request->input() as $key => $value) {
                // only do this for non-array fields (otherwise it will break many things like assigning tasks to users)
                if (!is_array($value)) {
                    // skip whitelisted fields and fields with names that start with 'html_'
                    if (!in_array($key, $this->whitelist()) && !Str::startsWith($key, 'html_')) {
                        $request->merge([$key => strip_tags($value)]);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Array of field names to whitelist
     *
     */
    private function whitelist() {

        return [
            'settings_offline_payments_details',
            'emailtemplate_body',
            'settings_code_head',
            'settings_code_body',
            'frontend_data_3',
            'html_frontend_data_3',
            'html_frontend_data_2',
            'html_frontend_data_5',
            'mod_specification_item_requirements',
            'mod_specification_item_note',
            'bill_terms',
            'bill_notes',
            'js_item_description',
            'client_description',
            'comment_text',
            'add_items_description',
            'doc_body',
            'bill_notes',
            'bill_terms',
            'expense_description',
            'bill_notes',
            'bill_terms',
            'item_description',
            'item_notes_estimatation',
            'item_notes_production',
            'product_task_description',
            'knowledgebase_text',
            'knowledgebase_embed_code',
            'checklist_text',
            'comment_text',
            'lead_mynotes',
            'lead_description',
            'message_text',
            'note_description',
            'payment_notes',
            'project_description',
            'reminder_notes',
            'settings_bank_details',
            'emailtemplate_body',
            'settings_estimates_default_terms_conditions',
            'webform_thankyou_message',
            'settings_invoices_default_terms_conditions',
            'kbcategory_description',
            'settings_theme_head',
            'settings_theme_body',
            'add_items_description',
            'webmail_template_body',
            'checklist_text',
            'task_mynotes',
            'task_description',
            'contract_template_body',
            'project_description',
            'proposal_template_body',
            'ticket_message',
            'ticketreply_text',
            'ticket_message',
            'ticket_message',
            'email_body',
            'blog_text',
            'foo_description',
            'settings_code_head',
            'settings_code_body',
            'faq_content',
            'frontend_data_3',
            'settings_code_meta_description',
            'emailtemplate_body',
            'settings_offline_payments_details',
            'contact_message',
        ];
    }

    /**
     * Array of named routes to whitelist
     *
     */
    private function excludedRoutes() {

        return [
            'webform.submit',
            'webform.save',
            'webhooks-stripe',
            'webhooks-paypal',
            'webhooks-mollie',
            'webhooks-razorpay',
            'webhooks-paystack',
        ];

    }

    /**
     * Array of excluded url's based on teh first segment
     * e.g. http://yourdomain.com/webhooks/foo/bar
     *
     */
    private function excludedURLs() {

        return [
            'webhooks',
            'api',
            'thankyou',
        ];

    }

}
