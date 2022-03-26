<?php

class CTemporary_PathGenerator_NullPathGenerator implements CTemporary_PathGeneratorInterface {
    public function generatePath($extension = null) {
        return '';
    }
}
