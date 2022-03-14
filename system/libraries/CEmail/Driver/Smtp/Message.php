<?php

class CEmail_Driver_Smtp_Message {
    protected $body;

    protected $headers;

    protected $extraHeaders;

    public function __construct($to, $body, $subject, CEmail_Config $config, $options) {
        $this->config = $config;
        $this->options = $options;
        $this->body = $body;
        $this->headers = [];
        $this->extraHeaders = [];
        $this->setHeader('Subject', $subject);
        $this->setHeader('From', $this->config->getOption('username'));
        $this->setHeader('To', CEmail_DriverAbstract::formatAddresses($to));

        foreach (['cc' => 'Cc', 'bcc' => 'Bcc', 'reply_to' => 'Reply-To'] as $key => $header) {
            $list = carr::get($options, $key, []);
            $list = c::collect(carr::wrap($list))->filter()->all();
            if (count($list) > 0) {
                $this->setHeader($header, CEmail_DriverAbstract::formatAddresses($list));
            }
        }
    }

    /**
     * Builds the headers and body.
     *
     * @param bool $noBcc whether to exclude Bcc headers
     *
     * @return array An array containing the headers and the body
     */
    public function buildMessage($noBcc = false) {
        $newline = $this->newline();
        $charset = $this->charset();
        $encoding = $this->encoding();

        $headers = '';
        $parts = ['Date', 'Return-Path', 'From', 'To', 'Cc', 'Bcc', 'Reply-To', 'Subject', 'Message-ID', 'X-Priority', 'X-Mailer', 'MIME-Version', 'Content-Type'];
        $noBcc and array_splice($parts, 5, 1);

        foreach ($parts as $part) {
            $headers .= $this->getHeader($part);
        }

        foreach ($this->extraHeaders as $header => $value) {
            $headers .= $header . ': ' . $value . $newline;
        }

        $headers .= $newline;

        $body = '';

        if ($this->type() === 'plain' or $this->type() === 'html') {
            $body = $this->body;
        } else {
            switch ($this->type()) {
                case 'html_alt':
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: text/plain; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->alt_body . $newline . $newline;
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: text/html; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->body . $newline . $newline;
                    $body .= '--' . $this->boundaries[0] . '--';

                    break;
                case 'plain_attach':
                case 'html_attach':
                case 'html_inline':
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $text_type = (stripos($this->type, 'html') !== false) ? 'html' : 'plain';
                    $body .= 'Content-Type: text/' . $text_type . '; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->body . $newline . $newline;
                    $attach_type = (stripos($this->type, 'attach') !== false) ? 'attachment' : 'inline';
                    $body .= $this->getAttachmentHeaders($attach_type, $this->boundaries[0]);
                    $body .= '--' . $this->boundaries[0] . '--';

                    break;
                case 'html_alt_inline':
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: text/plain' . '; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->alt_body . $newline . $newline;
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: multipart/related;' . $newline . "\tboundary=\"{$this->boundaries[1]}\"" . $newline . $newline;
                    $body .= '--' . $this->boundaries[1] . $newline;
                    $body .= 'Content-Type: text/html; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->body . $newline . $newline;
                    $body .= $this->getAttachmentHeaders('inline', $this->boundaries[1]);
                    $body .= '--' . $this->boundaries[1] . '--' . $newline . $newline;
                    $body .= '--' . $this->boundaries[0] . '--';

                    break;
                case 'html_alt_attach':
                case 'html_inline_attach':
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: multipart/alternative;' . $newline . "\t boundary=\"{$this->boundaries[1]}\"" . $newline . $newline;
                    if (stripos($this->type, 'alt') !== false) {
                        $body .= '--' . $this->boundaries[1] . $newline;
                        $body .= 'Content-Type: text/plain; charset="' . $charset . '"' . $newline;
                        $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                        $body .= $this->alt_body . $newline . $newline;
                    }
                    $body .= '--' . $this->boundaries[1] . $newline;
                    $body .= 'Content-Type: text/html; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->body . $newline . $newline;
                    if (stripos($this->type, 'inline') !== false) {
                        $body .= $this->getAttachmentHeaders('inline', $this->boundaries[1]);
                        $body .= $this->alt_body . $newline . $newline;
                    }
                    $body .= '--' . $this->boundaries[1] . '--' . $newline . $newline;
                    $body .= $this->getAttachmentHeaders('attachment', $this->boundaries[0]);
                    $body .= '--' . $this->boundaries[0] . '--';

                    break;
                case 'html_alt_inline_attach':
                    $body .= '--' . $this->boundaries[0] . $newline;
                    $body .= 'Content-Type: multipart/alternative;' . $newline . "\t boundary=\"{$this->boundaries[1]}\"" . $newline . $newline;
                    $body .= '--' . $this->boundaries[1] . $newline;
                    $body .= 'Content-Type: text/plain; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->alt_body . $newline . $newline;
                    $body .= '--' . $this->boundaries[1] . $newline;
                    $body .= 'Content-Type: multipart/related;' . $newline . "\t boundary=\"{$this->boundaries[2]}\"" . $newline . $newline;
                    $body .= '--' . $this->boundaries[2] . $newline;
                    $body .= 'Content-Type: text/html; charset="' . $charset . '"' . $newline;
                    $body .= 'Content-Transfer-Encoding: ' . $encoding . $newline . $newline;
                    $body .= $this->body . $newline . $newline;
                    $body .= $this->getAttachmentHeaders('inline', $this->boundaries[2]);
                    $body .= $this->alt_body . $newline . $newline;
                    $body .= '--' . $this->boundaries[2] . '--' . $newline . $newline;
                    $body .= '--' . $this->boundaries[1] . '--' . $newline . $newline;
                    $body .= $this->getAttachmentHeaders('attachment', $this->boundaries[0]);
                    $body .= '--' . $this->boundaries[0] . '--';

                    break;
            }
        }

        return [
            'header' => $headers,
            'body' => $body,
        ];
    }

    /**
     * Gets the header.
     *
     * @param string $header    The header name. Will return all headers, if not specified
     * @param bool   $formatted Adds newline as suffix and colon as prefix, if true
     *
     * @return string|array Mail header or array of headers
     */
    protected function getHeader($header = null, $formatted = true) {
        if ($header === null) {
            return $this->headers;
        }

        if (array_key_exists($header, $this->headers)) {
            $prefix = ($formatted) ? $header . ': ' : '';
            $suffix = ($formatted) ? $this->newline() : '';

            return $prefix . $this->headers[$header] . $suffix;
        }

        return '';
    }

    /**
     * Encodes a mimeheader.
     *
     * @param string $header Header to encode
     *
     * @return string Mimeheader encoded string
     */
    protected function encodeMimeheader($header) {

        // determine the transfer encoding to be used
        $transferEncoding = ($this->encoding() === 'quoted-printable') ? 'Q' : 'B';

        // encode
        $header = mb_encode_mimeheader($header, $this->charset(), $transferEncoding, $this->newline());

        // and return it
        return $header;
    }

    /**
     * Get the attachment headers.
     *
     * @param mixed $type
     * @param mixed $boundary
     */
    protected function getAttachmentHeaders($type, $boundary) {
        $return = '';

        $newline = $this->newline();
        $attachments = carr::get($this->options, 'attachments');
        foreach ($attachments as $attachment) {
            $return .= '--' . $boundary . $newline;
            $return .= 'Content-Type: ' . $attachment['mime'] . '; name="' . $attachment['file'][1] . '"' . $newline;
            $return .= 'Content-Transfer-Encoding: base64' . $newline;
            $type === 'inline' and $return .= 'Content-ID: <' . substr($attachment['cid'], 4) . '>' . $newline;
            $return .= 'Content-Disposition: ' . $type . '; filename="' . $attachment['file'][1] . '"' . $newline . $newline;
            $return .= $attachment['contents'] . $newline . $newline;
        }

        return $return;
    }

    public function charset() {
        return carr::get($this->options, 'charset', 'utf-8');
    }

    public function encoding() {
        return carr::get($this->options, 'encoding', 'utf-8');
    }

    public function type() {
        return carr::get($this->options, 'type', 'html');
    }

    public function newline() {
        return $this->config->getOption('newline', PHP_EOL);
    }

    /**
     * Sets the message headers.
     *
     * @param string $header The header type
     * @param string $value  The header value
     */
    protected function setHeader($header, $value) {
        empty($value) or $this->headers[$header] = $value;

        return $this;
    }
}
