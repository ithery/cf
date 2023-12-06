<?php
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class CEmail_Driver_SesDriver extends CEmail_DriverAbstract {
    protected $ses;

    public function __construct(CEmail_Config $config) {
        parent::__construct($config);
        $this->ses = $this->createSesClient();
    }

    protected function createSesClient() {
        $sesConfig = [];
        $sesOptions = [];
        $key = $this->config->get('key', CF::config('vendor.ses.key')) ?: $this->config->getUsername();
        $secret = $this->config->get('secret', CF::config('vendor.ses.secret')) ?: $this->config->getPassword();
        $region = $this->config->get('region', $this->config->get('ses_region', CF::config('vendor.ses.secret'))) ?: 'ap-southeast-1';
        $sesConfig = [
            'credentials'=> [
                'key' => $key,
                'secret' => $secret,

            ],
            'region' => $region,
            'version' => 'latest',
        ];

        $sesClient = new SesClient($sesConfig, $sesOptions);

        return $sesClient;
    }

    public function send(array $to, $subject, $body, $options = []) {
        $from = carr::get($options, 'from', $this->config->getFrom());
        $fromName = carr::get($options, 'from_name', $this->config->getFromName());
        $attachments = carr::get($options, 'attachments', []);
        $replyTo = carr::get($options, 'replyTo', '');
        $cc = carr::get($options, 'cc', []);
        $bcc = carr::get($options, 'bcc', []);
        $result = null;
        $options = [];
        // $options['Tags'][] = ['Name' => 'subject', 'Value' => $subject];
        // $options['Tags'][] = ['Name' => 'From', 'Value' => $from];

        $destinations = [
            'ToAddresses'=>$to,
        ];
        $content = [

                'Subject'=>[
                    'Data'=>$subject,
                ],
                'Body' => [
                    'Text' => [
                        'Data' => $body,
                    ],
                ],


        ];

        try {
            $options['Source'] = $from;
            $options['Destination'] = $destinations;
            $options['Message'] = $content;

            $result = $this->ses->sendEmail($options);
        } catch (AwsException $e) {
            $reason = $e->getAwsErrorMessage() ?? $e->getMessage();

            throw new CEmail_Exception_EmailSendingFailedException(
                sprintf('Request to AWS SES API failed. Reason: %s.', $reason),
                is_int($e->getCode()) ? $e->getCode() : 0,
                $e
            );
        }
        cdbg::dd($result);
        $messageId = $result->get('MessageId');

        return $result;
    }
}
