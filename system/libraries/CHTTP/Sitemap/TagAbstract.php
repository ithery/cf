<?php

abstract class CHTTP_Sitemap_TagAbstract {
    public function getType(): string {
        return cstr::substr(mb_strtolower(c::classBasename(static::class)), 0, -3);
    }
}
