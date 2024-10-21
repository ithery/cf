<?php

class CPeriod_OpeningHours {
    use CPeriod_Trait_DataTrait;
    use CPeriod_Trait_DateTimeCopierTrait;
    use CPeriod_OpeningHours_Trait_DiffTrait;

    const DEFAULT_DAY_LIMIT = 8;

    /**
     * @var \CPeriod_OpeningHours_Day[]
     */
    protected $openingHours = [];

    /**
     * @var \CPeriod_OpeningHours_OpeningHoursForDay[]
     */
    protected $exceptions = [];

    /**
     * @var callable[]
     */
    protected $filters = [];

    /**
     * @var null|DateTimeZone
     */
    protected $timezone = null;

    /**
     * @var null|DateTimeZone
     */
    protected $outputTimezone = null;

    /**
     * @var bool Allow for overflowing time ranges which overflow into the next day
     */
    protected $overflow;

    /**
     * @var int Number of days to try before abandoning the search of the next close/open time
     */
    protected $dayLimit = null;

    /**
     * @var string
     */
    protected $dateTimeClass = DateTime::class;

    /**
     * @param null|string|DateTimeZone $timezone
     * @param null|string|DateTimeZone $outputTimezone
     */
    public function __construct($timezone = null, $outputTimezone = null) {
        $this->setTimezone($timezone);
        $this->setOutputTimezone($outputTimezone);

        $this->openingHours = CPeriod_OpeningHours_Day::mapDays(static function () {
            return new CPeriod_OpeningHours_OpeningHoursForDay();
        });
    }

    /**
     * @param  array{
     *             monday?: array<string|array>,
     *             tuesday?: array<string|array>,
     *             wednesday?: array<string|array>,
     *             thursday?: array<string|array>,
     *             friday?: array<string|array>,
     *             saturday?: array<string|array>,
     *             sunday?: array<string|array>,
     *             exceptions?: array<array<string|array>>,
     *             filters?: callable[],
     *             overflow?: bool,
     *         }                         $data
     * @param null|string|DateTimeZone $timezone
     * @param null|string|DateTimeZone $outputTimezone
     *
     * @return static
     */
    public static function create(array $data, $timezone = null, $outputTimezone = null): self {
        return (new static($timezone, $outputTimezone))->fill($data);
    }

    /**
     * @param array $data         hours definition array or sub-array
     * @param array $excludedKeys keys to ignore from parsing
     *
     * @return array
     */
    public static function mergeOverlappingRanges(array $data, array $excludedKeys = ['data', 'filters', 'overflow']) {
        $result = [];
        $ranges = [];

        foreach (static::filterHours($data, $excludedKeys) as $key => $value) {
            $value = is_array($value)
                ? static::mergeOverlappingRanges($value, ['data'])
                : (is_string($value) ? CPeriod_OpeningHours_TimeRange::fromString($value) : $value);

            if ($value instanceof CPeriod_OpeningHours_TimeRange) {
                $newRanges = [];

                foreach ($ranges as $range) {
                    if ($value->format() === $range->format()) {
                        continue 2;
                    }

                    if ($value->overlaps($range) || $range->overlaps($value)) {
                        $value = CPeriod_OpeningHours_TimeRange::fromList([$value, $range]);

                        continue;
                    }

                    $newRanges[] = $range;
                }

                $newRanges[] = $value;
                $ranges = $newRanges;

                continue;
            }

            $result[$key] = $value;
        }

        foreach ($ranges as $range) {
            $result[] = $range;
        }

        return $result;
    }

    /**
     * @param  array{
     *             monday?: array<string|array>,
     *             tuesday?: array<string|array>,
     *             wednesday?: array<string|array>,
     *             thursday?: array<string|array>,
     *             friday?: array<string|array>,
     *             saturday?: array<string|array>,
     *             sunday?: array<string|array>,
     *             exceptions?: array<array<string|array>>,
     *             filters?: callable[],
     *             overflow?: bool,
     *         }                         $data
     * @param null|string|DateTimeZone $timezone
     * @param null|string|DateTimeZone $outputTimezone
     *
     * @return static
     */
    public static function createAndMergeOverlappingRanges(array $data, $timezone = null, $outputTimezone = null) {
        return static::create(static::mergeOverlappingRanges($data), $timezone, $outputTimezone);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public static function isValid(array $data): bool {
        try {
            static::create($data);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Select the class to use to create new date-time instances.
     *
     * @param null|string $dateTimeClass
     *
     * @throws CPeriod_Exception_InvalidDateTimeClass if $dateTimeClass is set with a string that is not a valid DateTimeInterface
     *
     * @return $this
     */
    public function setDateTimeClass($dateTimeClass = null) {
        if ($dateTimeClass !== null && !is_a($dateTimeClass, DateTimeInterface::class, true)) {
            throw CPeriod_Exception_InvalidDateTimeClass::forString($dateTimeClass);
        }

        $this->dateTimeClass = $dateTimeClass ?? DateTime::class;

        return $this;
    }

    /**
     * Set the number of days to try before abandoning the search of the next close/open time.
     *
     * @param int $dayLimit number of days
     *
     * @return $this
     */
    public function setDayLimit(int $dayLimit) {
        $this->dayLimit = $dayLimit;

        return $this;
    }

    /**
     * Get the number of days to try before abandoning the search of the next close/open time.
     *
     * @return int
     */
    public function getDayLimit(): int {
        return $this->dayLimit ?: static::DEFAULT_DAY_LIMIT;
    }

    public function setFilters(array $filters) {
        $this->filters = $filters;

        return $this;
    }

    public function getFilters(): array {
        return $this->filters;
    }

    public function fill(array $data) {
        $timezones = array_key_exists('timezone', $data) ? $data['timezone'] : [];
        unset($data['timezone']);

        if (!is_array($timezones)) {
            $timezones = ['input' => $timezones];
        }

        if (array_key_exists('input', $timezones)) {
            $this->timezone = $this->parseTimezone($timezones['input']);
        }

        if (array_key_exists('output', $timezones)) {
            $this->outputTimezone = $this->parseTimezone($timezones['output']);
        }

        list($openingHours, $exceptions, $metaData, $filters, $overflow, $dateTimeClass) = $this
            ->parseOpeningHoursAndExceptions($data);

        $this->overflow = $overflow;

        foreach ($openingHours as $day => $openingHoursForThisDay) {
            $this->setOpeningHoursFromStrings($day, $openingHoursForThisDay);
        }

        $this->setExceptionsFromStrings($exceptions);

        return $this->setDateTimeClass($dateTimeClass)->setFilters($filters)->setData($metaData);
    }

    public function forWeek(): array {
        return $this->openingHours;
    }

    public function forWeekCombined(): array {
        $equalDays = [];
        $allOpeningHours = $this->openingHours;
        $uniqueOpeningHours = array_unique($allOpeningHours);
        $nonUniqueOpeningHours = $allOpeningHours;

        foreach ($uniqueOpeningHours as $day => $value) {
            $equalDays[$day] = ['days' => [$day], 'opening_hours' => $value];
            unset($nonUniqueOpeningHours[$day]);
        }

        foreach ($uniqueOpeningHours as $uniqueDay => $uniqueValue) {
            foreach ($nonUniqueOpeningHours as $nonUniqueDay => $nonUniqueValue) {
                if ((string) $uniqueValue === (string) $nonUniqueValue) {
                    $equalDays[$uniqueDay]['days'][] = $nonUniqueDay;
                }
            }
        }

        return $equalDays;
    }

    public function forWeekConsecutiveDays(): array {
        $concatenatedDays = [];
        $allOpeningHours = $this->openingHours;
        foreach ($allOpeningHours as $day => $value) {
            $previousDay = end($concatenatedDays);
            if ($previousDay && (string) $previousDay['opening_hours'] === (string) $value) {
                $key = key($concatenatedDays);
                $concatenatedDays[$key]['days'][] = $day;

                continue;
            }

            $concatenatedDays[$day] = [
                'opening_hours' => $value,
                'days' => [$day],
            ];
        }

        return $concatenatedDays;
    }

    /**
     * @param string $day
     *
     * @return CPeriod_OpeningHours_OpeningHoursForDay
     */
    public function forDay($day) {
        $day = $this->normalizeDayName($day);

        return $this->openingHours[$day];
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return CPeriod_OpeningHours_OpeningHoursForDay
     */
    public function forDate(DateTimeInterface $date) {
        $date = $this->applyTimezone($date);

        foreach ($this->filters as $filter) {
            $result = $filter($date);

            if (is_array($result)) {
                return CPeriod_OpeningHours_OpeningHoursForDay::fromStrings($result);
            }
        }

        return $this->exceptions[$date->format('Y-m-d')] ?? ($this->exceptions[$date->format('m-d')] ?? $this->forDay(CPeriod_OpeningHours_Day::onDateTime($date)));
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return TimeRange[]
     */
    public function forDateTime(DateTimeInterface $date): array {
        $date = $this->applyTimezone($date);

        return array_merge(
            iterator_to_array($this->forDate(
                $this->yesterday($date)
            )->forNightTime(CPeriod_OpeningHours_Time::fromDateTime($date))),
            iterator_to_array($this->forDate($date)->forTime(CPeriod_OpeningHours_Time::fromDateTime($date)))
        );
    }

    public function exceptions(): array {
        return $this->exceptions;
    }

    public function isOpenOn(string $day): bool {
        if (preg_match('/^(?:(\d+)-)?(\d{1,2})-(\d{1,2})$/', $day, $match)) {
            list(, $year, $month, $day) = $match;
            $year = $year ?: date('Y');

            return count($this->forDate(new DateTimeImmutable("{$year}-{$month}-{$day}", $this->timezone))) > 0;
        }

        return count($this->forDay($day)) > 0;
    }

    public function isClosedOn(string $day): bool {
        return !$this->isOpenOn($day);
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return bool
     */
    public function isOpenAt(DateTimeInterface $dateTime) {
        $dateTime = $this->applyTimezone($dateTime);

        if ($this->overflow) {
            $dateTimeMinus1Day = $this->yesterday($dateTime);
            $openingHoursForDayBefore = $this->forDate($dateTimeMinus1Day);
            if ($openingHoursForDayBefore->isOpenAtNight(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTimeMinus1Day))) {
                return true;
            }
        }

        $openingHoursForDay = $this->forDate($dateTime);

        return $openingHoursForDay->isOpenAt(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
    }

    public function isClosedAt(DateTimeInterface $dateTime): bool {
        return !$this->isOpenAt($dateTime);
    }

    public function isOpen(): bool {
        return $this->isOpenAt(new $this->dateTimeClass());
    }

    public function isClosed(): bool {
        return $this->isClosedAt(new $this->dateTimeClass());
    }

    public function currentOpenRange(DateTimeInterface $dateTime) {
        $dateTime = $this->applyTimezone($dateTime);
        $list = $this->forDateTime($dateTime);

        return end($list) ?: false;
    }

    public function currentOpenRangeStart(DateTimeInterface $dateTime) {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->applyTimezone($dateTime);
        /** @var TimeRange $range */
        $range = $this->currentOpenRange($dateTime);

        if (!$range) {
            return false;
        }

        $dateTime = $this->copyDateTime($dateTime);

        $nextDateTime = $range->start()->toDateTime();

        if ($range->overflowsNextDay() && $nextDateTime->format('Hi') > $dateTime->format('Hi')) {
            $dateTime = $dateTime->modify('-1 day');
        }

        return $this->getDateWithTimezone(
            $dateTime->setTime($nextDateTime->format('G'), $nextDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    public function currentOpenRangeEnd(DateTimeInterface $dateTime) {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->applyTimezone($dateTime);
        /** @var TimeRange $range */
        $range = $this->currentOpenRange($dateTime);

        if (!$range) {
            return false;
        }

        $dateTime = $this->copyDateTime($dateTime);

        $nextDateTime = $range->end()->toDateTime();

        if ($range->overflowsNextDay() && $nextDateTime->format('Hi') < $dateTime->format('Hi')) {
            $dateTime = $dateTime->modify('+1 day');
        }

        return $this->getDateWithTimezone(
            $dateTime->setTime($nextDateTime->format('G'), $nextDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    public function nextOpen(DateTimeInterface $dateTime = null): DateTimeInterface {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->applyTimezone($dateTime ?? new $this->dateTimeClass());
        $dateTime = $this->copyDateTime($dateTime);
        $openingHoursForDay = $this->forDate($dateTime);
        $nextOpen = $openingHoursForDay->nextOpen(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        $tries = $this->getDayLimit();

        while ($nextOpen === false || $nextOpen->hours() >= 24) {
            if (--$tries < 0) {
                throw CPeriod_OpeningHours_Exception_MaximumLimitExceededException::forString(
                    'No open date/time found in the next ' . $this->getDayLimit() . ' days,'
                    . ' use $openingHours->setDayLimit() to increase the limit.'
                );
            }

            $dateTime = $dateTime
                ->modify('+1 day')
                ->setTime(0, 0, 0);

            if ($this->isOpenAt($dateTime) && !$openingHoursForDay->isOpenAt(CPeriod_OpeningHours_Time::fromString('23:59'))) {
                return $this->getDateWithTimezone($dateTime, $outputTimezone);
            }

            $openingHoursForDay = $this->forDate($dateTime);

            $nextOpen = $openingHoursForDay->nextOpen(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        }

        if ($dateTime->format('H:i') === '00:00' && $this->isOpenAt((clone $dateTime)->modify('-1 second'))) {
            return $this->getDateWithTimezone(
                $this->nextOpen($dateTime->modify('+1 minute')),
                $outputTimezone
            );
        }

        $nextDateTime = $nextOpen->toDateTime();

        return $this->getDateWithTimezone(
            $dateTime->setTime($nextDateTime->format('G'), $nextDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    public function nextClose(DateTimeInterface $dateTime = null): DateTimeInterface {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->applyTimezone($dateTime ?? new $this->dateTimeClass());
        $dateTime = $this->copyDateTime($dateTime);
        $nextClose = null;
        if ($this->overflow) {
            $dateTimeMinus1Day = $this->copyDateTime($dateTime)->modify('-1 day');
            $openingHoursForDayBefore = $this->forDate($dateTimeMinus1Day);
            if ($openingHoursForDayBefore->isOpenAtNight(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTimeMinus1Day))) {
                $nextClose = $openingHoursForDayBefore->nextClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
            }
        }

        $openingHoursForDay = $this->forDate($dateTime);
        if (!$nextClose) {
            $nextClose = $openingHoursForDay->nextClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));

            if ($nextClose && $nextClose->hours() < 24 && $nextClose->format('Gi') < $dateTime->format('Gi')) {
                $dateTime = $dateTime->modify('+1 day');
            }
        }

        $tries = $this->getDayLimit();

        while ($nextClose === false || $nextClose->hours() >= 24) {
            if (--$tries < 0) {
                throw CPeriod_OpeningHours_Exception_MaximumLimitExceededException::forString(
                    'No close date/time found in the next ' . $this->getDayLimit() . ' days,'
                    . ' use $openingHours->setDayLimit() to increase the limit.'
                );
            }

            $dateTime = $dateTime
                ->modify('+1 day')
                ->setTime(0, 0, 0);

            if ($this->isClosedAt($dateTime) && $openingHoursForDay->isOpenAt(CPeriod_OpeningHours_Time::fromString('23:59'))) {
                return $this->getDateWithTimezone($dateTime, $outputTimezone);
            }

            $openingHoursForDay = $this->forDate($dateTime);

            $nextClose = $openingHoursForDay->nextClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        }

        $nextDateTime = $nextClose->toDateTime();

        return $this->getDateWithTimezone(
            $dateTime->setTime($nextDateTime->format('G'), $nextDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return DateTimeInterface
     */
    public function previousOpen(DateTimeInterface $dateTime) {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->copyDateTime($this->applyTimezone($dateTime));
        $openingHoursForDay = $this->forDate($dateTime);
        $previousOpen = $openingHoursForDay->previousOpen(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        $tries = $this->getDayLimit();

        while ($previousOpen === false || ($previousOpen->hours() === 0 && $previousOpen->minutes() === 0)) {
            if (--$tries < 0) {
                throw CPeriod_OpeningHours_Exception_MaximumLimitExceededException::forString(
                    'No open date/time found in the previous ' . $this->getDayLimit() . ' days,'
                    . ' use $openingHours->setDayLimit() to increase the limit.'
                );
            }

            $midnight = $dateTime->setTime(0, 0, 0);
            $dateTime = clone $midnight;
            $dateTime = $dateTime->modify('-1 minute');

            $openingHoursForDay = $this->forDate($dateTime);

            if ($this->isOpenAt($midnight) && !$openingHoursForDay->isOpenAt(CPeriod_OpeningHours_Time::fromString('23:59'))) {
                return $this->getDateWithTimezone($midnight, $outputTimezone);
            }

            $previousOpen = $openingHoursForDay->previousOpen(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        }

        $nextDateTime = $previousOpen->toDateTime();

        return $this->getDateWithTimezone(
            $dateTime->setTime($nextDateTime->format('G'), $nextDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return DateTimeInterface
     */
    public function previousClose(DateTimeInterface $dateTime) {
        $outputTimezone = $this->getOutputTimezone($dateTime);
        $dateTime = $this->copyDateTime($this->applyTimezone($dateTime));
        $previousClose = null;
        if ($this->overflow) {
            $dateTimeMinus1Day = $this->copyDateTime($dateTime)->modify('-1 day');
            $openingHoursForDayBefore = $this->forDate($dateTimeMinus1Day);
            if ($openingHoursForDayBefore->isOpenAtNight(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTimeMinus1Day))) {
                $previousClose = $openingHoursForDayBefore->previousClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
            }
        }

        $openingHoursForDay = $this->forDate($dateTime);
        if (!$previousClose) {
            $previousClose = $openingHoursForDay->previousClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        }

        $tries = $this->getDayLimit();

        while ($previousClose === false || ($previousClose->hours() === 0 && $previousClose->minutes() === 0)) {
            if (--$tries < 0) {
                throw CPeriod_OpeningHours_Exception_MaximumLimitExceededException::forString(
                    'No close date/time found in the previous ' . $this->getDayLimit() . ' days,'
                    . ' use $openingHours->setDayLimit() to increase the limit.'
                );
            }

            $midnight = $dateTime->setTime(0, 0, 0);
            $dateTime = clone $midnight;
            $dateTime = $dateTime->modify('-1 minute');
            $openingHoursForDay = $this->forDate($dateTime);

            if ($this->isClosedAt($midnight) && $openingHoursForDay->isOpenAt(CPeriod_OpeningHours_Time::fromString('23:59'))) {
                return $this->getDateWithTimezone($midnight, $outputTimezone);
            }

            $previousClose = $openingHoursForDay->previousClose(CPeriod_OpeningHours_PreciseTime::fromDateTime($dateTime));
        }

        $previousDateTime = $previousClose->toDateTime();

        return $this->getDateWithTimezone(
            $dateTime->setTime($previousDateTime->format('G'), $previousDateTime->format('i'), 0),
            $outputTimezone
        );
    }

    /**
     * @return array
     */
    public function regularClosingDays() {
        return array_keys($this->filter(static function (CPeriod_OpeningHours_OpeningHoursForDay $openingHoursForDay) {
            return $openingHoursForDay->isEmpty();
        }));
    }

    /**
     * @return array
     */
    public function regularClosingDaysISO() {
        return carr::map($this->regularClosingDays(), [CPeriod_OpeningHours_Day::class, 'toISO']);
    }

    public function exceptionalClosingDates(): array {
        $dates = array_keys($this->filterExceptions(static function (CPeriod_OpeningHours_OpeningHoursForDay $openingHoursForDay) {
            return $openingHoursForDay->isEmpty();
        }));

        return carr::map($dates, static function ($date) {
            return DateTime::createFromFormat('Y-m-d', $date);
        });
    }

    /**
     * @param null|string|DateTimeZone $timezone
     *
     * @return void
     */
    public function setTimezone($timezone) {
        $this->timezone = $this->parseTimezone($timezone);
    }

    /**
     * @param null|string|DateTimeZone $timezone
     *
     * @return void
     */
    public function setOutputTimezone($timezone) {
        $this->outputTimezone = $this->parseTimezone($timezone);
    }

    protected function parseOpeningHoursAndExceptions(array $data): array {
        $dateTimeClass = carr::pull($data, 'dateTimeClass', null);
        $metaData = carr::pull($data, 'data', null);
        $exceptions = [];
        $filters = carr::pull($data, 'filters', []);
        $overflow = (bool) carr::pull($data, 'overflow', false);

        foreach (carr::pull($data, 'exceptions', []) as $key => $exception) {
            if (is_callable($exception)) {
                $filters[] = $exception;

                continue;
            }

            $exceptions[$key] = $exception;
        }

        $openingHours = [];

        foreach ($data as $day => $openingHoursData) {
            $openingHours[$this->normalizeDayName($day)] = $openingHoursData;
        }

        return [$openingHours, $exceptions, $metaData, $filters, $overflow, $dateTimeClass];
    }

    protected function setOpeningHoursFromStrings(string $day, array $openingHours) {
        $day = $this->normalizeDayName($day);

        $data = null;

        if (isset($openingHours['data'])) {
            $data = $openingHours['data'];
            unset($openingHours['data']);
        }

        $this->openingHours[$day] = CPeriod_OpeningHours_OpeningHoursForDay::fromStrings($openingHours)->setData($data);
    }

    protected function setExceptionsFromStrings(array $exceptions) {
        if (empty($exceptions)) {
            return;
        }

        if (!$this->dayLimit) {
            $this->dayLimit = 366;
        }

        $this->exceptions = carr::map($exceptions, static function (array $openingHours, string $date) {
            $recurring = DateTime::createFromFormat('m-d', $date);

            if ($recurring === false || $recurring->format('m-d') !== $date) {
                $dateTime = DateTime::createFromFormat('Y-m-d', $date);

                if ($dateTime === false || $dateTime->format('Y-m-d') !== $date) {
                    throw CPeriod_Exception_InvalidDateException::forFormat($date, 'Y-m-d');
                }
            }

            return CPeriod_OpeningHours_OpeningHoursForDay::fromStrings($openingHours);
        });
    }

    /**
     * @param string $day
     *
     * @return void
     */
    protected function normalizeDayName($day) {
        $day = strtolower($day);

        if (!CPeriod_OpeningHours_Day::isValid($day)) {
            throw CPeriod_OpeningHours_Exception_InvalidDayNameException::invalidDayName($day);
        }

        return $day;
    }

    protected function applyTimezone(DateTimeInterface $date) {
        return $this->getDateWithTimezone($date, $this->timezone);
    }

    /**
     * @param DateTimeInterface $date
     * @param null|DateTimeZone $timezone
     *
     * @return DateTimeInterface
     */
    protected function getDateWithTimezone(DateTimeInterface $date, $timezone) {
        if ($timezone) {
            if ($date instanceof DateTime) {
                $date = clone $date;
            }
            /** @var DateTime $date */
            $date = $date->setTimezone($timezone);
        }

        return $date;
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function filter(callable $callback) {
        return carr::filter($this->openingHours, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function map($callback) {
        return carr::map($this->openingHours, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function flatMap($callback) {
        return CPeriod_OpeningHours_Helper::flatMap($this->openingHours, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function filterExceptions($callback) {
        return carr::filter($this->exceptions, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function mapExceptions($callback) {
        return carr::map($this->exceptions, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function flatMapExceptions($callback) {
        return CPeriod_OpeningHours_Helper::flatMap($this->exceptions, $callback);
    }

    /**
     * @param string $format
     * @param mixed  $timezone
     *
     * @return array
     */
    public function asStructuredData($format = 'H:i', $timezone = null) {
        $regularHours = $this->flatMap(static function (CPeriod_OpeningHours_OpeningHoursForDay $openingHoursForDay, $day) use ($format, $timezone) {
            return $openingHoursForDay->map(static function (CPeriod_OpeningHours_TimeRange $timeRange) use ($format, $timezone, $day) {
                return [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ucfirst($day),
                    'opens' => $timeRange->start()->format($format, $timezone),
                    'closes' => $timeRange->end()->format($format, $timezone),
                ];
            });
        });

        $exceptions = $this->flatMapExceptions(static function (CPeriod_OpeningHours_OpeningHoursForDay $openingHoursForDay, $date) use ($format, $timezone) {
            if ($openingHoursForDay->isEmpty()) {
                $zero = CPeriod_OpeningHours_Time::fromString('00:00')->format($format, $timezone);

                return [[
                    '@type' => 'OpeningHoursSpecification',
                    'opens' => $zero,
                    'closes' => $zero,
                    'validFrom' => $date,
                    'validThrough' => $date,
                ]];
            }

            return $openingHoursForDay->map(static function (CPeriod_OpeningHours_TimeRange $timeRange) use ($format, $date, $timezone) {
                return [
                    '@type' => 'OpeningHoursSpecification',
                    'opens' => $timeRange->start()->format($format, $timezone),
                    'closes' => $timeRange->end()->format($format, $timezone),
                    'validFrom' => $date,
                    'validThrough' => $date,
                ];
            });
        });

        return array_merge($regularHours, $exceptions);
    }

    /**
     * @param array $data
     * @param array $excludedKeys
     *
     * @return Generator
     */
    private static function filterHours(array $data, array $excludedKeys) {
        foreach ($data as $key => $value) {
            if (in_array($key, $excludedKeys, true)) {
                continue;
            }

            if (is_int($key) && is_array($value) && isset($value['hours'])) {
                foreach ((array) $value['hours'] as $subKey => $hour) {
                    yield "{$key}.{$subKey}" => $hour;
                }

                continue;
            }

            yield $key => $value;
        }
    }

    /**
     * @param mixed $timezone
     *
     * @throws CPeriod_Exception_InvalidTimezoneException
     *
     * @return null|DateTimeZone
     */
    private function parseTimezone($timezone) {
        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }

        if (is_string($timezone)) {
            return new DateTimeZone($timezone);
        }

        if ($timezone) {
            throw CPeriod_Exception_InvalidTimezoneException::create();
        }

        return null;
    }

    /**
     * @param null|DateTimeInterface $dateTime
     *
     * @return null|DateTimeZone
     */
    private function getOutputTimezone(DateTimeInterface $dateTime = null) {
        if ($this->outputTimezone !== null) {
            return $this->outputTimezone;
        }

        if ($this->timezone === null || $dateTime === null) {
            return $this->timezone;
        }

        return $dateTime->getTimezone();
    }
}
