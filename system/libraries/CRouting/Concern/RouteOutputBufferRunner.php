<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 5, 2020 
 * @license Ittron Global Teknologi
 */
trait CRouting_Concern_RouteOutputBufferRunner   {
    use CHTTP_Trait_OutputBufferTrait;
    public function runWithOutputBuffer() {
        $this->startOutputBuffering();
        
        register_shutdown_function(function()  {
            if (!CHTTP::kernel()->isHandled()) {
                $output = $this->cleanOutputBuffer();
                if (strlen($output) > 0) {
                    echo $output;
                }
            }
        });
        $output = '';
        $response = null;
        try {
            $response = $this->run();
            
            //$response = $this->invokeController($request);
        } catch (Exception $e) {

            throw $e;
        } finally {

            $output = $this->cleanOutputBuffer();
        }
        if ($response == null || is_bool($response)) {
            //collect the header
            $response = c::response($output);

            if (!headers_sent()) {
                $headers = headers_list();

                foreach ($headers as $header) {
                    list($headerKey, $headerValue) = explode(":", $header);
                    header_remove($headerKey);
                    $response->header($headerKey, $headerValue);
                }
            }
        }
        return $response;
    }

    
    
}
