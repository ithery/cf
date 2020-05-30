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
    private $password = '';
    private $domain = '';
    private $conference_domain = '';
    private $debug = '';

    /**
     * Ejabberd constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config) {

        $this->api = carr::get($config, 'api');
        $this->user = carr::get($config, 'user');
        $this->password = carr::get($config, 'password');
        $this->domain = carr::get($config, 'domain');
        $this->conference_domain = carr::get($config, 'conference_domain');

        $this->debug = carr::get($config, 'debug');
    }

    /**
     * @param CXMPP_Ejabberd_CommandAbstract
     * @return null|\Psr\Http\Message\StreamInterface
     * @throws EjabberdException
     */
    public function execute(CXMPP_Ejabberd_CommandAbstract $command) {
        $client = new Client([
            'verify' => false,
            'base_uri' => $this->api
        ]);
        $command_name = $command->getCommandName();
        try {
            $response = $client->request('POST', $command_name, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'auth' => [
                    $this->user, $this->password
                ],
                'json' => $command->getCommandData()
            ]);
            if ($this->debug) {
                Log::info($command->getCommandName() . 'executed successfully.');
            }
            return $response->getBody();
        } catch (GuzzleException $e) {
            if ($this->debug) {
                clog::info("Error occurred while executing the command " . $command->getCommandName() . ".");
            }
            throw CXMPP_Ejabberd_Exception::networkException($e);
        } catch (\Exception $e) {
            if ($this->debug) {
                Log::info("Error occurred while executing the command " . $command->getCommandName() . ".");
            }
            throw CXMPP_Ejabberd_Exception::generalException($e);
        }
    }

}
