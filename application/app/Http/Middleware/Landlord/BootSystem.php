<?php

/** ---------------------------------------------------------------------------------------------------------------
 * [NEXTLOOPS]
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
use Illuminate\Support\Facades\Storage;

class BootSystem {

    public $settings;

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //system sanity check
        $this->sanityCheck();

        //setup the landloard database
        $this->setupDatabase();

        //default settings
        $this->systemSettings();

        //customer defaults
        $this->customerDefaults();

        //set the backend theme
        $this->setBackEndTheme();

        //set the frontend theme
        $this->setFrontEndTheme();

        //set the language
        $this->setLanguage();

        return $next($request);

    }

    /**
     * system sanity check
     */
    private function sanityCheck() {
        //CHECK IF SETUP COMPLETED - REDIRECT TO SETUP PAGE
        if (env('SETUP_STATUS') != 'COMPLETED') {
            if (request()->route()->getName() != 'setup') {
                //redirect to setup page
                return redirect()->route('setup');
            }
        }

        //TRYING TO ACCESS SETUP WHEN ITS ALREADY BEEN COMPLETED
        if (env('SETUP_STATUS') == 'COMPLETED') {
            if (request()->route()->getName() == 'setup') {
                //redirect to setup page
                return redirect()->route('home');
            }
        }

    }

    /**
     * savethe system settings
     */
    public function setupDatabase() {

        //set the landloard database connection
        config()->set('database.default', 'landlord');

    }

    /**
     * save the customer default settings
     */
    public function customerDefaults() {

        //get landlord settings
        $defaults = \App\Models\Landlord\Defaults::Where('defaults_id', 1)->first();
       
        config(['customer_defaults' => $defaults]);
    }

    /**
     * savethe system settings
     */
    public function systemSettings() {

        //get the general settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();
        $this->settings = $settings;

        //set timezone
        date_default_timezone_set($settings->settings_system_timezone);

        //currency symbol position setting
        if ($settings->settings_system_currency_position == 'left') {
            $settings['currency_symbol_left'] = $settings->settings_system_currency_symbol;
            $settings['currency_symbol_right'] = '';
        } else {
            $settings['currency_symbol_right'] = $settings->settings_system_currency_symbol;
            $settings['currency_symbol_left'] = '';
        }

        //cronjob path
        $settings['cronjob_path'] = '/usr/local/bin/php ' . BASE_DIR . '/application/artisan schedule:run >> /dev/null 2>&1';

        //javascript file versioning to avoid caching when making updates
        $settings['versioning'] = $settings->settings_system_javascript_versioning;

        //count tenant with pending email settings
        $settings['count_tenant_email_config_status'] = \App\Models\Landlord\Tenant::where('tenant_email_config_status', 'pending')->count();

        //save to config
        config(['system' => $this->settings]);

        //cronjob path
        $cron_path = '/usr/local/bin/php ' . BASE_DIR . '/application/artisan schedule:run >> /dev/null 2>&1';
        $cron_path = str_replace('\\', '/', $cron_path);
        $cron_path_2 = '/usr/local/bin/php ' . BASE_DIR . '/application/artisan tenants:artisan schedule:run >> /dev/null 2>&1';
        $cron_path_2 = str_replace('\\', '/', $cron_path_2);

        config([
            'cronjob_path' => $cron_path,
            'cronjob_path_2' => $cron_path_2,
        ]);

        //recaptcha
        config([
            'recaptcha.api_site_key' => $settings->settings_captcha_api_site_key,
            'recaptcha.api_secret_key' => $settings->settings_captcha_api_secret_key,
        ]);

    }

    /**
     * set the theme
     */
    public function setBackEndTheme() {

        //do not run this for SETUP path
        if (env('SETUP_STATUS') != 'COMPLETED') {
            config([
                'theme.backend.selected_theme_css' => 'public/themes/default/css/style.css?v=1',
            ]);
        }

        //get all directories in themes folder
        $directories = Storage::disk('root')->directories('public/themes');

        //clean up directory names
        array_walk($directories, function (&$value, $key) {
            $value = str_replace('public/themes/', '', $value);
        });

        //check if default theme exists
        if (!in_array($this->settings->settings_theme_name, $directories)) {
            abort(409, __('lang.default_theme_not_found') . ' [' . runtimeThemeName($this->settings->settings_theme_name) . ']');
        }

        //check if css file exists
        if (!is_file(BASE_DIR . '/public/themes/' . $this->settings->settings_theme_name . '/css/style.css')) {
            abort(409, __('lang.selected_theme_is_invalid'));
        }

        //validate if the folders in the /public/themes/ directory have a style.css file
        $list = [];
        foreach ($directories as $directory) {
            if (is_file(BASE_DIR . "/public/themes/$directory/css/style.css")) {
                $list[] = $directory;
            }
        }

        //set global data (Frontend Theme)
        config([
            'theme.list' => $list,
            'theme.backend.selected_name' => $this->settings->settings_theme_name,
            //main css file
            'theme.backend.selected_theme_css' => 'public/themes/' . $this->settings->settings_theme_name . '/css/style.css?v=' . $this->settings->settings_system_javascript_versioning,
            //saas changes css file
            'theme.backend.selected_theme_saas_css' => 'public/themes/' . $this->settings->settings_theme_name . '/css/saas.css?v=1',
        ]);

    }

    /**
     * set the theme
     */
    public function setFrontEndTheme() {
        config([
            'theme.frontend.selected_theme_css' => 'public/themes/default/css/style.css?v=1',
        ]);
    }

    /**
     * set the language to be used by the app
     * @return void
     */
    private function setLanguage() {

        //set default system language first
        $lang = config('system.settings_system_language_default');
        if (file_exists(resource_path("lang/$lang"))) {
            //for use by javascripts - like tinymce (set in the header)
            request()->merge([
                'system_language' => $lang,
            ]);
            \App::setLocale($lang);
        } else {
            //for use by javascripts - like tinymce (set in the header)
            request()->merge([
                'system_language' => 'english',
            ]);
            //revert to english
            \App::setLocale('english');
        }

        //create list of languages
        $dir = BASE_DIR . '/application/resources/lang';
        $languages = array_diff(scandir($dir), array('..', '.'));
        request()->merge([
            'system_languages' => $languages,
        ]);
    }
}