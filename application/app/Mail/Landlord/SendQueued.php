<?php

/** --------------------------------------------------------------------------------
 * SendQueued
 * Send emails that are stored in the email queue (database)
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail\Landlord;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;

class SendQueued extends Mailable {
    use Queueable, SerializesModels;

    public $data;

    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $attachment = '') {
        //
        $this->data = $data;
        $this->attachment = $attachment;
    }

    /**
     * Nextloop: This will send the email that has been saved in the database (as sent by the cronjob)
     *
     * @return $this
     */
    public function build() {

        //validate
        if (!$this->data instanceof \App\Models\Landlord\EmailQueue) {
            return;
        }

        //[attachement] send emil with an attahments
        $email = $this->from(config('system.settings_email_from_address'), config('system.settings_email_from_name'))
            ->subject($this->data->emailqueue_subject)
            ->with([
                'content' => $this->data->emailqueue_message,
            ])
            ->view('landlord.emails.template');

        //attachments
        if ($this->data->emailqueue_attachments != '') {
            //convert to array
            $attachments = json_decode($this->data->emailqueue_attachments, true);
            //loop and attach
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    $file_path = BASE_DIR . "/storage/files/".$attachment['directory']."/".$attachment['filename'];
                    Log::info("attachment: $file_path");
                    if (file_exists($file_path)) {
                        $email->attach($file_path);
                    }
                }
            }
        }

        //send email
        return $email;
    }
}
