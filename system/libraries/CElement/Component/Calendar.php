<?php

/**
 * Renders a fullCalendar-backed calendar element. Rendering/behavior is handled
 * client-side by cres.js (see media/js/cres/src/element/component/Calendar), the
 * same way CElement_Component_TreeView works: this class only builds the markup and passes
 * config via the `cres-config` attribute.
 *
 * $events starts out as a CElement_Component_Calendar_CalendarEvents (created in the
 * constructor) to fill in directly via getEvents()->setData()/addData(), or via
 * setEvents(array $events), for a fixed event list rendered as-is.
 *
 * Calling setEvents(callable $events) instead switches to ajax mode: $events replaces
 * the property with the callback itself, invoked through the CAjax::TYPE_CALENDAR
 * engine (CAjax_Engine_Calendar) with the visible date range and a fresh
 * CElement_Component_Calendar_CalendarEvents to fill in.
 */
class CElement_Component_Calendar extends CElement_Component {
    /**
     * Either a CElement_Component_Calendar_CalendarEvents (fixed event list, rendered
     * as-is) or a callable (ajax mode, see setEvents()).
     *
     * @var callable|CElement_Component_Calendar_CalendarEvents
     */
    protected $events;

    /**
     * List of file paths to require_once before invoking the callback, ajax mode only.
     *
     * @var array
     */
    protected $requires = [];

    /**
     * Short (2-letter, moment.js-style) locale code, e.g. 'id', passed to fullCalendar so
     * it derives its month/weekday names from the moment locale data of the same code.
     *
     * @var string
     */
    protected $locale;

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);

        CManager::instance()->registerModule('moment');
        CManager::instance()->registerModule('fullcalendar');

        $this->events = new CElement_Component_Calendar_CalendarEvents();

        $locale = CF::getLocale();
        if (strlen($locale) > 2) {
            $locale = strtolower(substr($locale, 0, 2));
        }
        $this->locale = $locale;
    }

    /**
     * @param string $locale short (2-letter, moment.js-style) locale code, e.g. 'id'
     *
     * @return $this
     */
    public function setLocale($locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Translated button text (today/month/week/day, see system/i18n/{locale}/element/calendar.php),
     * falling back to English defaults client-side for any key missing from the current locale.
     *
     * @return array
     */
    public function getTranslation() {
        $translator = CTranslation::translator();

        return $translator->getLoader()->load($translator->getLocale(), 'element/calendar');
    }

    /**
     * @param string $id
     *
     * @return \CElement_Component_Calendar
     */
    public static function factory($id = null) {
        return new static($id);
    }

    /**
     * @return callable|CElement_Component_Calendar_CalendarEvents
     */
    public function getEvents() {
        return $this->events;
    }

    /**
     * A callable switches this calendar to ajax mode: it's invoked, for the calendar's
     * visible date range, as `$events($startDate, $endDate, CElement_Component_Calendar_CalendarEvents $events)`
     * and should push matching events into $events via addData(). Anything else is
     * treated as a fixed event list and replaces the current one (see
     * CElement_Component_Calendar_CalendarEvents::setData()).
     *
     * @param callable|array    $events
     * @param array|string|null $require one or more file paths to require_once before invoking the callback, ajax mode only
     *
     * @return $this
     */
    public function setEvents($events, $require = null) {
        if (is_callable($events)) {
            $this->events = $events;
            if ($require != null) {
                if (!is_array($require)) {
                    $require = [$require];
                }
                foreach ($require as $req) {
                    $this->requires[] = $req;
                }
            }

            return $this;
        }

        if (!($this->events instanceof CElement_Component_Calendar_CalendarEvents)) {
            $this->events = new CElement_Component_Calendar_CalendarEvents();
        }
        $this->events->setData($events);

        return $this;
    }

    /**
     * @return bool
     */
    public function isAjax() {
        return is_callable($this->events);
    }

    /**
     * @return string
     */
    public function createAjaxUrl() {
        return CAjax::createMethod()
            ->setType(CAjax::TYPE_CALENDAR)
            ->setMethod('post')
            ->setData('callback', serialize(c::toSerializableClosure($this->events)))
            ->setData('requires', $this->requires)
            ->makeUrl();
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();

        $this->addClass('cres:element:component:Calendar');
        $this->setAttr('cres-element', 'component:Calendar');

        $config = [
            'ajax' => $this->isAjax(),
            'locale' => $this->locale,
            'buttonText' => $this->getTranslation(),
        ];
        if ($this->isAjax()) {
            $config['ajaxUrl'] = $this->createAjaxUrl();
        } else {
            $config['events'] = $this->getEvents()->getData();
        }

        $this->setAttr('cres-config', c::json($config));
    }
}
