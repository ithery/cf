<?php

defined('SYSPATH') or die('No direct access allowed.');
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware as GuzzleMiddleware;

class CDebug_DebugBar_GuzzleManager {
    /**
     * @return HandlerStack
     */
    public static function createStack() {
        $stack = HandlerStack::create();

        return self::configureStack($stack);
    }

    /**
     * @return HandlerStack
     */
    public static function configureStack(HandlerStack $stack) {
        $debugBar = CDebug::bar();
        $stack->push(new CDebug_DebugBar_GuzzleProfiler_Middleware(new CDebug_DebugBar_GuzzleProfiler($timeline = $debugBar->getCollector('time'))));

        $stack->unshift(new CDebug_DebugBar_GuzzleProfiler_ExceptionMiddleware($debugBar->getCollector('exceptions')));
        $formatter = new MessageFormatter();
        $messageCollector = $debugBar->getCollector('messages');
        /** @var \DebugBar\DataCollector\MessagesCollector $messageCollector */
        $stack->unshift(GuzzleMiddleware::log($messageCollector, $formatter));

        return $stack;
    }
}
