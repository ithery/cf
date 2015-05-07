<?php

    class CCalendar extends CElement {

        protected $html;
        protected $css;
        protected $js;
//        protected $month;
        protected $dialog_url = '';
//        protected $day_content = array();
        protected $month_names = array();
        protected $prev_month;
        protected $curr_month;
        protected $next_month;
        protected $next_year;
        protected $curr_year;
        protected $prev_year;
        protected $curr_date;
        protected $url;
        protected $ajax_url;
        protected $use_navigate = true;
        protected $use_edit = true;
        protected $use_modal = false;
        protected $selected_value;
        protected $title = "Dialog";
        protected $http_method = 'POST';
        protected $data_calendar = array();
        protected $attributes = array();
        protected $post_data = array();

        public function __construct($id) {
            parent::__construct($id);
//            $this->dialog_url='';
//            $this->input_month_id = $id."_selected_month";
//            $this->input_year_id = $id."_selected_year";
//            $this->input_selected_day_id = $id."_selected_day";

            $this->month_names = array(
                clang::__('January'),
                clang::__('February'),
                clang::__('March'),
                clang::__('April'),
                clang::__('May'),
                clang::__('June'),
                clang::__('July'),
                clang::__('August'),
                clang::__('September'),
                clang::__('October'),
                clang::__('November'),
                clang::__('December'),
            );

            $this->curr_date = date("Y-m-d");
            $this->curr_month = date('n', strtotime($this->curr_date));
            $this->curr_year = date("Y", strtotime($this->curr_date));
            $this->prev_year = $this->curr_year;
            $this->next_year = $this->curr_year;
            $this->data_calendar = array();
        }

        public function html($indent = 0) {
            $this->calculate_month_year();

            $html = new CStringBuilder();

            $html->appendln($this->css());

            $html->appendln('<div>');
            $html->appendln('<div class="row" id="' . $this->id . '">
                                <div class="span12">');
            if ($this->use_navigate) {
                $html->appendln($this->button());
            }
            if ($this->use_edit) {
                $html->appendln($this->button_edit());
            }

            // body
            $html->appendln('<div id="calendar-content">');
            $html->appendln('<input id="select_val" type="hidden" value="" />');
            $html->appendln($this->selected_day());
            $timestamp = mktime(0, 0, 0, $this->curr_month, 1, $this->curr_year);
            $max_day = date("t", $timestamp);
            $this_month = getdate($timestamp);
            $start_day = $this_month['wday'];
            $html->appendln('<ol id="selectable">');
            $modal_data = '';
            for ($i = 0; $i < ($max_day + $start_day); $i++) {
                //if(($i % 7) == 0 ) echo "<tr>";
                if ($i < $start_day) {
                    $html->appendln("<li class='blank-date'></li>");
                }
                else {
                    $li_right = "";
                    $the_date = ($i - $start_day + 1);
                    $the_date_complete = $the_date . ' ' . $this_month['month'] . ' ' . $this_month['year'];
                    $the_date_complete_convert = date('Y-m-d', strtotime($the_date_complete));
                    if (($i + 1) % 7 == 0) {
                        $li_right = "right-over";
                    }
                    elseif (($i % 7) == 0) {
                        $li_right = "left-over";
                    }

                    $date_content = $the_date;

                    $curr_data_date = carr::get($this->data_calendar, $the_date_complete_convert);

                    $id = carr::get($curr_data_date, 'id', '');
                    if (strlen($id) > 0) $id = ' id = "' . $id . '"';
                    $date_content .= carr::get($curr_data_date, 'content', '<br><br><span style="font-size:12px;color:red;">-</span>');

                    $attributes = '';
                    $all_attributes = carr::get($curr_data_date, 'attr', array());
                    foreach ($all_attributes as $all_attributes_k => $all_attributes_v) {
                        $attributes .= $all_attributes_k . '="' . $all_attributes_v . '" ';
                    }

                    foreach ($this->attributes as $attr_k => $attr_v) {
                        $attributes .= $attr_k . '="' . $attr_v . '" ';
                    }
                    if($this->use_modal) {
                        $modal_data .= $this->modal($the_date_complete, $the_date_complete_convert, $date_content);
                    }
                    $html->appendln('<li data-date="' . $the_date_complete_convert . '" ' . $id . ' '
                            . $attributes . ' class="' . $li_right . '">' . $date_content . '</li>');
                }

                //            if(($i % 7) == 6 ) echo "<li class='blank-date'>".$i."</li>";
            }
            $html->appendln('</ol>');
            $html->appendln('</div>');
            // end body

            $html->appendln('</div>');

            $html->appendln('</div>');

            $html->appendln('</div>');
            $html->appendln($modal_data);
            return $html->text();
        }

        public function button() {
            return '<div class="btn-group">
                        <a month="' . $this->prev_month . '" year="' . $this->prev_year . '" '
                    . 'href="javascript:void(0)" class="btn-large btn calendar-link-action" action="prev-month">'
                    . '<i class="icon icon-arrow-left"></i></a>'
                    . '<label class="btn btn-large btn-info">' . $this->month_names[$this->prev_month] . ' ' . $this->curr_year . '</label>'
                    . '<a month="' . $this->next_month . '" year="' . $this->next_year . '" 
                        href="javascript:void(0)" class="btn-large btn calendar-link-action" action="next-month">
                        <i class="icon icon-arrow-right"></i></a>
                    </div>';
        }
        public function modal($the_date_complete, $the_date_complete_convert, $date_content) {
            return '<div class="modal fade" id="modal' . $the_date_complete_convert . '">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">' . $the_date_complete . '</h4>
                          </div>
                          <div class="modal-body">
                            ' . $date_content . '
                          </div>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->';
        }

        public function button_edit() {
            return '<div class="btn-group"><a id="btn_edit" class="btn-large btn" href="javascript:void(0)" action="next-month">Edit</a></div>';
        }

        public function selected_day() {
            return '<ol id="selectable-day">
                        <li>' . clang::__('Sunday') . '</li>
                        <li>' . clang::__('Monday') . '</li>
                        <li>' . clang::__('Tuesday') . '</li>
                        <li>' . clang::__('Wednesday') . '</li>
                        <li>' . clang::__('Thursday') . '</li>
                        <li>' . clang::__('Friday') . '</li>
                        <li>' . clang::__('Saturday') . '</li>
                    </ol>';
        }

        public function js($indent = 0) {
            $selected_date = "jQuery('#select_val').val()";
            $params = "";
            foreach ($this->attributes as $attr_k => $attr_v) {
                $params .= ", '" . $attr_k . "': '" . $attr_v . "'";
            }
            foreach ($this->post_data as $k => $v) {
                $params .= ", '" . $k . "': " . $v;
            }
            $post_data = "{'selected-date':" . $selected_date . $params . "}";
            $return = "jQuery('#btn_edit').click(function() {                         
			$.cresenity.show_dialog('" . uniqid() . "','" . $this->dialog_url . "','" . $this->http_method
                    . "','" . $this->title . "'," . $post_data . ");
                    });";

            $return .= "
                    var month;
                    var year;
                    var action;
                    var month_names = JSON.parse('" . json_encode($this->month_names) . "');
                        
                    jQuery('.calendar-link-action').on('click', function() {
                        
                        month = jQuery(this).attr('month');
                        year = jQuery(this).attr('year');
                        action = jQuery(this).attr('action');
                        //alert(month);                          
                        refresh_" . $this->id . "(action);
                    });
                    
                    function refresh_" . $this->id . "(action) {
                        var cctrl_" . $this->id . " = jQuery('#" . $this->id . " #calendar-content');
                        cctrl_" . $this->id . ".html('<i class=\"icon-spinner icon-spin icon-large \"></i>');
                        cctrl_" . $this->id . ".addClass('loading');

                        jQuery.ajax({
                            url:'" . $this->url . "',
                            type:'get',
                            dataType:'json',
                            data: {
                                'month': month,
                                'year': year,
                                'calendar_id':'" . $this->id . "'
                                " . $params . "
                            }
                        }).done(function(data){                                      
                            cctrl_" . $this->id . ".html(data.html);
                            cctrl_" . $this->id . ".removeClass('loading');        
                            selectable_event();

                            jQuery('.btn-info').text(month_names[(month-1)] + ' ' + year);
                            
                            var other_month = month;
                            var other_year = year;
                            var other_action;
                            if (action == 'prev-month') {      
                                other_action = 'next-month';
                                other_month = other_month * 1 + 1;
                                if (other_month > 12) {
                                    other_month = 1;
                                    other_year = other_year * 1 + 1;
                                } 
                                                                                                   
                                month = month * 1 - 1;                                
                                if (month < 1) {
                                    month = 12;
                                    year = year * 1 - 1;
                                }                                
                            }
                            else {         
                                other_action = 'prev-month';
                                other_month = other_month * 1 - 1;                                
                                if (other_month < 1) {
                                    other_month = 12;
                                    other_year = other_year * 1 - 1;
                                } 
                                                   
                                month = month * 1 + 1;
                                if (month > 12) {
                                    month = 1;
                                    year = year * 1 + 1;
                                }   
                            }
                            jQuery('.calendar-link-action[action=\'' + other_action + '\']').attr('month', other_month);
                            jQuery('.calendar-link-action[action=\'' + other_action + '\']').attr('year', other_year);  
                            jQuery('.calendar-link-action[action=\'' + action + '\']').attr('month', month);
                            jQuery('.calendar-link-action[action=\'' + action + '\']').attr('year', year);                                                             
                        }).error(function(data) {

                        });
                    }
                    ";

            $modal_js = '';

            if($this->use_modal) {
                $modal_js = "$('#modal'+date_selected).modal('toggle');";
            }

            $return .= "$(function() {
                            var res_date = '';
                            
                            selectable_event();
                            
                        });
                        function selectable_event(){ 
                            $( '#selectable' ).selectable({
                                    start : function(event, ui){

                                    },
                                    selected: function(event, ui) {
                                            var date_selected = $(ui.selected).attr('data-date');
                                            " . $modal_js . "
                                    },
                                    stop : function(event, ui){
                                            var result_date = $( '#select-result-date' ).empty();

                                            var all_date_selected = [];
                                            $( 'li.ui-selected', this ).each(function() {
                                                    var index = $( '#selectable li' ).index( this );
                                                    var val_date = {
                                                        'id' : $(this).attr('id'),
                                                        'date': $(this).attr('data-date')
                                                    };

                                                    all_date_selected.push(val_date);
                                            });
                                            res_date = JSON.stringify(all_date_selected);                                            
                                            jQuery('#select_val').val(res_date);
                                    }
                            });
                        }
";

            return $return;
        }

        /**
         * This function is used to create new Calendar
         * 
         * @param string $id
         * @return \CCalendar
         */
        public static function factory($id) {
            return new CCalendar($id);
        }

        protected function calculate_month_year() {
            $this->prev_month = $this->curr_month - 1;
            if ($this->prev_month < 1) {
                $this->prev_month = 12;
                $this->prev_year = $this->curr_year - 1;
            }

            $this->next_month = $this->curr_month + 1;
            if ($this->next_month > 12) {
                $this->next_month = 1;
                $this->next_year = $this->curr_year + 1;
            }
        }

        protected function css() {
            return '<style>
                        #cms-calendar {
                        }
                        #feedback { font-size: 1.4em; }
                        #selectable .ui-selecting { background-color: #FECA40; }
                        #selectable .ui-selected { background-color: #F39814; color: white; }
                        #selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
                        #selectable li { 
                            /*border: 1px solid #CCCCCC;*/
                            margin: 0px; 
                            padding: 1px; 
                            float: left; 
                            width: 14%; 
                            height: 80px; /*font-size: 4em;*/
                            font-size: 1em; 
                            text-align: left; 
                            background-color: #FEFEFE;
                            font-weight: bold;
                        }
                        #selectable li:hover {
                            cursor: pointer;
                        }
                        #selectable .blank-date { background-color: #F5F5F5; }
                        #selectable .right-over { border-right: 1px solid #D9D7D9; }
                        #selectable .left-over { border-left: 1px solid #D9D7D9; }
                        #selectable-day { list-style-type: none; margin: 0; padding: 0; width: 100%; }
                        #selectable-day li { 
                            margin: 0px; 
                            padding: 1px; 
                            float: left; 
                            width: 14%; 
                            height: 40px; /*font-size: 4em;*/
                            font-size: 1em; 
                            text-align: center; 
                        }
                    </style>';
        }

        public function get_url() {
            return $this->url;
        }

        public function set_url($url) {
            $this->url = $url;
            return $this;
        }

        public function set_dialog_url($url) {
            $this->dialog_url = $url;
            return $this;
        }

        public function set_month($month) {
            $this->curr_month = $month;
            return $this;
        }

        public function set_year($year) {
            $this->curr_year = $year;
        }

        public function set_navigate($use_navigate) {
            $this->use_navigate = $use_navigate;
        }

        public function set_edit($use_edit) {
            $this->use_edit = $use_edit;
        }

        public function set_modal($use_modal) {
            $this->use_modal = $use_modal;
        }

        public function get_selected_value() {
            return $this->selected_value;
        }

        public function add_data_calendar($key, $value) {
            $this->data_calendar[$key] = $value;
            return $this;
        }

        public function set_data_calendar($data_calendar) {
            $this->data_calendar = $data_calendar;
            return $this;
        }

        public function get_data_calendar() {
            return $this->data_calendar;
        }

        public function get_title() {
            return $this->title;
        }

        public function set_title($title) {
            $this->title = $title;
            return $this;
        }

        public function get_http_method() {
            return $this->http_method;
        }

        public function set_http_method($http_method) {
            $this->http_method = $http_method;
            return $this;
        }

        public function set_attributes($attributes) {
            $this->attributes = $attributes;
            return $this;
        }

        public function get_attributes() {
            return $this->attributes;
        }

    }

    