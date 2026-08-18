<?php
use Carbon\Carbon;

class CComponent_RenameMe_SupportDateTimes {
    public static function init() {
        return new static();
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('property.dehydrate', function ($name, $value, $component, $response) {
            if (!$value instanceof \DateTime) {
                return;
            }

            $component->{$name} = $value->format(\DateTimeInterface::ISO8601);

            c::fill($response->memo, 'dataMeta.dates', []);

            if ($value instanceof CCarbon) {
                $response->memo['dataMeta']['dates'][$name] = 'illuminate';
            } elseif ($value instanceof Carbon) {
                $response->memo['dataMeta']['dates'][$name] = 'carbon';
            } else {
                $response->memo['dataMeta']['dates'][$name] = 'native';
            }
        });

        CComponent_Manager::instance()->listen('property.hydrate', function ($name, $value, $component, $request) {
            $dates = c::get($request->memo, 'dataMeta.dates', []);

            $types = [
                'native' => DateTime::class,
                'carbon' => Carbon::class,
                'illuminate' => CCarbon::class,
            ];

            foreach ($dates as $name => $type) {
                c::set($component, $name, new $types[$type]($value));
            }
        });
    }
}
