<?php

class CElement_FormInput_MapPicker extends CElement_FormInput {
    protected $lat;

    protected $lng;

    protected $mapContainer;

    protected $wrapperContainer;

    protected $searchContainer;

    protected $searchControl;

    protected $haveSearch;

    protected $searchPlaceholder;

    protected $radius;

    protected $draggable;

    protected $scrollwheel;

    protected $markerDraggable;

    protected $markerInCenter;

    protected $geoCodingApiKey;

    protected $searchSelector;

    protected $latitudeSelector;

    protected $longitudeSelector;

    protected $radiusSelector;

    protected $rawJsOnChanged;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'text';
        $this->tag = 'div';
        $this->addClass('form-control');
        //default to Jakarta lat lng
        $this->lat = '-6.200000';
        $this->lng = '106.816666';
        $this->value = $this->lat . ',' . $this->lng;
        $this->wrapperContainer = $this->addDiv($this->id . '-wrapper')->addClass('map-wrapper');
        $this->haveSearch = true;
        $this->searchPlaceholder = 'Search Location';

        $this->radius = 0;
        $this->draggable = true;
        $this->scrollwheel = true;
        $this->markerDraggable = true;
        $this->markerInCenter = true;

        $this->geoCodingApiKey = CF::config('vendor.google.geocoding_api_key');
        $this->rawJsOnChanged = '';
        if (strlen($this->geoCodingApiKey) == 0) {
            throw new Exception('no api key found in config vendor.google.geocoding_api_key');
        }
        CManager::registerModule('locationpicker-googleruntime', [
            'js' => 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $this->geoCodingApiKey . ''
        ]);
        CManager::registerModule('locationpicker');
    }

    public function setValue($val) {
        parent::setValue($val);
        $latlngArray = explode(',', $val);
        $this->lat = carr::get($latlngArray, 0, 0);
        $this->lng = carr::get($latlngArray, 1, 0);

        return $this;
    }

    public function radius($val) {
        return $this->setRadius($val);
    }

    public function setRadius($val) {
        $this->radius = $val;

        return $this;
    }

    public function setLatitudeSelector($latitude) {
        if ($latitude instanceof CRenderable) {
            $latitude = '#' . $latitude->id();
        }
        $this->latitudeSelector = $latitude;

        return $this;
    }

    public function setLongitudeSelector($longitude) {
        if ($longitude instanceof CRenderable) {
            $longitude = '#' . $longitude->id();
        }
        $this->longitudeSelector = $longitude;

        return $this;
    }

    public function setRadiusSelector($selector) {
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        $this->radiusSelector = $selector;

        return $this;
    }

    public function setSearchSelector($selector) {
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        $this->searchSelector = $selector;

        return $this;
    }

    public function setDraggable($bool = true) {
        $this->draggable = $bool;

        return $this;
    }

    public function setScrollwheel($bool = true) {
        $this->scrollwheel = $bool;

        return $this;
    }

    public function markerDraggable($bool = true) {
        $this->markerDraggable = $bool;

        return $this;
    }

    public function markerInCenter($bool = true) {
        $this->markerInCenter = $bool;

        return $this;
    }

    public function setJsOnChanged($js) {
        $this->rawJsOnChanged = $js;

        return $this;
    }

    public function build() {
        //$this->wrapperContainer->add('<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $this->geoCodingApiKey . '" type="text/javascript"></script>');

        if ($this->haveSearch) {
            if ($this->searchSelector == null) {
                $this->searchContainer = $this->wrapperContainer->addDiv()->addClass('mb-3');
                $this->searchControl = $this->searchContainer->addControl($this->id . '-search', 'text')->setPlaceholder($this->searchPlaceholder);
                if (!$this->markerDraggable) {
                    $this->searchControl->setReadonly();
                }
                $this->searchSelector = '#' . $this->id . '-search';
            }
        }

        if ($this->latitudeSelector == null) {
            $this->wrapperContainer->addControl($this->id . '-lat', 'hidden')->setValue($this->lat)->setName($this->name . '[lat]');
            $this->latitudeSelector = '#' . $this->id . '-lat';
        }
        if ($this->longitudeSelector == null) {
            $this->wrapperContainer->addControl($this->id . '-lng', 'hidden')->setValue($this->lng)->setName($this->name . '[lng]');
            $this->longitudeSelector = '#' . $this->id . '-lng';
        }

        $this->wrapperContainer->addDiv($this->id . '-map')
            ->customCss('height', '300px')
            ->add('Loading...');
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $locationPickerJs = "
            $('#" . $this->id . "-map').locationpicker({
                location: {
                    latitude: " . $this->lat . ',
                    longitude: ' . $this->lng . ',
                },

                inputBinding: {
                    latitudeInput: ' . ($this->latitudeSelector ? " $('" . $this->latitudeSelector . "')" : 'null') . ',
                    longitudeInput: ' . ($this->longitudeSelector ? " $('" . $this->longitudeSelector . "')" : 'null') . ',
                    radiusInput: ' . ($this->radiusSelector ? " $('" . $this->radiusSelector . "')" : 'null') . ',
                    locationNameInput: ' . ($this->searchSelector ? " $('" . $this->searchSelector . "')" : 'null') . "
                },
                enableAutocomplete: true,
                enableAutocompleteBlur: true,
                addressFormat: 'street_address',
                draggable: " . json_encode($this->draggable) . ',
                scrollwheel: ' . json_encode($this->scrollwheel) . ',
                radius: ' . $this->radius . ",
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    $('#" . $this->id . "-lat').val(currentLocation.latitude);
                    $('#" . $this->id . "-lng').val(currentLocation.longitude);
                    " . $this->rawJsOnChanged . '
                },
                markerDraggable: ' . json_encode($this->markerDraggable) . ',
                markerInCenter: ' . json_encode($this->markerInCenter) . ',
            });
        ';

        $js->appendln($locationPickerJs);
        $js->append(parent::js());

        return $js->text();
    }
}
