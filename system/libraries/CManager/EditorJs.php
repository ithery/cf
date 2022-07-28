<?php

class CManager_EditorJs {
    public static function createImageUploadHandler($options = []) {
        return new CManager_EditorJs_ImageUploadHandler($options);
    }
}
