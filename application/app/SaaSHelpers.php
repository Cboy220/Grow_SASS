<?php

/**
 * get the correct [HOST] depending on the 'DB_METHOD' set in .env
 * @param string $url
 * @return string
 */
function env_db_host() {

    //get the right value
    switch (env('DB_METHOD')) {
    case 'mysql_user':
        return env('DB_METHOD_MYSQL_HOST');
    case 'cpanel':
        return env('DB_METHOD_CPANEL_HOST');
    case 'plesk':
        return env('DB_METHOD_PLESK_HOST');
    default:
        return 'localhost';
    }
}

/**
 * get the correct [PORT] depending on the 'DB_METHOD' set in .env
 * @param string $url
 * @return string
 */
function env_db_port() {

    //get the right value
    switch (env('DB_METHOD')) {
    case 'mysql_user':
        return env('DB_METHOD_MYSQL_PORT');
    case 'cpanel':
        return env('DB_METHOD_CPANEL_PORT');
    case 'plesk':
        return env('DB_METHOD_PLESK_PORT');
    default:
        return '3306';
    }
}

/**
 * get the correct [USER] depending on the 'DB_METHOD' set in .env
 * @param string $url
 * @return string
 */
function env_db_user() {

    //get the right value
    switch (env('DB_METHOD')) {
    case 'mysql_user':
        return env('DB_METHOD_MYSQL_USER');
    case 'cpanel':
        return env('DB_METHOD_CPANEL_USER');
    case 'plesk':
        return env('DB_METHOD_PLESK_USERNAME');
    default:
        return 'undefined';
    }
}

/**
 * get the correct [PASSWORD] depending on the 'DB_METHOD' set in .env
 * @param string $url
 * @return string
 */
function env_db_password() {

    //get the right value
    switch (env('DB_METHOD')) {
    case 'mysql_user':
        return env('DB_METHOD_MYSQL_PASSWORD');
    case 'cpanel':
        return env('DB_METHOD_CPANEL_PASSWORD');
    case 'plesk':
        return env('DB_METHOD_PLESK_PASSWORD');
    default:
        return 'undefined';
    }
}

/**
 * @param string $status the status of the toggled form section (show or hide)
 * @return string
 */
function saasToggleFormStatus($status = '') {
    if (!in_array($status, ['enabled'])) {
        return 'hidden';
    }
}

/**
 * highlight archived button
 * @return string
 */
function saasToggleArchivedPackages() {
    if (request('package_status') == 'archived') {
        return 'active';
    }
}

/**
 * @param string $status the status of the toggled form section (show or hide)
 * @return string
 */
function saasToggleFormFreePlan($status = '') {
    if ($status == 'yes') {
        return 'hidden';
    }
}

/**
 * show or hide the monthly and yearly billing options, in the packages modal
 * @return string $type monthly|yearly
 * @return string $option monthly|yearly|both|free
 */
function saasToggleSubscriptionOption($type = '', $option = '') {

    if ($option == 'free') {
        return 'hidden';
    }

}

/**
 * bootstrap class, based on value
 * @param string $status the status of the ticket
 * @param string $type lable|background
 * @return string bootstrap label class
 */
function runtimeCustomerStatusColors($status = '') {

    //default colour
    $colour = 'default';

    switch ($status) {

    case 'awaiting-payment':
        $colour = 'warning';
        break;

    case 'cancelled':
        $colour = 'danger';
        break;

    case 'active':
    case 'free-trial':
        $colour = 'success';
        break;

    case 'failed':
        $colour = 'danger';
        break;

    }

    //return the css
    return bootstrapColors($colour, 'label');
}

/**
 * bootstrap class, based on value
 * @param string $status the status of the ticket
 * @param string $type lable|background
 * @return string bootstrap label class
 */
function runtimeCustomerStatusLang($status = '') {

    //default colour

    switch ($status) {

    case 'awaiting-payment':
        $status = __('lang.awaiting_payment');
        break;

    case 'failed':
        $status = __('lang.payment_failed');
        break;

    case 'free-trial':
        $status = __('lang.free_trial');
        break;

    case 'cancelled':
        $status = __('lang.cancelled');
        break;

    case 'active':
        $status = __('lang.active');
        break;
    }

    return $status;
}

/**
 * bootstrap class, based on value
 * @param string $status the status of the ticket
 * @param string $type lable|background
 * @return string bootstrap label class
 */
function runtimeSubscriptionStatusColors($status = '') {

    //default colour
    $colour = 'default';

    switch ($status) {

    case 'awaiting-payment':
        $colour = 'warning';
        break;

    case 'cancelled':
        $colour = 'danger';
        break;

    case 'active':
    case 'free-trial':
        $colour = 'success';
        break;

    case 'failed':
        $colour = 'danger';
        break;
    }

    //return the css
    return bootstrapColors($colour, 'label');
}

/**
 * bootstrap class, based on value
 * @param string $status the status of the ticket
 * @param string $type lable|background
 * @return string bootstrap label class
 */
function runtimeSubscriptionStatusLang($status = '') {

    //default colour

    switch ($status) {

    case 'awaiting-payment':
        $status = __('lang.awaiting_payment');
        break;

    case 'failed':
        $status = __('lang.payment_failed');
        break;

    case 'free-trial':
        $status = __('lang.free_trial');
        break;

    case 'cancelled':
        $status = __('lang.cancelled');
        break;

    case 'active':
        $status = __('lang.active');
        break;
    }

    return $status;
}

/**
 * return the url to logo
 * @return string
 */
function runtimeLogoFrontEnd() {
    $logo = config('system.settings_system_logo_frontend_name');
    $version = config('system.settings_system_logo_versioning');
    return url("storage/logos/app/$logo?v=$version");
}

/**
 * @param bool $menu if left inner menu is enabled
 * @return string
 */
function runtimeLeftInnerMenu($menu = false) {
    if ($menu == true) {
        return 'has_left_inner_menu';
    }
}

/**
 * check if a payment gateway (flash message) exists or show general error message
 * @return string
 */
function getFlashErrorMessage() {
    if (request()->session()->has('flash-error-message')) {
        //store message
        $message = session('flash-error-message');
        //delete the flash message
        request()->session()->forget('flash-error-message');
        //return message
        return $message;
    } else {
        return __('lang.request_could_not_be_completed');
    }
}

/**
 * disabling checkbox
 * @param string $var indenty of the checkbox
 * @return string css
 */
function runtimeTrialDaysDisabled($var = '') {
    if ($var == 'no') {
        return 'disabled';
    }
}

/**
 * get the base domain from the current URL
 * this function is used mostly during setup
 * example https://foo.domain.com/setup will return 'domain.com'
 * @return string css
 */
function getBaseDomainName() {
    $url = url()->current();
    $base_domain = parse_url($url, PHP_URL_HOST);
    return $base_domain;
}

/**
 * Configure various settings that are not available when cronjobs run (because landlord config is set in middleware)
 * This is not needed for teneant cronjobs because their cofig is set via service providers
 * @return array
 */
function runtimeLandlordCronConfig() {

    //defaults
    $email_signature = '';
    $email_footer = '';

    //get settings
    $settings = \App\Models\Landlord\Settings::on('landlord')->Where('settings_id', 'default')->first();

    //currency symbol position setting
    if ($settings->settings_system_currency_position == 'left') {
        $settings['currency_symbol_left'] = $settings->settings_system_currency_symbol;
        $settings['currency_symbol_right'] = '';
    } else {
        $settings['currency_symbol_right'] = $settings->settings_system_currency_symbol;
        $settings['currency_symbol_left'] = '';
    }

    //get email signature
    if ($template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'Email Signature')->first()) {
        $email_signature = $template->emailtemplate_body;
    }

    //get email footer
    if ($template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'Email Footer')->first()) {
        $email_footer = $template->emailtemplate_body;
    }

    //set configs
    config([
        'system' => $settings,
    ]);

    //save to config
    config([
        'mail.default' => $settings->settings_email_server_type,
        'mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => $settings->settings_email_smtp_host,
            'port' => $settings->settings_email_smtp_port,
            'encryption' => ($settings->settings_email_smtp_encryption == 'none') ? '' : $settings->settings_email_smtp_encryption,
            'username' => $settings->settings_email_smtp_username,
            'password' => $settings->settings_email_smtp_password,
            'url' => env('MAIL_URL'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        'mail.data' => [
            'company_name' => config('system.settings_company_name'),
            'todays_date' => runtimeDate(date('Y-m-d')),
            'email_signature' => $email_signature,
            'email_footer' => $email_footer,
            'dashboard_url' => url('/'),
        ],
    ]);

}

/**
 * return the image and its path as a css style
 * @return string css
 */
function dynamicStyleBackgroundImage($dir = "", $file = "") {

    return "style=\"background-image: url(storage/frontend/$dir/$file)\"";

}

/**
 * toggle options when adding a new menulink
 */
function saasLinkTypeToggle($type = '', $option = '') {

    if ($type != $option) {
        return 'hidden';
    }
}

/**
 * toggle options when adding a new menulink
 */
function saasLinktarget($type = '') {

    if ($type == 'new_window') {
        return '_blank';
    } else {
        return '_self';
    }
}

/**
 * set the current subscription renewal cycle
 */
function runtimeRenewalPeriodSelector($cycle = '', $type = 'period') {

    if ($type == 'period') {
        switch ($cycle) {
        case 'weekly':
            return 'one_week_from_today';
        case 'monthly':
            return 'one_month_from_today';
        case 'yearly':
            return 'one_year_from_today';
        }
    }

    if ($type == 'date') {
        switch ($cycle) {
        case 'weekly':
            return \Carbon\Carbon::now()->addWeeks(1)->format('Y-m-d');
        case 'monthly':
            return \Carbon\Carbon::now()->addMonths(1)->format('Y-m-d');
        case 'yearly':
            return \Carbon\Carbon::now()->addYears(1)->format('Y-m-d');
        }
    }

}

/**
 * retun a cpanel url based on the current url
 */
function cpanelURL() {

    return str_replace('/:2083', ':2083', url(':2083'));

}

/**
 * retun a plesk domain
 */
function pleskDomain() {

    return request()->getHttpHost();

}

/**
 * retun a plesk url based on the current url
 */
function pleskURL() {

    return str_replace('/:8443', ':8443', url(':8443'));

}

/**
 * create a database name with a prefix (if provided)
 * @param string $prefix
 */
function databaseName($prefix = '') {

    $prefix = ($prefix != '') ? $prefix : 'growcrm_';

    //database name
    $num = time();
    return $prefix . strtolower(random_string(6)) . '_' . $num;

}

/**
 * create a database username
 */
function databaseUserName() {

    //database name
    $num = time();
    return 'crm_user_' . strtolower(random_string(9));

}

/**
 * create a mysql username
 * @param string $cpanel_username
 */
function cpanelMysqlUsername($cpanel_username = '') {

    //database name
    $num = time();
    return $cpanel_username . '_' . strtolower(random_string(6));

}

/**
 * validate a cpanel url
 * @param string $url
 */
function validateCpanelURL($url = '') {

    //is it a URL

    //make sure this is the port (:2083) type url
    if (!preg_match('/:2083/', $url)) {
        return false;
    }

    return true;

}

/**
 * remove any extra characters and params in a cpanel url
 * @param string $url cpanel url
 */
function cpanelCleanURL($url = '') {

    //clean url - remove any params after :2083
    $url = preg_replace('/\:2083(.*)/', ':2083', $url);
    $url = rtrim($url);

    //ensure its SSL (none ssl urls cause errors)
    $url = str_replace('http://', 'https://', $url);

    return $url;
}

/**
 * remove any extra characters and params in a cpanel url
 * @param string $url cpanel url
 */
function pleskCleanURL($url = '') {

    //clean url - remove any params after :8443
    $url = preg_replace('/\:8443(.*)/', ':8443', $url);
    $url = rtrim($url);

    //ensure its SSL (none ssl urls cause errors)
    $url = str_replace('http://', 'https://', $url);

    return $url;
}

/**
 * remove any extra characters and params in a url
 * @param string $url
 */
function cleanURLDomain($url = 'http://frontend.nextloop.net/') {

    $url = str_replace(['http://', 'https://', 'www.'], '', $url);
    $url = rtrim($url, '/');

    return $url;

}

/**
 * return a formatted number (php number_format) (1,230.00)
 * @param string $number
 * @return string formatted number
 */
function runtimeNumberFormatPricing($number = '') {

    //validation
    if (!is_numeric($number)) {
        $number = 0;
    }

    //decimal separator
    $decimal = runtimeCurrrencySeperators(config('system.settings_system_decimal_separator'));

    //thousand separator
    $thousands = runtimeCurrrencySeperators(config('system.settings_system_thousand_separator'));

    //if number has not decimal
    if (floor($number) == $number) {
        return number_format(floatval($number), 0, $decimal, $thousands);
    }

    //format the number
    return number_format(floatval($number), 2, $decimal, $thousands);
}

/**
 * return a formtted value with a currency symbol ($1,230.00)
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 * @return string css setting
 */
function runtimeMoneyFormatPricing($number = '', $span_id = "") {

    $number = runtimeNumberFormatPricing($number);

    //are we wrapping in a span
    if ($span_id != '') {
        $number = '<span id="' . $span_id . '">' . $number . '</span>';
    }

    return config('system.currency_symbol_left') . $number . config('system.currency_symbol_right');
}

/**
 * check if the value is unlimited
 * @param string $number current users setting
 * @return mixed number or 'unlimted'
 */
function runtimeCheckUnlimited($number = '') {

    if ($number == -1) {
        return __('lang.unlimited');
    }

    return $number;
}

/**
 * add css class for the login button
 * @param string $url
 * @return string
 */
function runtimeFrontendMenuSignup($url = '') {

    if ($url == '/account/signup') {
        return 'frontent-signup-button';
    }

}

/**
 * Return friendly name of the database creation method
 * @param string $type
 * @return string
 */
function db_creation_method($type = '') {

    //get the right value
    switch ($type) {
    case 'mysql_user':
        return __('lang.mysql_root_user');
    case 'cpanel':
        return 'Cpanel API';
    case 'plesk':
        return 'Plesk API';
    default:
        return '---';
    }
}

/**
 * return a formtted value with a currency symbol ($1,230.00). Taking the symbol and settings from the admin settings
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 */
function runtimeMoneyFormatSaaS($number = '', $span_id = "") {

    $number = runtimeNumberFormat($number);

    //are we wrapping in a span
    if ($span_id != '') {
        $number = '<span id="' . $span_id . '">' . $number . '</span>';
    }

    //get admin payment gateway settings
    if (!$landlord_settings = DB::connection('landlord')
        ->table('settings')
        ->where('settings_id', 'default')
        ->first()) {
        Log::critical("setting currrency symbol failed - unable to fetch the landlord settimgs", ['process' => '[SaaSHelper]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $settings->settings_saas_tenant_id]);
        return $number;
    }

    //set the position for the currency symbol
    if ($landlord_settings->settings_system_currency_position == 'left') {
        return $landlord_settings->settings_system_currency_symbol . $number;
    } else {
        return $number . $landlord_settings->settings_system_currency_symbol;

    }
}

/**
 * validate a plesk url
 * @param string $url
 */
function validatePleskURL($url = '') {

    //make sure this is the port (:8443) type url
    if (!preg_match('/:8443/', $url)) {
        return false;
    }

    return true;

}

/**
 * generate secure password that has a combination of upper, lower and special characters
 * [example] U7g@f5#$dD
 * @return string
 */
function generateSecurePassword($length = 12) {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $specialChars = '@#$_';
    $allChars = $uppercase . $lowercase . $numbers . $specialChars;
    $password = '';

    // Ensure at least one uppercase letter, one lowercase letter, one number, and one special character
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $numbers[rand(0, strlen($numbers) - 1)];
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];

    $remainingLength = $length - 4;
    $characterCount = strlen($allChars);
    for ($i = 0; $i < $remainingLength; $i++) {
        $index = rand(0, $characterCount - 1);
        $password .= $allChars[$index];
    }

    // Shuffle the password to randomize the order of characters
    $password = str_shuffle($password);

    return $password;
}

/**
 * precheck the 'show page title option'
 * @param string $url
 */
function editPageShowTitle($mode = '', $status = '') {

    if ($mode == 'create' || $status == 'yes') {
        return 'checked';
    }

}

/**
 * precheck the 'show page title option'
 * @param string $url
 */
function editPagePermalinkStatus($mode = '') {

    if ($mode == 'create') {
        return 'disabled';
    }

}

/**
 * create a slug from posted permalink (sanity)
 * @param string $input
 */
function pagePermalinkSlug($input) {
    // Convert to lowercase
    $slug = strtolower($input);
    // Replace whitespace with dashes
    $slug = preg_replace('/\s+/', '-', $slug);
    // Remove non-alphanumeric characters except dashes
    $slug = preg_replace('/[^\w-]+/', '', $slug);
    // Replace multiple dashes with a single dash
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}