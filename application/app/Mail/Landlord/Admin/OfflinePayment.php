<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [project created] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail\Landlord\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class OfflinePayment extends Mailable {
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

        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();

        //email template
        if (!$template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'New Offline Payment')->first()) {
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
            'customer_email' => $this->data['customer_email'],
            'customer_url' => $this->data['customer_url'],
            'button_url' => $this->data['button_url'],
            'customer_status' => $this->data['customer_status'],
        ];

        //save attachment data as json
        $attachments = [];
        $attachments[] = [
            'directory' => $this->data['directory'],
            'filename' => $this->data['filename'],
        ];
        $attachments = json_encode($attachments);

        //save in the database queue
        $queue = new \App\Models\Landlord\EmailQueue();
        $queue->setConnection('landlord');
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->emailqueue_attachments = $attachments;
        $queue->save();
    }
}
