<?php

class CAI {
    public static function openAI($options = []) {
        return CAI_Manager::instance()->createOpenAIService($options);
    }
}
