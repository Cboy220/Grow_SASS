<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the template controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Landlord\Customers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateValidation extends FormRequest {

    //use App\Http\Requests\Foo\TemplateValidation;
    //function update(TemplateValidation $request,

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
            'full_name.required' => __('lang.first_name') . ' - ' . __('lang.is_required'),
            'email_address.required' => __('lang.email') . ' - ' . __('lang.is_required'),
            'email_address.unique' => __('lang.email') . ' - ' . __('lang.already_exists'),
            'account_name.required' => __('lang.account_name') . ' - ' . __('lang.is_required'),
            'account_name.unique' => __('lang.account_url') . ' - ' . __('lang.already_exists'),
        ];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        //Common to all of them
        $rules = [
            'full_name' => [
                'required',
            ],
            'email_address' => [
                'required',
                Rule::unique('tenants', 'tenant_email')->ignore(request()->route('customer'), 'tenant_id'),
            ],
            'account_name' => [
                'required',
                Rule::unique('tenants', 'subdomain')->ignore(request()->route('customer'), 'tenant_id'),
                function ($attribute, $value, $fail) {
                    //validate domain name characters (a-z A-Z 0-9 . -)
                    if (!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9-._]*[a-zA-Z0-9]+$/', $value)) {
                        return $fail(__('lang.account_url_is_invalid'));
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
        ];

        /**-------------------------------------------------------
         * [create] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST') {

        }

        /**-------------------------------------------------------
         * [update] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'PUT') {

        }

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