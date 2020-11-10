<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "text";
        $this->tag = 'div';
        $this->addClass('form-control');

        CManager::registerModule('locationpicker');
        //default to Jakarta lat lng
        $this->lat = '-6.200000';
        $this->lng = '106.816666';
        $this->value = $this->lat . ',' . $this->lng;
        $this->wrapperContainer = $this->addDiv($this->id . '-wrapper')->addClass('map-wrapper');
        $this->haveSearch = true;
        $this->searchPlaceholder = 'Search Location';

        $this->radius = 300;
        $this->draggable = true;
        $this->scrollwheel = true;
        $this->markerDraggable = true;
        $this->markerInCenter = true;

        $this->geoCodingApiKey = CF::config('vendor.google.geocoding_api_key');
    }

    public function setValue($val) {
        parent::setValue($val);
        $latlngArray = explode(",", $val);
        $this->lat = carr::get($latlngArray, 0,0);
        $this->lng = carr::get($latlngArray, 1,0);
    }

    public function radius($val) {
        $this->radius = $val;
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

    public function build() {
        if (strlen($this->geoCodingApiKey) == 0) {
            throw new Exception('no api key found in config vendor.google.geocoding_api_key');
        }
       
        $this->wrapperContainer->add('<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key='.$this->geoCodingApiKey.'" type="text/javascript"></script>');
        
        if ($this->haveSearch) {
            $this->searchContainer = $this->wrapperContainer->addDiv()->addClass('mb-3');
            $this->searchControl = $this->searchContainer->addControl($this->id . '-search', 'text')->setPlaceholder($this->searchPlaceholder);
            if (! $this->markerDraggable) {
                $this->searchControl->setReadonly();
            }
        }


        $this->wrapperContainer->addControl($this->id . '-lat', 'hidden')->setValue($this->lat)->setName($this->name . '[lat]');
        $this->wrapperContainer->addControl($this->id . '-lng', 'hidden')->setValue($this->lng)->setName($this->name . '[lng]');


        $this->wrapperContainer->addDiv($this->id . '-map')
                ->customCss('height', '300px')
                ->add('Loading...');
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $miniColorJs = "
            $('#" . $this->id . "-map').locationpicker({
                location: {
                    latitude: " . $this->lat . ",
                    longitude: " . $this->lng . ",
                },

                inputBinding: {
                    latitudeInput: $('#" . $this->id . "-lat'),
                    longitudeInput: $('#" . $this->id . "-lng'),
                    radiusInput: null,
                    locationNameInput: $('#" . $this->id . "-search')
                },
                
                enableAutocomplete: true,
                enableAutocompleteBlur: true,
                addressFormat: 'street_address',
                draggable: " . json_encode($this->draggable) . ",
                scrollwheel: " . json_encode($this->scrollwheel) . ",
                radius: " . $this->radius . ",
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    $('#" . $this->id . "-lat').val(currentLocation.latitude);
                    $('#" . $this->id . "-lng').val(currentLocation.longitude);
                },
                markerDraggable: " . json_encode($this->markerDraggable) . ",
                markerInCenter: " . json_encode($this->markerInCenter) . ",
            });
        ";
        
        $js->appendln($miniColorJs);
        $js->append(parent::js());

        return $js->text();
    }

}
