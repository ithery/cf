<?php

use Webmozart\Assert\Assert;

final class CVendor_Kataai_Message_Language {
    /**
     * @var string
     */
    private $code;

    public function __construct(string $code) {
        Assert::length($code, 2);
        Assert::inArray($code, ['id', 'en']);

        $this->code = $code;
    }

    public function getCode(): string {
        return $this->code;
    }
}
