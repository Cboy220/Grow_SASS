<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the template controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Landlord\Packages;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateValidation extends FormRequest {

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
            'package_name.required' => __('lang.package_name') . ' - ' . __('lang.is_required'),
            'package_limits_clients.required' => __('lang.maximum_clients') . ' - ' . __('lang.is_required'),
            'package_limits_projects.required' => __('lang.maximum_projects') . ' - ' . __('lang.is_required'),
            'package_limits_team.required' => __('lang.maximum_employees') . ' - ' . __('lang.is_required'),
            'package_amount_monthly.required_if' => __('lang.monthly_price') . ' - ' . __('lang.is_required'),
            'package_amount_yearly.required_if' => __('lang.yearly_price') . ' - ' . __('lang.is_required'),
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
            'package_name' => [
                'required',
            ],
            'package_limits_clients' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '' && $value < -1) {
                        return $fail(__('lang.maximum_clients') . ' - ' . __('lang.is_invalid'));
                    }
                },
            ],
            'package_limits_projects' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '' && $value < -1) {
                        return $fail(__('lang.maximum_projects') . ' - ' . __('lang.is_invalid'));
                    }
                },
            ],
            'package_limits_team' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '' && $value < -1) {
                        return $fail(__('lang.maximum_employees') . ' - ' . __('lang.is_invalid'));
                    }
                },
            ],
            'package_amount_monthly' => [
                'required_if:package_subscription_options,paid',
                function ($attribute, $value, $fail) {
                    if ($value != '' && $value <= 0) {
                        return $fail(__('lang.monthly_price') . ' - ' . __('lang.must_be_greater_than_zero'));
                    }
                },
            ],
            'package_amount_yearly' => [
                'required_if:package_subscription_options,paid',
                function ($attribute, $value, $fail) {
                    if ($value != '' && $value <= 0) {
                        return $fail(__('lang.yearly_price') . ' - ' . __('lang.must_be_greater_than_zero'));
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
