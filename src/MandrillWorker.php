<?php
/**
 * Author: Paul Bardack paul.bardack@gmail.com http://paulbardack.com
 * Date: 30.10.15
 * Time: 16:37
 */

namespace Drunken;

class MandrillWorker extends AbstractWorker
{
    public function doThisJob(array $data)
    {
        $required_data = [
            'mandrill_key',
            'subject',
            'from_email',
            'from_name',
            'reply_to',
            'email',
            'email_data',
            'template_name'
        ];
        foreach ($required_data as $key) {
            if (!array_key_exists($key, $data)) {
                return "data should consist key '$key'";
            }
        }

        if (!is_array($data['email_data'])) {
            return "email_data should be an array";
        } else {
            try {
                $global_merge_vars = [];
                foreach ($data['email_data'] as $key => $value) {
                    $global_merge_vars[] = [
                        "name" => $key,
                        "content" => $value
                    ];
                }

                $mandrill = new \Mandrill($data['mandrill_key']);
                $message = [
                    'subject' => $data['subject'],
                    'from_email' => $data['from_email'],
                    'from_name' => $data['from_name'],
                    "to" => [
                        [
                            'email' => $data['email'],
                            "type" => "to"
                        ]
                    ],
                    "headers" => [
                        "Reply-To" => $data['reply_to']
                    ],
                    "merge" => true,
                    "merge_language" => "handlebars",
                    "global_merge_vars" => $global_merge_vars
                ];
                $result = $mandrill->messages->sendTemplate($data['template_name'], [], $message);
                
                if (is_array($result) and isset($result[0])) {
                    if ($result[0]['status'] == 'sent' or $result[0]['status'] == 'queued') {
                        return true;
                    } else {
                        return $result[0];
                    }
                } else {
                    return [
                        'error' => 'bad response from mandrill',
                        'response' => $result
                    ];
                }

            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
