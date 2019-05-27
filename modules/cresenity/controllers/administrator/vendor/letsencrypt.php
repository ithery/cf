<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 12:10:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Vendor_Letsencrypt extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Lets Encrypt');
        $options = array();
        $options['email'] = array('hery@ittron.co.id');

        $domain = CF::domain();
        $basename = CHelper_Domain::getTopLevelDomain($domain);
        $domains = array($domain);


        //$options['log'] = LEClient\LEClient::LOG_DEBUG;
        $letsEncrypt = CVendor::letsEncrypt($options);
        $haveCertificate = $letsEncrypt->haveCertificate();


        $acct = $letsEncrypt->client()->getAccount();

        $widget = $app->addWidget()->setTitle('Info');
        if (!$haveCertificate) {
            //generate button create certificate
            $widget->header()->addAction()->setLabel('Create Certificate')->addClass('btn-primary')->setLink(curl::base() . 'administrator/vendor/letsencrypt/order');
        }
        $form = $widget->addForm();
        $divRow = $form->addDiv()->addClass('row');
        $colLeft = $divRow->addDiv()->addClass('col-sm-6');
        $colRight = $divRow->addDiv()->addClass('col-sm-6');
        $colLeft->addField()->setLabel('Domain')->addControl('domain', 'label')->setValue($domain);
        $colLeft->addField()->setLabel('Top Domain')->addControl('topDomain', 'label')->setValue($basename);
        $colLeft->addField()->setLabel('ID')->addControl('accountId', 'label')->setValue($acct->id);
        $colLeft->addField()->setLabel('Contact')->addControl('accountContact', 'label')->setValue(implode(",", $acct->contact));
        $colLeft->addField()->setLabel('Initial IP')->addControl('accountInitialIp', 'label')->setValue($acct->initialIp);
        $colLeft->addField()->setLabel('Account Status')->addControl('accountStatus', 'label')->setValue($acct->status);
        $colRight->addField()->setLabel('Private Key')->addControl('privateKey', 'text')->setValue($letsEncrypt->getPrivateKeyPath())->setReadOnly();;
        if (!$haveCertificate) {
            $colRight->addField()->setLabel('Certificate')->addControl('certificate', 'label')->setValue($haveCertificate ? 'YES' : 'NO');
        } else {
            $colRight->addField()->setLabel('Certificate')->addControl('certificatePath', 'text')->setValue($letsEncrypt->getCertificatePath())->setReadOnly();
            if ($letsEncrypt->haveChain()) {
                $colRight->addField()->setLabel('Chain')->addControl('chainPath', 'text')->setValue($letsEncrypt->getChainPath())->setReadOnly();
            }
            $orderData = $letsEncrypt->getOrderData();
            $colRight->addField()->setLabel('Expires')->addControl('certificateExpires', 'label')->setValue(carr::get($orderData, 'expires'));

            $colRight->addHr();
            $colRight->addAction()->setLabel('Renew Certificate')->addClass('btn-primary')->setIcon(' lnr lnr-plus-circle')->setLink(curl::base() . 'administrator/vendor/letsencrypt/renew')->setConfirm();
        }


        echo $app->render();
    }

    public function order() {
        $options = array();
        $options['email'] = array('hery@ittron.co.id');

        $domain = CF::domain();
        $basename = CHelper_Domain::getTopLevelDomain($domain);
        $domains = array($domain);


        //$options['log'] = LEClient\LEClient::LOG_DEBUG;
        $letsEncrypt = CVendor::letsEncrypt($options);
        $haveCertificate = $letsEncrypt->haveCertificate();
        $order = $letsEncrypt->client()->getOrCreateOrder($basename, $domains);

        $valid = $order->allAuthorizationsValid();
        if (!$valid) {
            $pendingHttp = $order->getPendingAuthorizations(LEClient\LEOrder::CHALLENGE_TYPE_HTTP);
            // Walk the list of pending authorization HTTP challenges.
            if (!empty($pendingHttp)) {
                foreach ($pendingHttp as $challenge) {
                    // Define the folder in which to store the challenge. For the purpose of this example, a fictitious path is set.
                    $folder = DOCROOT . '.well-known/acme-challenge/';
                    // Check if that directory yet exists. If not, create it.
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    // Store the challenge file for this domain.
                    file_put_contents($folder . $challenge['filename'], $challenge['content']);
                    // Let LetsEncrypt verify this challenge.
                    $order->verifyPendingOrderAuthorization($challenge['identifier'], LEClient\LEOrder::CHALLENGE_TYPE_HTTP);
                }
            }




            //$form->addField()->setLabel('HTTP')->addControl('orderValid', 'label')->setValue($valid ? 'YES' : 'NO');
        }
        // Check once more whether all authorizations are valid before we can finalize the order.
        $valid = $order->allAuthorizationsValid();
        if ($order->allAuthorizationsValid()) {
            // Finalize the order first, if that is not yet done.
            if (!$order->isFinalized()) {
                $order->finalizeOrder();
            }
            // Check whether the order has been finalized before we can get the certificate. If finalized, get the certificate.
            if ($order->isFinalized()) {
                $certificate = $order->getCertificate();
            }
            cmsg::add('success', 'Successfully create certificate');
        } else {
            cmsg::add('error', 'Failed to create certificate');
        }
        curl::redirect('administrator/vendor/letsencrypt');
    }

    public function renew() {
        $this->order();
    }

}
