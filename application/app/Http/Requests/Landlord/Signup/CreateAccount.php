<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the template controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Landlord\Signup;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAccount extends FormRequest {

    /**
     * we are checking authorised users via the middleware
     * so just retun true here
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * custom error messages for specific valdation checks
     * @optional
     * @return array
     */
    public function messages() {
        return [
            'full_name.required' => __('lang.full_name') . ' - ' . __('lang.is_required'),
            'email_address.required' => __('lang.email') . ' - ' . __('lang.is_required'),
            'email_address.email' => __('lang.email') . ' - ' . __('lang.is_invalid'),
            'email_address.unique' => __('lang.email') . ' - ' . __('lang.already_exists'),
            'password.required' => __('lang.password') . ' - ' . __('lang.is_required'),
            'password.min' => __('lang.password') . ' - ' . __('lang.password_must_be_x_characters'),
            'account_name.required' => __('lang.account_name') . ' - ' . __('lang.is_required'),
            'account_name.unique' => __('lang.account_name') . ' - ' . __('lang.is_already_taken'),
            'plan.required' => __('lang.plan') . ' - ' . __('lang.is_required'),
            'g-recaptcha-response.recaptcha' => __('lang.recaptcha_validation_error'),
        ];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        $rules = [];

        /**-------------------------------------------------------
         * recaptcha validation
         * ------------------------------------------------------*/
        if (config('system.settings_captcha_status') == 'enabled') {
            $rules += [
                'g-recaptcha-response' => 'recaptcha',
            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'full_name' => [
                'required',
            ],
            'email_address' => [
                'required',
                'email',
                Rule::unique('tenants', 'tenant_email'),
            ],
            'password' => [
                'required',
                'min:6',
            ],
            'account_name' => [
                'required',
                Rule::unique('tenants', 'subdomain'),
                function ($attribute, $value, $fail) {
                    if ($value != "" && !preg_match("/^[a-zA-Z0-9]*$/", $value)) {
                        return $fail(__('lang.must_only_contain_letters_numbers'));
                    }
                },
                //validate reserved words
                function ($attribute, $value, $fail) {
                    $settings = \App\Models\Landlord\Settings::on('landlord')->Where('settings_id', 'default')->first();
                    $reserved_words = explode(',', $settings->settings_reserved_words);
                    $reserved_words = array_map('trim', $reserved_words);
                    if (in_array($value, $reserved_words)) {
                        return $fail(__('lang.reserved_words_error'));
                    }
                },
            ],
            'plan' => [
                'required',
                function ($attribute, $value, $fail) {
                    $plan_id = str_replace(['monthly_', 'yearly_', 'free_'], '', $value);
                    if ($value != "" && \App\Models\Landlord\Package::Where('package_id', $plan_id)->Where('package_status', 'active')->doesntExist()) {
                        return $fail(__('lang.package_not_found'));
                    }
                },
            ],
        ];

        //validate
        return $rules;
    }

    /**
     * Deal with the errors - send messages to the frontend
     */
    public function failedValidation(Validator $validator) {

        $errors = $validator->errors();
        $messages = '';
        foreach ($errors->all() as $message) {
            $messages .= "<li>$message</li>";
        }

        abort(409, $messages);
    }
}