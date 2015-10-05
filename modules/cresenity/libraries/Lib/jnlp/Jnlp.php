<?php

    /**
     *
     * @author Raymond
     * @since  Apr 10, 2014
     */
    
    class Jnlp{
        const __ext_file = '.jnlp';


        private $invoice_id;
        private $xml;
        private $folder = 'web_start/'; // please dont change this folder. If u change this, u need create folder manually.
        private $prefix = 'Invoice_';
        private $full_name;
        private $url_redirect;
        private $url_argument;
        
        
        public function create(){
            $this->full_name = $this->prefix .$this->invoice_id;
            
            // build raw xml
            $this->__xml_raw();
            
            $this->url_redirect = curl::base() . $this->folder .str_replace('/', '~', $this->full_name) .$this::__ext_file;
            file_put_contents($this->folder .str_replace('/', '~', $this->full_name) .$this::__ext_file, $this->xml);
        }
        
        private function __xml_raw(){
            $this->xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                <jnlp codebase="http://travons.b2b.local/web_start" href="'.str_replace('/', '~', $this->full_name).'.jnlp" spec="1.0+">
                    <information>
                        <title>TorsPrinter</title>
                        <vendor>TORS</vendor>
                        <homepage href=""/>
                        <description>TorsPrinter</description>
                        <description kind="short">TorsPrinter</description>
                    </information>
                    <update check="always"/>
                    <security>
                        <all-permissions/>
                    </security>
                    <resources>
                        <j2se version="1.7+"/>
                        <property name="java.net.preferIPv4Stack" value="true"/> 
                <jar href="TorsPrinterNew.jar" main="true"/>
                <jar href="lib/commons-logging-1.1.1.jar"/>
                <jar href="lib/cups4j-0.6.4.jar"/>
                <jar href="lib/httpclient-4.3.1.jar"/>
                <jar href="lib/httpcore-4.3.jar"/>
                <jar href="lib/httpmime-4.3.1.jar"/>
                <jar href="lib/log4j1.2.15.jar"/>
                <jar href="lib/slf4j-api-1.6.6.jar"/>
                <jar href="lib/commons-codec-1.9.jar"/>
                <jar href="lib/commons-io-2.4.jar"/>
                </resources>
                    <application-desc main-class="torsprinternew.ExecPrinter">
                        <argument>'.$this->url_argument.'</argument>
                    </application-desc>
                </jnlp>';
        }
        
        public function setInvoice_id($invoice_id) {
            $this->invoice_id = $invoice_id;
        }
        
        public function setPrefix($prefix) {
            $this->prefix = $prefix;
        }
        
        public function getUrl_redirect() {
            return $this->url_redirect;
        }

        public function getUrl_argument() {
            return $this->url_argument;
        }

        public function setUrl_argument($url_argument) {
            $this->url_argument = $url_argument;
        }

            
    }
?>
