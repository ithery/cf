<?php

interface CEmail_Contract_AttachableInterface {
    /**
     * Get an attachment instance for this entity.
     *
     * @return \CEmail_Attachment
     */
    public function toMailAttachment();
}
