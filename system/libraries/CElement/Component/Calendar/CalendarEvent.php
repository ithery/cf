<?php

defined('SYSPATH') or die('No direct access allowed.');

use Illuminate\Contracts\Support\Arrayable;

/**
 * A single fullCalendar-shaped event. Normalizes snake_case input (background_color,
 * border_color, all_day -- e.g. straight off a CModel row) to the camelCase keys
 * fullCalendar expects.
 */
class CElement_Component_Calendar_CalendarEvent implements Arrayable {
    /**
     * @var null|int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $start;

    /**
     * @var null|string
     */
    protected $end;

    /**
     * @var bool
     */
    protected $allDay;

    /**
     * @var null|string
     */
    protected $url;

    /**
     * @var null|string
     */
    protected $backgroundColor;

    /**
     * @var null|string
     */
    protected $borderColor;

    /**
     * @param array $data event data, snake_case or camelCase keys
     *
     * @return void
     */
    public function __construct(array $data = []) {
        $this->id = carr::get($data, 'id');
        $this->title = carr::get($data, 'title');
        $this->start = carr::get($data, 'start');
        $this->end = carr::get($data, 'end');
        $this->allDay = (bool) carr::get($data, 'allDay', carr::get($data, 'all_day', false));
        $this->url = carr::get($data, 'url');
        $this->backgroundColor = carr::get($data, 'backgroundColor', carr::get($data, 'background_color'));
        $this->borderColor = carr::get($data, 'borderColor', carr::get($data, 'border_color'));
    }

    /**
     * @return array
     */
    public function toArray() {
        $event = [
            'title' => $this->title,
            'start' => $this->start,
            'allDay' => $this->allDay,
        ];

        if ($this->id !== null) {
            $event['id'] = $this->id;
        }
        if ($this->end !== null) {
            $event['end'] = $this->end;
        }
        if (strlen($this->url) > 0) {
            $event['url'] = $this->url;
        }
        if (strlen($this->backgroundColor) > 0) {
            $event['backgroundColor'] = $this->backgroundColor;
        }
        if (strlen($this->borderColor) > 0) {
            $event['borderColor'] = $this->borderColor;
        }

        return $event;
    }
}
