<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the template controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Landlord\Subscriptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdate extends FormRequest {

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
            'package_id.required' => __('lang.package') . ' - ' . __('lang.is_required'),
            'package_id.exists' => __('lang.package') . ' - ' . __('lang.could_not_be_found'),
            'billing_cycle.required' => __('lang.billing_cycle') . ' - ' . __('lang.is_required'),
            'subscription_date_next_renewal.required_if' => __('lang.initial_payment_due_date') . ' - ' . __('lang.is_required'),
            'subscription_payment_method.required' => __('lang.payment_method') . ' - ' . __('lang.is_required'),
            'trial_end_date.required_if' => __('lang.trial_end_date') . ' - ' . __('lang.is_required'),
        ];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        //initialize
        $rules = [];

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'package_id' => [
                'required',
                Rule::exists('packages', 'package_id'),
            ],
            'billing_cycle' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '' && !in_array($value, ['monthly', 'yearly'])) {
                        return $fail(__('lang.billing_cycle') . ' - ' . __('lang.is_invalid'));
                    }
                },
            ],
            'subscription_payment_method' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '' && !in_array($value, ['automatic', 'offline'])) {
                        return $fail(__('lang.payment_method') . ' - ' . __('lang.is_invalid'));
                    }
                },
                function ($attribute, $value, $fail) {
                    if ($value == 'offline' && config('system.settings_offline_payments_status') == 'disabled') {
                        return $fail(__('lang.first_enable_offline_payments'));
                    }
                },
            ],
            'free_trial_days' => [
                'required_if:free_trial,yes',
                function ($attribute, $value, $fail) {
                    if (request('free_trial') == 'yes') {
                        if ($value <= 0) {
                            return $fail(__('lang.free_trial_duration') . ' - ' . __('lang.must_be_greater_than_zero'));
                        }
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