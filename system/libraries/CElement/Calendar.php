<?php

/**
 * Renders a fullCalendar-backed calendar element, sourcing events either from a
 * fixed list or via ajax against a SQL query.
 */
class CElement_Calendar extends CElement {
    use CTrait_Compat_Element_FormInput_Calendar;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * Whether events are fetched via ajax (using $query/$keyField) instead of $events.
     *
     * @var bool
     */
    protected $ajax;

    /**
     * SQL query used as the event source when $ajax is true.
     *
     * @var string
     */
    protected $query;

    /**
     * Column used to filter $query by the calendar's visible date range.
     *
     * @var string
     */
    protected $keyField;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);

        CManager::instance()->registerModule('fullcalendar');
        $this->ajax = true;
    }

    /**
     * This function is used to create new Calendar.
     *
     * @param string $id
     * @param mixed  $tag
     *
     * @return \CElement_Calendar
     */
    public static function factory($id = null, $tag = 'div') {
        /** @phpstan-ignore-next-line */
        return new static($id, $tag);
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = new CStringBuilder();

        $classes = $this->generate_class();

        $html->appendln("<div id='" . $this->id . "' class = '" . $classes . "'>");
        $html->appendln('</div>');

        return $html->text();
    }

    /**
     * Ajax endpoint invoked by fullCalendar to fetch events for the visible date range,
     * echoing the matching rows as a JSON event list.
     *
     * @param object $data
     *
     * @return void
     */
    public static function ajax($data) {
        $db = c::db();
        $q = $data->query;

        $base_q = $q;
        $pos_order_by = strpos(strtolower($base_q), 'order by', strpos(strtolower($base_q), 'from'));
        $temp_order_by = '';
        if ($pos_order_by !== false) {
            $temp_order_by = substr($base_q, $pos_order_by, strlen($base_q) - $pos_order_by);
            $base_q = substr($base_q, 0, $pos_order_by);
        }

        $post = $_POST;
        $start_date = carr::get($post, 'start');
        $end_date = carr::get($post, 'end');
        $query = 'SELECT * FROM (' . $base_q . ') as a WHERE ';
        $query .= ' ' . $data->key_field . ' >= ' . $db->escape($start_date);
        $query .= ' AND ' . $data->key_field . ' <= ' . $db->escape($end_date);

        $data = [];
        $r = $db->query($query);
        foreach ($r as $k => $v) {
            $start = date('Y-m-d H:i:s', strtotime(c::get($v, 'start')));
            $end = date('Y-m-d H:i:s', strtotime(c::get($v, 'end')));
            $url = c::get($v, 'url');
            $background_color = c::get($v, 'background_color');
            $border_color = c::get($v, 'border_color');
            $allDay = false;
            $arr_data = [
                'id' => c::get($v, 'id'),
                'title' => c::get($v, 'title'),
                'start' => $start,
                'end' => $end,
                'allDay' => $allDay,
            ];
            if (strlen($url) > 0) {
                $arr_data['url'] = $url;
            }
            if (strlen($background_color) > 0) {
                $arr_data['backgroundColor'] = $background_color;
            }
            if (strlen($border_color) > 0) {
                $arr_data['borderColor'] = $border_color;
            }
            $data[] = $arr_data;
        }
        echo json_encode($data);
    }

    /**
     * @return string
     */
    public function create_ajax_url() {
        return CAjaxMethod::factory()
            ->set_type('callback')
            ->set_method('post')
            ->set_data('callable', [CElement_Calendar::class, 'ajax'])
            ->set_data('query', $this->query)
            ->set_data('key_field', $this->keyField)
            ->makeurl();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = CStringBuilder::factory();

        $js->appendln("
            jQuery('#" . $this->id . "').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    month: 'month',
                    week: 'week',
                    day: 'day'
                },
                timeFormat: 'H:mm',
                eventLimit: true,
            ");
        if ($this->ajax) {
            $ajax_url = $this->create_ajax_url();
            $js->appendln("
                eventSources: [
                    {
                        url: '" . $ajax_url . "',
                        type: 'POST',
                    }
                ]
            ");
        } else {
            $event_js = '';
            foreach ($this->events as $key => $value) {
            }
        }
        $js->appendln('
            });
            ');

        $js->append(parent::js($indent));

        return $js->text();
    }

    /**
     * @param array $events
     *
     * @return $this
     */
    public function setEvents($events) {
        $this->events = $events;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query) {
        $this->query = $query;

        return $this;
    }

    /**
     * Alias for setKeyField().
     *
     * @param string $keyField
     *
     * @return $this
     */
    public function set_key_field($keyField) {
        return $this->setKeyField($keyField);
    }

    /**
     * @param string $keyField
     *
     * @return $this
     */
    public function setKeyField($keyField) {
        $this->keyField = $keyField;

        return $this;
    }
}
