<?php

defined('SYSPATH') OR die('No direct access allowed.');

class cmail {

    public static function error_mail($html) {


        $app = CApp::instance();
        $org = $app->org();
        $org_name = 'CAPP';
        $org_email = $org_name;
        if ($org != null) {
            $org_email = $org->name;
            $org_name = $org->name;
        }
        $subject = "Error Cresenity APP - " . $org_name . " on " . crouter::complete_uri();

        $headers = "From: " . strip_tags($org_email) . "\r\n";
        $headers .= "Reply-To: " . strip_tags($org_email) . "\r\n";
        //$headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = $html;
        $admin_email = ccfg::get("admin_email");

        if (ccfg::get("mail_error_smtp")) {
            $smtp_username = ccfg::get('smtp_username_error');
            $smtp_password = ccfg::get('smtp_password_error');
            $smtp_host = ccfg::get('smtp_host_error');
            $smtp_port = ccfg::get('smtp_port_error');
            $secure = ccfg::get('smtp_secure_error');
            $arr_options = array();
            if (strlen($smtp_username) > 0) {
                $arr_options['smtp_username'] = $smtp_username;
            }
            if (strlen($smtp_password) > 0) {
                $arr_options['smtp_password'] = $smtp_password;
            }
            if (strlen($smtp_host) > 0) {
                $arr_options['smtp_host'] = $smtp_host;
            }
            if (strlen($smtp_port) > 0) {
                $arr_options['smtp_port'] = $smtp_port;
            }
            if (strlen($secure) > 0) {
                $arr_options['smtp_secure'] = $secure;
            }

            $ret = cmail::send_smtp($admin_email, $subject . " [FOR ADMINISTRATOR]", $message, array(), array(), array(), $arr_options);
        } else {

            $ret = cmail::send($admin_email, $subject . " [FOR ADMINISTRATOR]", $message, $headers);
        }

        //echo $message;
    }

    public static function register($org_id) {
        $db = CDatabase::instance();
        $q = "select * from org where org_id=" . $db->escape($org_id);
        $r = $db->query($q);
        $email = "";
        if ($r->count() > 0) {
            $email = $r[0]->email;
        }

        $to = $email;

        $subject = "Welcome to Cresenity APP";

        $headers = "From: " . strip_tags("no-reply@cresenity.com") . "\r\n";
        $headers .= "Reply-To: " . strip_tags("no-reply@cresenity.com") . "\r\n";
        //$headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = cmail::register_content($org_id);
        $ret = cmail::send($to, $subject, $message, $headers);
        $admin_email = ccfg::get("admin_email");
        $ret = cmail::send($admin_email, $subject . " [FOR ADMINISTRATOR]", $message, $headers);

        //echo $message;
    }

    public static function header_html() {
        $html = '
		<style type="text/css">
		body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }
		</style>
		';
        $html .= '
			<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
			<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td align="center" valign="top" style="padding:20px 0 20px 0">
					<table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
						<!-- [ header starts here] -->
						<tr>
							<td valign="top" style="text-align:center; "><a href="' . curl::base(false, 'http') . '"><img src="' . curl::base(false, 'http') . 'media/img/cresenity-logo.png" alt="Cresenity APP"  style="margin-bottom:10px;" border="0"/></a></td>
						</tr>
						<!-- [ middle starts here] -->
			';

        return $html;
    }

    public static function footer_html() {
        $html = '
		<tr>
						<td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you, <strong>' . 'Cresenity APP' . '</strong></p></center></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>
		</body>
		';
        return $html;
    }

    public static function register_content($org_id) {
        $db = CDatabase::instance();
        $q = "select * from org where org_id=" . $db->escape($org_id);
        $r = $db->query($q);
        if ($r->count() > 0) {
            $name = $r[0]->name;
        }
        $q = "select * from users where org_id=" . $db->escape($org_id) . " order by user_id asc";

        $r = $db->query($q);
        if ($r->count() > 0) {
            $username = $r[0]->username;
        }
        $activation_link = capplication::activation_link($org_id);

        $html = '		<tr>
							<td valign="top">
								<h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;"">Welcome ' . $name . ',</h1>
								<p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Welcome to Cresenity APP. To log in when visiting our site just click <a href="' . curl::base() . '" style="color:#1E7EC8;">Login</a>, and then enter your username and password.</p>
								<p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Here is your activation link: <br/><a href="' . $activation_link . '" title="Activate your account" >' . $activation_link . '</a><br/>Please make sure you have activated your account before log in to your account</p>
								<p style="border:1px solid #E0E0E0; font-size:12px; line-height:16px; margin:0; padding:13px 18px; background:#f9f9f9;">
									Use the following values when prompted to log in after you activated your account:<br/>
									<strong>Username</strong>: ' . $username . '<br/>
									<strong>Password</strong>: &lt;Your Password&gt;<p>
								<p style="font-size:12px; line-height:16px; margin:0 0 8px 0;">When you log in to your account, you will be able to do the following:</p>
								<ul style="font-size:12px; line-height:16px; margin:0 0 16px 0; padding:0;">
									<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Change your password</li>
									<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Add item for your business the status of orders</li>
								</ul>
								<p style="font-size:12px; line-height:16px; margin:0;">If you have any questions about your account or any other matter, please feel free to contact us at <a href="mailto:admin@cresenity.com" style="color:#1E7EC8;">admin@cresenity.com</a> or or call us at <span class="nobr"></span> Monday - Friday, 8am - 5pm PST.
								</p>
							</td>
						</tr>
						';
        $html = cmail::header_html() . $html . cmail::footer_html();
        return $html;
    }

    public static function send_smtp($to, $subject, $message, $attachments = array(), $cc = array(), $bcc = array(), $options = array()) {
        $mail = CSMTP::factory();
        $smtp_username = carr::get($options, 'smtp_username');
        $smtp_password = carr::get($options, 'smtp_password');
        $smtp_host = carr::get($options, 'smtp_host');
        $smtp_port = carr::get($options, 'smtp_port');
        $secure = carr::get($options, 'smtp_secure');
        if (!$smtp_username) {
            $smtp_username = ccfg::get('smtp_username');
        }
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtp_host) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if (!$smtp_port) {
            $smtp_port = ccfg::get('smtp_port');
        }
        if (!$secure) {
            $secure = ccfg::get('smtp_secure');
        }

        if (count($attachments) == 0) {
            switch ($smtp_host) {
                case 'smtp.sendgrid.net':
                    return cmailapi::sendgrid($to, $subject, $message, $attachments, $cc, $bcc, $options);
                    break;
                case 'smtp.elasticemail.com':
                case 'smtp25.elasticemail.com':
                    return cmailapi::elasticemail($to, $subject, $message, $attachments, $cc, $bcc, $options);
                    break;
                case 'smtp.postmarkapp.com':
                    return cmailapi::postmark($to, $subject, $message, $attachments, $cc, $bcc, $options);
                    break;
            }
        }



        $mail->set_username($smtp_username);
        $mail->set_password($smtp_password);
        $mail->set_host($smtp_host);
        $mail->set_port($smtp_port);

        if ($secure == "ssl")
            $mail->set_ssl();
        if ($secure == "tls")
            $mail->set_tls();

        $smtp_from = carr::get($options, 'smtp_from');
        if ($smtp_from == null) {
            $smtp_from = ccfg::get('smtp_from');
        }
        $smtp_from_name = carr::get($options, 'smtp_from_name');
        if ($smtp_from_name == null) {
            $smtp_from_name = ccfg::get('smtp_from_name');
        }
        $mail->set_from($smtp_from, $smtp_from_name);

        $mail->set_message_html($message);
        $mail->set_subject($subject);
        if (!is_array($to)) {
            $to = array($to);
        }
        foreach ($to as $em) {
            $mail->add_to($em);
        }

        if (!is_array($cc))
            $cc = array($cc);
        foreach ($cc as $cc_k => $cc_v) {
            $mail->add_cc($cc_v);
        }
        if (!is_array($bcc))
            $bcc = array($bcc);
        foreach ($bcc as $bcc_k => $bcc_v) {
            $mail->add_bcc($bcc_v);
        }

        foreach ($attachments as $attachment) {
            $data = carr::get($attachment, 'data');
            $name = carr::get($attachment, 'name');
            $encoding = carr::get($attachment, 'encoding', 'base64');
            $type = carr::get($attachment, 'type', 'application/octet-stream');
            $mail->add_attachment_string($data, $name, $encoding, $type);
        }
        try {
            $mail->send();
        } catch (Exception $ex) {
//                die($ex->getMessage());
            throw $ex;
        }
    }

    public static function send($to, $subject, $message, $headers) {
        return @mail($to, $subject, $message, $headers);
    }

}
