<?php

# PayPal Library
# aitindo.test@yahoo.com:aitindo.test
# aitind_1237348252_biz@yahoo.com:237348232
# aitind_1237350432_per@yahoo.com:237350398

//define('PAYPAL_EMAIL', 'aitind_1237348252_biz@yahoo.com');
define('PAYPAL_EMAIL', 'hendrachiang@insight-unlimited.com');
//define('PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
define('PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
//define('PAYPAL_VERIFICATION_URL', 'ssl://www.sandbox.paypal.com:443/cgi-bin/webscr');
define('PAYPAL_VERIFICATION_URL', 'ssl://www.paypal.com:443/cgi-bin/webscr');
//define('PAYPAL_NOTIFICATION_URL', 'http://www.benih.com/beta/shop/payment/paypal/notify');
define('PAYPAL_NOTIFICATION_URL', 'http://www.benih.com/shop/payment/paypal/notify');
//define('PAYPAL_RETURN_URL', 'http://www.benih.com/beta/shop/payment/paypal/return');
define('PAYPAL_RETURN_URL', 'http://www.benih.com/shop/payment/paypal/return');
define('PAYPAL_CURRENCY_CODE', 'USD');
define('PAYPAL_NO_SHIPPING', 2);
define('PAYPAL_NO_NOTE', 1);
define('PAYPAL_RM', 1);

define('PP_DEMO_ITEM_NAME', 'Some things are better left alone.');
define('PP_DEMO_ITEM_NUMBER', '987123');
define('PP_DEMO_ITEM_AMOUNT', 59);

class CPayPalNative {
    var $email;
    var $url;
    var $verification_url;
    var $notification_url;
    var $return_url;
    var $currency_code = 'USD';
    var $no_shipping;
    var $no_note;
    var $rm;

    function PayPal($web)
    {
        $this->web = $web;
		return $this;
    }

    function set_email($email)
    {
        $this->email = $email;
		return $this;
    }

    function set_url($url)
    {
        $this->url = $url;
		return $this;
    }

    function set_verification_url($verification_url)
    {
        $this->verification_url = $verification_url;
		return $this;
    }

    function set_notification_url($notification_url)
    {
        $this->notification_url = $notification_url;
		return $this;
    }

    function set_return_url($return_url)
    {
        $this->return_url = $return_url;
		return $this;
    }

    function set_currency_code($currency_code)
    {
        $this->currency_code = $currency_code;
		return $this;
    }

    function set_no_shipping($no_shipping)
    {
        $this->no_shipping = $no_shipping;
		return $this;
    }

    function set_no_note($no_note)
    {
        $this->no_note = $no_note;
		return $this;
    }

    function set_rm($rm)
    {
        $this->rm = $rm;
		return $this;
    }

    function render_form($name, $number, $amount)// {{{
    {
        ob_start();
    ?>
        <form method="post" action="<?=$this->url?>">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="<?=$this->email?>">
            <input type="hidden" name="item_name" value="<?=$name?>">
            <input type="hidden" name="item_number" value="<?=$number?>">
            <input type="hidden" name="amount" value="<?=$amount?>">
            <input type="hidden" name="no_shipping" value="<?=$this->no_shipping?>">
            <input type="hidden" name="no_note" value="<?=$this->no_note?>">
            <input type="hidden" name="currency_code" value="<?=$this->currency_code?>">
            <input type="hidden" name="return" value="<?=$this->return_url?>">
            <input type="hidden" name="notify_url" value="<?=$this->notification_url?>">
            <input type="hidden" name="rm" value="<?=$this->rm?>">
            <input type="hidden" name="bn" value="PP-BuyNowBF">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    <?php
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }// }}}

    function verify($data = '', $headers = array(), $timeout = 30)// {{{
    {
        $url = parse_url($this->verification_url);
        if (!$url['path']) $url['path'] = '/';
        if ($url['query']) $url['path'] .= '?' . $url['query'];
        $request = 'POST ' . $url['path'] . " HTTP/1.0\r\n";
        $headers['Host'] = $url['host'];
        $headers['Content-Length'] = strlen($data);
        foreach ($headers as $name => $value) {
            $request .= $name . ': ' . $value . "\r\n";
        }
        $request .= "\r\n";
        $request .= $data;
        $response = false;
        if (!isset($url['port'])) $url['port'] = 443;
        if (false != ($http = fsockopen($url['scheme'] . '://' . $url['host'], $url['port'], $errno, $errstr, $timeout)) && is_resource($http)) {
            fwrite($http, $request);
            while (!feof($http))
                $response .= fgets($http, 1160); // One TCP-IP packet
            fclose($http);
            $response = explode("\r\n\r\n", $response, 2);
        } else {
            // LOG: unable to open HTTP socket connection to $paypal_verification_url
            //file_put_contents('paypal_log.txt', 'PAYPAL ' . date('Y-m-d H:i:s', time()) . ' HTTP FAIL', FILE_APPEND);
        }
        return $response;
    }// }}}

    function ipn($callback1, $callback2)// {{{
    {
        $request[] = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $request[] = urlencode(stripslashes($key)) . '=' . urlencode(stripslashes($value));
        }

        // check the item_number and see if it exists... $_POST['item_number'] if not preferable log it for inspection
        // $_POST['item_number'] here translates to invoice
        list($valid, $item) = call_user_func_array($callback1, array($this->web, $_POST));
        if (!$valid) return false;

        //file_put_contents('/var/www/www.benih.com/beta/webroot/files/log_paypal_about_to_verify.txt', 'imabout to verify the payment!');

        $paypal_url = 'http://';
        list($header, $response) = $this->verify(implode('&', $request), array(), 30);
        // make sure we send HTTP 200 response
        header('HTTP/1.1 200 OK');

        if ($response !== false) {
            // check for validation
            if (strcmp(strtoupper($response), 'VERIFIED') == 0) {
                // check that the payment_status is Completed
                // $_POST['payment_status'] == 'Completed'
                if ($_POST['payment_status'] != 'Completed') {
                    // LOG: transaction is incomplete.
                    // OPTIONAL: handle other cases
                    return false;
                }

                // check that txn_id has not been previously processed
                // $_POST['txn_id']

                // check that receiver_email is your primary PayPal email
                // $_POST['receiver_email']
                if ($_POST['receiver_email'] != $this->email) {
                    // LOG: receiver_email is something other than our PAYPAL_EMAIL setting, someone is trying to bork our joint!
                    //file_put_contents('paypal_log.txt', 'PAYPAL ' . date('Y-m-d H:i:s', time()) . ' EMAIL ERROR', FILE_APPEND);
                    return false;
                }

                // check that item_number, payment_amount and payment_currency are all correct
                // $_POST['item_number'], $_POST['mc_gross'], $_POST['mc_currency']
                if ($_POST['item_number'] != $item['item_number'] or $_POST['mc_gross'] != $item['mc_gross'] or $_POST['mc_currency'] != $this->currency_code) {
                    //file_put_contents('paypal_log.txt', 'PAYPAL ' . date('Y-m-d H:i:s', time()) . ' ITEM ERROR', FILE_APPEND);
                    return false;
                }

                //file_put_contents('/var/www/www.benih.com/beta/webroot/files/log_paypal_in_ipn.txt', 'imabout to complete the order!');
                call_user_func_array($callback2, array($this->web, $_POST));
                // done!
                //file_put_contents('paypal_log.txt', 'PAYPAL ' . date('Y-m-d H:i:s', time()) . ' OK', FILE_APPEND);
                exit;
            } elseif (strcmp(strtoupper($response), 'INVALID') == 0) {
                // silently log for manual investigation later
                header('HTTP/1.1 200 OK');
                return false;
            }
        } else {
            // PayPal unable to receive verification response for order...
            exit;
        }
    }// }}}

    function http_request($method, $url, $data = '', $headers = array(), $timeout = 5)// {{{
    {
        $url = parse_url($url);
        if (!$url['path']) $url['path'] = '/';
        if ($url['query']) $url['path'] .= '?' . $url['query'];
        $request = strtoupper($method) . ' ' . $url['path'] . " HTTP/1.0\r\n";
        $headers['Host'] = $url['host'];
        $headers['Content-Length'] = strlen($data);
        foreach ($headers as $name => $value) {
            $request .= $name . ': ' . $value . "\r\n";
        }
        $request .= "\r\n";
        $request .= $data;
        $response = false;
        if (!isset($url['port'])) $url['port'] = 80;
        if (false != ($http = @fsockopen($url['host'], $url['port'], $errno, $errstr, $timeout)) && is_resource($http)) {
            fwrite($http, $request);
            while (!feof($http))
                $response .= fgets($http, 1160); // One TCP-IP packet
            fclose($http);
            $response = explode("\r\n\r\n", $response, 2);
        }
        return $response;
    }// }}}

    function http_get($url, $data = '', $headers = array(), $timeout = 5)// {{{
    {
        if ($data) $url .= '?' . $data;
        return http_request('GET', $url, '', $headers, $timeout);
    }// }}}

    function http_post($url, $data = '', $headers = array(), $timout = 5)// {{{
    {
        if (!isset($headers['Content-Type'])) {
            $headers = array_merge($headers, array('Content-Type' => 'application/x-www-form-urlencoded'));
        }
        return http_request('POST', $url, $data, $headers, $timeout);
    }// }}}
}

?>
