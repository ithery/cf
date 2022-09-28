<?php
interface CEmail_Contract_FactoryInterface {
    /**
     * Get a mailer instance by name.
     *
     * @param null|string $name
     *
     * @return \CEmail_Contract_MailerInterface
     */
    public function mailer($name = null);
}
