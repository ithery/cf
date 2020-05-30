<?php

/**
 * Description of Client
 * 
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 30, 2020 
 * @license Ittron Global Teknologi
 */
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CXMPP_Ejabberd_Client {

    private $api = '';
    private $user = '';
    private $verify;
    private $password = '';
    private $domain = '';
    private $conference_domain = '';
    private $debug = '';
    private $client = '';

    /**
     * Ejabberd constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config) {

        $this->api = carr::get($config, 'api');
        $this->user = carr::get($config, 'user');
        $this->user = carr::get($config, 'verify', false);
        $this->password = carr::get($config, 'password');
        $this->domain = carr::get($config, 'domain');
        $this->conference_domain = carr::get($config, 'conference_domain');

        $this->debug = carr::get($config, 'debug');

        $this->client = new Client([
            'base_uri' => $this->api,
            'verify' => $this->verify,
            'headers' => [
                'X-Admin' => true
            ]
        ]);
    }

    /**
     * @param CXMPP_Ejabberd_CommandAbstract
     * @return null|\Psr\Http\Message\StreamInterface
     * @throws EjabberdException
     */
    public function execute(CXMPP_Ejabberd_CommandAbstract $command) {

        $command_name = $command->getCommandName();
        try {
            $request = [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $command->getCommandData()
            ];

            if (strlen($this->user) > 0) {
                $request['auth'] = [
                    $this->user, $this->password
                ];
            }


            $response = $this->client->request('POST', $command_name, $request)->getBody()->getContents();
        } catch (GuzzleException $e) {
            $response = $exception->getResponse()->getBody()->getContents();
        } catch (\Exception $e) {

            $response = $e;
        }

        return (new CXMPP_Ejabberd_Response($response))->toArray();
    }

}
