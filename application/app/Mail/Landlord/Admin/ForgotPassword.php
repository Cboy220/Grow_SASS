<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [forgot password] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail\Landlord\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ForgotPassword extends Mailable {
    use Queueable;

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
    public function __construct($user = []) {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        //email template
        if (!$template = \App\Models\Landlord\EmailTemplate::on('landlord')->Where('emailtemplate_name', 'Reset Password Request')->first()) {
            return false;
        }

        //validate
        if (!$this->user instanceof \App\Models\User) {
            return;
        }

        //only active templates
        if ($template->emailtemplate_status != 'enabled') {
            return false;
        }

        //get common email variables
        $payload = config('mail.data');

        //set template variables
        $payload += [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'password_reset_link' => url('app-admin/resetpassword?token=' . $this->user->forgot_password_token),
        ];

        //log email
        $queue = new \App\Models\Landlord\EmailLog();
        $queue->setConnection('landlord');
        $queue->emaillog_email = $this->user->email;
        $queue->emaillog_subject = $template->parse('subject', $payload);
        $queue->emaillog_body = $template->parse('body', $payload);
        $queue->save();

        //get the temple
        return $this->from(config('system.settings_email_from_address'), config('system.settings_email_from_name'))
            ->subject($template->parse('subject', $payload))
            ->with([
                'content' => $template->parse('body', $payload),
            ])
            ->view('pages.emails.template');

    }
}
