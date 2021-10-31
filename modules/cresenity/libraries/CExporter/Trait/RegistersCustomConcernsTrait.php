<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CExporter_Trait_RegistersCustomConcernsTrait {

    /**
     * @var array
     */
    private static $eventMap = [
        CExporter_Event_BeforeWriting::class => CExporter_Writer::class,
        CExporter_Event_BeforeExport::class => CExporter_Writer::class,
        CExporter_Event_BeforeSheet::class => CExporter_Sheet::class,
        CExporter_Event_AfterSheet::class => CExporter_Sheet::class,
    ];

    /**
     * @param string   $concern
     * @param callable $handler
     * @param string   $event
     */
    public static function extend($concern, callable $handler, $event = CExporter_Event_BeforeWriting::class) {
        /** @var HasEventBus $delegate */
        $delegate = isset(static::$eventMap[$event]) ? static::$eventMap[$event] : CExporter_Event_BeforeWriting::class;

        $delegate::listen($event, function (CExporter_Event $event) use ($concern, $handler) {
            if ($event->appliesToConcern($concern)) {
                $handler($event->getConcernable(), $event->getDelegate());
            }
        });
    }

}
