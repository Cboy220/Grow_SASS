<?php

/** ---------------------------------------------------------------------------------------------------------------
 * The purpose of this middleware it to set fallback config values
 * for older versions of Grow CRM that are upgrading. Reason being that new
 * values in the file /config/settings.php will not exist for them (as settings files in not included in updates)
 *
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 * @revised    9 July 2021
 *--------------------------------------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Landlord;
use Closure;

class BootMail {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        //do not run this for SETUP path
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return $next($request);
        }

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //defaults
        $email_signature = '';
        $email_footer = '';

        //get email signature
        if ($template = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_name', 'Email Signature')->first()) {
            $email_signature = $template->emailtemplate_body;
        }

        //get email footer
        if ($template = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_name', 'Email Footer')->first()) {
            $email_footer = $template->emailtemplate_body;
        }

        //save to config
        config([
            'mail.driver' => $settings->settings_email_server_type,
            'mail.host' => $settings->settings_email_smtp_host,
            'mail.port' => $settings->settings_email_smtp_port,
            'mail.username' => $settings->settings_email_smtp_username,
            'mail.password' => $settings->settings_email_smtp_password,
            'mail.encryption' => ($settings->settings_email_smtp_encryption == 'none') ? '' : $settings->settings_email_smtp_encryption,
            'mail.data' => [
                'company_name' => config('system.settings_company_name'),
                'todays_date' => runtimeDate(date('Y-m-d')),
                'email_signature' => $email_signature,
                'email_footer' => $email_footer,
                'dashboard_url' => url('/'),
            ],
        ]);

        return $next($request);

    }

}