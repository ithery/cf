/* Valid Date formats:
dateFormat.masks = {
		"default":      "ddd mmm dd yyyy HH:MM:ss",
		shortDate:      "m/d/yy",
		mediumDate:     "mmm d, yyyy",
		longDate:       "mmmm d, yyyy",
		fullDate:       "dddd, mmmm d, yyyy",
		shortTime:      "h:MM TT",
		mediumTime:     "h:MM:ss TT",
		longTime:       "h:MM:ss TT Z",
		isoDate:        "yyyy-mm-dd",
		isoTime:        "HH:MM:ss",
		isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
		isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
	};*/
(function($){
	
	var thisClock = new Object();
	
	//Attach this new method to jQuery
 	$.fn.serverTime = function(options) {
		
		var settings = jQuery.extend({
                                    ajaxFile: 'serverTime.php',
									displayDateFormat: "yyyy-mm-dd HH:MM:ss"
                                  }, options);
		
		thisClock.displayDateFormat = settings['displayDateFormat'];
		thisClock.ajaxFile =  settings['ajaxFile'];
		//return "answer";
		//Iterate over the current set of matched elements
		return this.each(function() {
			
			now = new Date();
			//alert('here1'+settings['displayDateFormat']);
		
			//do the ajax call to get server time and then pass to response funciont
			$.fn.serverTime.getServerTime(parseInt(now.getTime() / 1000));	
			
			var serverTimeStamp;
			
			SecondsLeft = 0;
			var now = new Date();
			var serverTimeStamp = $.fn.serverTime.adjustTime (thisClock.timediff, thisClock.secDiffGMT, now); 		//local time - time difference
			
			var serverTime = setInterval("$.fn.serverTime.tickSecond("+serverTimeStamp+", "+thisClock.timediff+", "+thisClock.secDiffGMT+")", 1000);
			

		});
	};
	
	// var $.fn.serverTime.StartDate;
	// var $.fn.serverTime.serverDate;		
	// var $.fn.serverTime.SecondsInAuction ;
	// var $.fn.serverTime.secDiffGMT; //Passed back from php file - $secDiffGMT	= $serverTimeStampEST - $serverTimeStampGMT;
	
	$.fn.serverTime.getServerTime = function (localstamp)
	{
		
		$.ajax({
			async: false,
			method: 'get',
			url : thisClock.ajaxFile+'?localstamp='+localstamp,
			dataType : 'text',
			success: $.fn.serverTime.response
		});
		
	}
			
	$.fn.serverTime.response = function (text) { 
		
		var ajaxResponse		= text.split('|');
		
		thisClock.timediff 		= parseInt(ajaxResponse[0]) * 1000;		
		thisClock.secDiffGMT 	= ajaxResponse[1];
		return;
				
	}	
			
	
	
	$.fn.serverTime.tickSecond = function (serverTimeStamp, timediff, secDiffGMT) {
		
		//alert(serverTimeStamp + '-' + timediff + '-' + secDiffGMT);
		
		var now = new Date();
		TheServerTime = new Date();
		FinishTime  = new Date();
		var CounDownStr = "";
		var seconds;
		var minutes;
		var hours;
		var days;
		var serverTimeStamp;

		serverTimeStamp	= $.fn.serverTime.adjustTime (timediff, secDiffGMT, now);  //Local time + time difference (server time) in milliseconds
		
		//Add secDiffGMT which comes from clock.php, this is the offeset calculated in seconds between GMT and Australia EST
		//All of the time stamps will be rendered as the local time by Javascript, so need to first calc the diffence between local machine and GMT
		//and then add the offset held in secDiffGMT
		
		var localToGMToffset = getTZOffset (); 
	
		var adjustedTimeStamp = parseInt(serverTimeStamp) + (parseInt(localToGMToffset) * 1000) + (parseInt(secDiffGMT) * 1000);
		// alert ("serverTimeStamp = "+serverTimeStamp);
		// alert ("adjustedTimeStamp = "+adjustedTimeStamp);
		TheServerTime.setTime(adjustedTimeStamp);				//used to display the time
			
		//This works - document.getElementById('ServerTime').innerHTML = dateFormat(TheServerTime, "h:MM:ss TT Z");
		//This works too - $('#ServerTime').html(TheServerTime.toString());
		
		//Listen for new data
		$(document).bind('setData', function(evt, key, value) {
			if ( key == 'clock' ) {
				$('#servertime').html( value );
			}
		});
		//alert(thisClock.displayDateFormat);
		$(document).data('clock', (dateFormat(TheServerTime, thisClock.displayDateFormat)).toString() );	
		
	}
	
	/* function updatePage (TheServerTime) {
		var newTime = dateFormat(TheServerTime, "H:MM:ss ") + " AEST";
				
	}*/
			
	$.fn.serverTime.adjustTime = function(timediff, secDiffGMT, now) {
		//This function gets the current local time using javascript and adds the calculated time difference
		
		var nowStamp = new Date(now);
		
		//New time as a MILLISECOND!!!! time stamp					
		var newDateStamp;				
		
		//newDateStamp = parseInt(now.getTime()) - parseInt(timediff); This was the complex problem - not sure why changed it to minus
		newDateStamp = parseInt(nowStamp.getTime()) + parseInt(timediff);
		//alert (newDateStamp);
		return newDateStamp;
	}
	
	function getTZOffset () {
		//If we are west of GMT it returns a positive number. 
		//IF we are east of GMT if returns a negative number.
		var d = new Date()
		//getTimezoneOffset returns mintues, so we turn that into hours
		var getTZOffsetSeconds = d.getTimezoneOffset()*60;
		
		return getTZOffsetSeconds;
	}
	
	/* We are using this to format the server time */
	/*
	 * Date Format 1.2.2
	 * (c) 2007-2008 Steven Levithan <stevenlevithan.com>
	 * MIT license
	 * Includes enhancements by Scott Trenda <scott.trenda.net> and Kris Kowal <cixar.com/~kris.kowal/>
	 *
	 * Accepts a date, a mask, or a date and a mask.
	 * Returns a formatted version of the given date.
	 * The date defaults to the current date/time.
	 * The mask defaults to dateFormat.masks.default.
	 */
	var dateFormat = function () {
		var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
			timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
			timezoneClip = /[^-+\dA-Z]/g,
			pad = function (val, len) {
				val = String(val);
				len = len || 2;
				while (val.length < len) val = "0" + val;
				return val;
			};

		// Regexes and supporting functions are cached through closure
		return function (date, mask, utc) {
			var dF = dateFormat;

			// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
			if (arguments.length == 1 && (typeof date == "string" || date instanceof String) && !/\d/.test(date)) {
				mask = date;
				date = undefined;
			}

			// Passing date through Date applies Date.parse, if necessary
			date = date ? new Date(date) : new Date();
			if (isNaN(date)) throw new SyntaxError("invalid date");

			mask = String(dF.masks[mask] || mask || dF.masks["default"]);

			// Allow setting the utc argument via the mask
			if (mask.slice(0, 4) == "UTC:") {
				mask = mask.slice(4);
				utc = true;
			}

			var	_ = utc ? "getUTC" : "get",
				d = date[_ + "Date"](),
				D = date[_ + "Day"](),
				m = date[_ + "Month"](),
				y = date[_ + "FullYear"](),
				H = date[_ + "Hours"](),
				M = date[_ + "Minutes"](),
				s = date[_ + "Seconds"](),
				L = date[_ + "Milliseconds"](),
				o = utc ? 0 : date.getTimezoneOffset(),
				flags = {
					d:    d,
					dd:   pad(d),
					ddd:  dF.i18n.dayNames[D],
					dddd: dF.i18n.dayNames[D + 7],
					m:    m + 1,
					mm:   pad(m + 1),
					mmm:  dF.i18n.monthNames[m],
					mmmm: dF.i18n.monthNames[m + 12],
					yy:   String(y).slice(2),
					yyyy: y,
					h:    H % 12 || 12,
					hh:   pad(H % 12 || 12),
					H:    H,
					HH:   pad(H),
					M:    M,
					MM:   pad(M),
					s:    s,
					ss:   pad(s),
					l:    pad(L, 3),
					L:    pad(L > 99 ? Math.round(L / 10) : L),
					t:    H < 12 ? "a"  : "p",
					tt:   H < 12 ? "am" : "pm",
					T:    H < 12 ? "A"  : "P",
					TT:   H < 12 ? "AM" : "PM",
					Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
					o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
					S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
				};

			return mask.replace(token, function ($0) {
				return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
			});
		};
	}();

	// Some common format strings
	dateFormat.masks = {
		"default":      "ddd mmm dd yyyy HH:MM:ss",
		shortDate:      "m/d/yy",
		mediumDate:     "mmm d, yyyy",
		longDate:       "mmmm d, yyyy",
		fullDate:       "dddd, mmmm d, yyyy",
		shortTime:      "h:MM TT",
		mediumTime:     "h:MM:ss TT",
		longTime:       "h:MM:ss TT Z",
		isoDate:        "yyyy-mm-dd",
		isoTime:        "HH:MM:ss",
		isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
		isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
	};

	// Internationalization strings
	dateFormat.i18n = {
		dayNames: [
			"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
			"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
		],
		monthNames: [
			"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
			"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
		]
	};

	// For convenience...
	Date.prototype.format = function (mask, utc) {
		return dateFormat(this, mask, utc);
	};

//pass jQuery to the function, 
//So that we will able to use any valid Javascript variable name 
//to replace "$" SIGN. But, we'll stick to $ (I like dollar sign: ) )		
})(jQuery);


			
