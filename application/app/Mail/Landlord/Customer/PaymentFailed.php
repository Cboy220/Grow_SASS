<?php

/** --------------------------------------------------------------------------------
 * [template]
 * This classes renders the [new email] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail\Landlord\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class PaymentFailed extends Mailable {
    use Queueable;

    /**
     * The data for merging into the email
     */
    public $data;

    /**
     * Model instance
     */
    public $obj;

    /**
     * Model instance
     */
    public $customer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customer = [], $data = [], $obj = []) {

        $this->data = $data;
        $this->customer = $customer;
        $this->obj = $obj;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        //email template
        if (!$template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'Renewal Payment Failed')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Landlord\Subscription || !$this->customer instanceof \App\Models\Landlord\Tenant) {
            return false;
        }

        //only active templates
        if ($template->emailtemplate_status != 'enabled') {
            return false;
        }

        //get common email variables
        $payload = config('mail.data');

        //set template variables
        $payload += [
            'name' => $this->customer->tenant_name,
            'account_url' => "https://" . $this->customer->domain,
            'plan_name' => $this->data['plan_name'] ?? '---',
            'subscription_id' => $this->obj->subscription_id,
            'amount' => runtimeMoneyFormat($this->obj->subscription_amount),
        ];

        //save in the database queue
        $queue = new \App\Models\Landlord\EmailQueue();
        $queue->setConnection('landlord');
        $queue->emailqueue_to = $this->customer->tenant_email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->save();
    }
}