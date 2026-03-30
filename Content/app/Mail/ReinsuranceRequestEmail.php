<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReinsuranceRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $attachments_list;

    public function __construct(array $mailData, array $attachments_list = [])
    {
        $this->mailData         = $mailData;
        $this->attachments_list = $attachments_list;
    }

    public function build()
{
    $email = $this->from(
                $this->mailData['from'] ?? config('mail.from.address'),
                $this->mailData['from_name'] ?? config('mail.from.name')
            )
                  ->subject($this->mailData['subject'])
                  ->view('emails.reinsurance_request')
                  ->with([
                      'content' => $this->mailData['body'],
                      'records' => $this->mailData['records'] ?? [],
                  ]);

    foreach ($this->attachments_list as $att) {
        if (!empty($att['path']) && file_exists($att['path'])) {
            $email->attach($att['path'], [
                'as'   => $att['name'] ?? 'attachment',
                'mime' => mime_content_type($att['path']),
            ]);
        }
    }

    return $email;
}
}