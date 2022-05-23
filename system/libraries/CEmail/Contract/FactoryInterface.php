<?php
interface CEmail_FactoryInterface {
    /**
     * Get a mailer instance by name.
     *
     * @param null|string $name
     *
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
