<?php
/**
 * Author: Paul Bardack paul.bardack@gmail.com http://paulbardack.com
 * Date: 30.10.15
 * Time: 16:29
 */

namespace App;

use Drunken\Manager;
use Monolog\Logger;

class Mandrill
{
    private $mandrill_key;

    public function __construct(Manager $drunken, Logger $log, $mandrill_key)
    {
        $this->log = $log;
        $this->drunken = $drunken;
        $this->mandrill_key = $mandrill_key;
    }

    public function send(
        $email,
        $template,
        $subject,
        $email_data,
        $from_email,
        $from_name,
        $reply_to,
        array $attachments = [],
        $priority = 0,
        $expiresAt = null,
        $ignoreFieldsForDrunkenUniqueHash = null
    ) {
        $data = [
            'mandrill_key' => $this->mandrill_key,
            'subject' => $subject,
            'from_email' => $from_email,
            'from_name' => $from_name,
            'reply_to' => $reply_to,
            'email' => $email,
            'email_data' => $email_data,
            'template_name' => $template,
        ];
        if ($attachments) {
            $data['attachments'] = $attachments;
        }
        try {
            $result = $this->drunken->add('Mandrill', $data, $priority, $expiresAt, $ignoreFieldsForDrunkenUniqueHash);
            if ($result['ok'] != 1) {
                $this->log->addError(sprintf(
                    'Cannot save MandrillWorker task into mongo. Template: "%s", data: "%s"',
                    $template,
                    var_export($data, true)
                ));
            }
        } catch (\Exception $e) {
            $this->log->addError(sprintf(
                'Failed prepare mail data for MandrillWorker. Template: "%s", data: "%s". Exception: %s',
                $template,
                var_export($data, true),
                $e->__toString()
            ));
        }
    }
}
