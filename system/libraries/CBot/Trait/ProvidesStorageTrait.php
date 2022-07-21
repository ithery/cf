<?php

trait CBot_Trait_ProvidesStorageTrait {
    /**
     * @return CBot_Storage
     */
    public function userStorage() {
        return (new CBot_Storage($this->storage))
            ->setPrefix('user_')
            ->setDefaultKey($this->getMessage()->getSender());
    }

    /**
     * @return CBot_Storage
     */
    public function channelStorage() {
        return (new CBot_Storage($this->storage))
            ->setPrefix('channel_')
            ->setDefaultKey($this->getMessage()->getRecipient());
    }

    /**
     * @return CBot_Storage
     */
    public function driverStorage() {
        return (new CBot_Storage($this->storage))
            ->setPrefix('driver_')
            ->setDefaultKey($this->getDriver()->getName());
    }
}
