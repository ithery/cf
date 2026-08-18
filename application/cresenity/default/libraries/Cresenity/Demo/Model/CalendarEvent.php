<?php

namespace Cresenity\Demo\Model;

/**
 * Demo calendar event model backing the ajax CElement_Component_Calendar demo.
 *
 * @property-read int    $calendar_event_id
 * @property-read string $title
 * @property-read string $start
 * @property-read string $end
 * @property-read string $background_color
 * @property-read string $border_color
 * @property-read bool   $all_day
 */
class CalendarEvent extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    /**
     * @var array
     */
    protected $rows = [
        ['calendar_event_id' => 1, 'title' => 'Quarterly Review', 'start' => '2026-06-28 10:00:00', 'end' => '2026-06-28 11:30:00', 'background_color' => '#6f42c1', 'border_color' => '#6f42c1', 'all_day' => false],
        ['calendar_event_id' => 2, 'title' => 'Product Roadmap Sync', 'start' => '2026-07-05 09:00:00', 'end' => '2026-07-05 10:00:00', 'background_color' => '#0d6efd', 'border_color' => '#0d6efd', 'all_day' => false],
        ['calendar_event_id' => 3, 'title' => 'Sprint Planning', 'start' => '2026-07-06 13:00:00', 'end' => '2026-07-06 14:30:00', 'background_color' => '#20c997', 'border_color' => '#20c997', 'all_day' => false],
        ['calendar_event_id' => 4, 'title' => 'Client Demo Day', 'start' => '2026-07-10 00:00:00', 'end' => '2026-07-11 00:00:00', 'background_color' => '#fd7e14', 'border_color' => '#fd7e14', 'all_day' => true],
        ['calendar_event_id' => 5, 'title' => 'Team Retro', 'start' => '2026-07-15 15:00:00', 'end' => '2026-07-15 16:00:00', 'background_color' => '#dc3545', 'border_color' => '#dc3545', 'all_day' => false],
    ];
}
