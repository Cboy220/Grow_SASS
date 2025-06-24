<?php

/** --------------------------------------------------------------------------------
 * [template]
 * This classes renders the [new email] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail\Landlord\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class NewCustomerSignUp extends Mailable {
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
    public $user;

    public $emailerrepo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user = [], $data = [], $obj = []) {

        $this->data = $data;
        $this->user = $user;
        $this->obj = $obj;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        //email template
        if (!$template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'New Customer Sign Up')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Landlord\Package || !$this->user instanceof \App\Models\User) {
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
            'name' => $this->user->first_name,
            'customer_name' => $this->data['customer_name'],
            'account_url' => 'https://' . $this->data['account_url'],
            'account_name' => $this->data['account_name'],
            'plan_name' => $this->obj->package_name,
            'button_url' => url('app-admin/customers/' . $this->data['customer_id']),
        ];

        //save in the database queue
        $queue = new \App\Models\Landlord\EmailQueue();
        $queue->setConnection('landlord');
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->save();
    }
}