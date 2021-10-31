<?php

class CEmail_Driver_SmtpDriver extends CEmail_DriverAbstract {
    /**
     * The SMTP connection.
     */
    protected $smtpConnection = null;

    /**
     * Pipelining enabled?
     */
    protected $pipelining = false;

    public function send(array $to, $subject, $body, $options = []) {
        // send the email
        try {
            return $this->sendEmail($to, $subject, $body, $options);
        } catch (\Exception $e) {
            // something failed
            // disconnect if needed
            if ($this->smtpConnection) {
                if ($e instanceof CEmail_Exception_SmtpTimeoutException) {
                    // simply close the connection
                    fclose($this->smtpConnection);
                    $this->smtpConnection = null;
                } else {
                    // proper close, with a QUIT
                    $this->smtpDisconnect();
                }
            }

            // rethrow the exception
            throw $e;
        }
    }

    /**
     * Class destructor.
     */
    public function __destruct() {
        // makes sure any open connections will be closed
        if ($this->smtpConnection) {
            $this->smtpDisconnect();
        }
    }

    /**
     * Sends the actual email.
     *
     * @param mixed $subject
     * @param mixed $body
     * @param mixed $options
     *
     * @return bool success boolean
     */
    protected function sendEmail(array $to, $subject, $body, $options = []) {
        $smtpHost = carr::get($options, 'host', $this->config->getOption('port', 25));
        $smtpPort = carr::get($options, 'port', $this->config->getOption('host'));

        if (empty($smtpHost)) {
            throw new Exception('Must supply a SMTP host and port, none given.');
        }
        $smtpUsername = carr::get($options, 'username', $this->config->getUsername());
        $smtpPassword = carr::get($options, 'password', $this->config->getPassword());

        // Use authentication?
        $authenticate = (empty($this->smtpConnection) && !empty($smtpUsername) and !empty($smtpPassword));

        // Connect
        $this->smtpConnect($smtpHost, $smtpPort);

        // Authenticate when needed
        if ($authenticate) {
            $this->smtpAuthenticate($smtpUsername, $smtpPassword);
        }

        // Set return path
        $returnPath = carr::get($options, 'returnPath', carr::get($options, 'from'));
        $this->smtpSend('MAIL FROM:<' . $returnPath . '>', 250);

        foreach ([$this->arrayAddresses($to), 'cc', 'bcc'] as $addresses) {
            if (is_string($addresses)) {
                $addresses = $this->arrayAddresses(carr::get($options, $addresses, []));
            }
            foreach ($addresses as $recipient) {
                $this->smtpSend('RCPT TO:<' . $recipient . '>', [250, 251]);
            }
        }

        // Prepare for data sending
        $this->smtpSend('DATA', 354);

        $newLine = $this->config->getOption('newline', PHP_EOL);

        $headers = [];

        foreach (['cc' => 'Cc', 'bcc' => 'Bcc', 'reply_to' => 'Reply-To'] as $key => $headerKey) {
            $value = $this->formatAddresses(carr::get($options, $key));
            $headers[$headerKey] = $value;
        }
        $lines = explode($newLine, $headers . preg_replace('/^\./m', '..$1', $body));

        foreach ($lines as $line) {
            if (substr($line, 0, 1) === '.') {
                $line = '.' . $line;
            }

            fputs($this->smtpConnection, $line . $newLine);
        }

        // Finish the message
        $this->smtpSend('.', 250);

        // Close the connection if we're not using pipelining
        $this->pipelining or $this->smtpDisconnect();

        return true;
    }

    /**
     * Connects to the given smtp and says hello to the other server.
     *
     * @param mixed $smtpHost
     * @param mixed $smtpPort
     * @param mixed $smtpOptions
     */
    protected function smtpConnect($smtpHost, $smtpPort, $smtpOptions = []) {
        // re-use the existing connection
        if (!empty($this->smtpConnection)) {
            return;
        }

        // add a transport if not given
        if (strpos($smtpHost, '://') === false) {
            $smtpHost = 'tcp://' . $smtpHost;
        }

        $context = stream_context_create();
        if (is_array($smtpOptions) and !empty($smtpOptions)) {
            stream_context_set_option($context, $smtpOptions);
        }

        $this->smtpConnection = stream_socket_client(
            $smtpHost . ':' . $smtpPort,
            $errCode,
            $errMessage,
            $this->getTimeout(),
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (empty($this->smtpConnection)) {
            throw new CEmail_Exception_SmtpConnectionException('Could not connect to SMTP: (' . $errCode . ') ' . $errMessage);
        }

        // Clear the smtp response
        $this->smtpGetResponse();

        // Just say hello!
        try {
            $this->smtpSend('EHLO' . ' ' . c::request()->server('SERVER_NAME', 'localhost.local'), 250);
        } catch (CEmail_Exception_SmtpCommandFailureException $e) {
            // Didn't work? Try HELO
            $this->smtpSend('HELO' . ' ' . c::request()->server('SERVER_NAME', 'localhost.local'), 250);
        }

        // Enable TLS encryption if needed, and we're connecting using TCP
        $secure = carr::get($this->config->getSecure(), 'tls');

        if ($secure == 'tls') {
            try {
                $this->smtpSend('STARTTLS', 220);
                if (!stream_socket_enable_crypto($this->smtpConnection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new CEmail_Exception_SmtpConnectionException('STARTTLS failed, Crypto client can not be enabled.');
                }
            } catch (CEmail_Exception_SmtpCommandFailureException $e) {
                throw new CEmail_Exception_SmtpConnectionException('STARTTLS failed, invalid return code received from server.');
            }

            // Say hello again, the service list might be updated (see RFC 3207 section 4.2)
            try {
                $this->smtpSend('EHLO' . ' ' . c::request()->server('SERVER_NAME', 'localhost.local'), 250);
            } catch (CEmail_Exception_SmtpCommandFailureException $e) {
                // Didn't work? Try HELO
                $this->smtpSend('HELO' . ' ' . c::request()->server('SERVER_NAME', 'localhost.local'), 250);
            }
        }

        try {
            $this->smtpSend('HELP', 214);
        } catch (CEmail_Exception_SmtpCommandFailureException $e) {
            // Let this pass as some servers don't support this.
        }

        return $this->smtpConnection;
    }

    /**
     * Close SMTP connection.
     */
    protected function smtpDisconnect() {
        $this->smtpSend('QUIT', false);
        fclose($this->smtpConnection);
        $this->smtpConnection = null;
    }

    /**
     * Performs authentication with the SMTP host.
     *
     * @param string $smtpUsername
     * @param string $smtpPassword
     */
    protected function smtpAuthenticate($smtpUsername, $smtpPassword) {
        // Encode login data
        $username = base64_encode($smtpUsername);
        $password = base64_encode($smtpPassword);

        try {
            // Prepare login
            $this->smtpSend('AUTH LOGIN', 334);

            // Send username
            $this->smtpSend($username, 334);

            // Send password
            $this->smtpSend($password, 235);
        } catch (CEmail_Exception_SmtpCommandFailureException $e) {
            throw new CEmail_Exception_SmtpAuthenticationFailedException('Failed authentication.');
        }
    }

    /**
     * Sends data to the SMTP host.
     *
     * @param string             $data         The SMTP command
     * @param string|bool|string $expecting    The expected response
     * @param bool               $returnNumber Set to true to return the status number
     *
     * @throws \CEmail_Exception_SmtpCommandFailureException when the command failed an expecting is not set to false
     * @throws \CEmail_Exception_SmtpTimeoutException        SMTP connection timed out
     *
     * @return mixed Result or result number, false when expecting is false
     */
    protected function smtpSend($data, $expecting, $returnNumber = false) {
        !is_array($expecting) and $expecting !== false and $expecting = [$expecting];

        stream_set_timeout($this->smtpConnection, $this->config['smtp']['timeout']);
        if (!fputs($this->smtpConnection, $data . $this->config['newline'])) {
            if ($expecting === false) {
                return false;
            }

            throw new CEmail_Exception_SmtpCommandFailureException('Failed executing command: ' . $data);
        }

        $info = stream_get_meta_data($this->smtpConnection);
        if ($info['timed_out']) {
            throw new CEmail_Exception_SmtpTimeoutException('SMTP connection timed out.');
        }

        // Get the reponse
        $response = $this->smtpGetResponse();

        // Get the reponse number
        $number = (int) substr(trim($response), 0, 3);

        // Check against expected result
        if ($expecting !== false and !in_array($number, $expecting)) {
            throw new CEmail_Exception_SmtpCommandFailureException('Got an unexpected response from host on command: [' . $data . '] expecting: ' . join(' or ', $expecting) . ' received: ' . $response);
        }

        if ($returnNumber) {
            return $number;
        }

        return $response;
    }

    protected function getTimeout() {
        return $this->config->getOption('timeout', 5);
    }

    /**
     * Get SMTP response.
     *
     * @throws \CEmail_Exception_SmtpTimeoutException
     *
     * @return string SMTP response
     */
    protected function smtpGetResponse() {
        $data = '';

        // set the timeout.
        stream_set_timeout($this->smtpConnection, $this->getTimeout());

        while ($str = fgets($this->smtpConnection, 512)) {
            $info = stream_get_meta_data($this->smtpConnection);
            if ($info['timed_out']) {
                throw new CEmail_Exception_SmtpTimeoutException('SMTP connection timed out.');
            }

            $data .= $str;

            if (substr($str, 3, 1) === ' ') {
                break;
            }
        }

        return $data;
    }
}
