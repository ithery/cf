function key_press_number(evt) {
    var char_code = (evt.which) ? evt.which : window.event.keyCode;  

    if (char_code <= 13) { 
        return true; 
    } else { 
        var keyChar = String.fromCharCode(char_code); 
        var re = /[0-9]/ 
        return re.test(keyChar); 
    } 
}


function key_press_number_signed(evt) {
    var char_code = (evt.which) ? evt.which : window.event.keyCode;  

    if (char_code <= 13) { 
        return true; 
    } else { 
        var keyChar = String.fromCharCode(char_code); 
		if(keyChar=="-") return true;
        var re = /[0-9]/ 
        return re.test(keyChar); 
    } 
}


function key_press_float(evt) {
    var char_code = (evt.which) ? evt.which : window.event.keyCode;  

    if (char_code <= 13) { 
        return true; 
    } else { 
        var keyChar = String.fromCharCode(char_code); 
        var re = /[0-9]/ 
        var test =  re.test(keyChar); 
		test = test||keyChar==".";
		return test;
    } 
}
function exit_1_999(c) {
	var p = c.value;
	var i = 1;
	if (p.length>0) i = parseInt(p);
	if (i>999) i=999;
	if (i<0) i=0;
	c.value=i;
}


function is_integer(s) {
  return (s.toString().search(/^-?[0-9]+$/) == 0);
}

function is_valid_date(d, m , y) {
  var dayobj = new Date(y, m-1, d);
  return !((dayobj.getMonth()+1!=m)||(dayobj.getDate()!=d)||(dayobj.getFullYear()!=y));
}

function thousand_separator(rp) {
	rp =""+rp;
	var rupiah = "";
	var vfloat = "";
	
	if (rp.indexOf(".")>=0) {
		vfloat = rp.substring(rp.indexOf("."));
		rp = rp.substring(0,rp.indexOf("."));
	}
	p = rp.length;
	while(p > 3) {
		rupiah = "," + rp.substring(p-3) + rupiah;
		l = rp.length - 3;
		rp = rp.substring(0,l);
		p = rp.length;
	}
	rupiah = rp + rupiah;
	if (vfloat.length>2) vfloat = vfloat.substring(0,3);
	return rupiah+vfloat;
}



function is_valid_email(email, required) {
    if (required==undefined) {   // if not specified, assume it's required
        required=true;
    }
    if (email==null) {
        if (required) {
            return false;
        }
        return true;
    }
    if (email.length==0) {  
        if (required) {
            return false;
        }
        return true;
    }
    if (! allValidChars(email)) {  // check to make sure all characters are valid
        return false;
    }
    if (email.indexOf("@") < 1) { //  must contain @, and it must not be the first character
        return false;
    } else if (email.lastIndexOf(".") <= email.indexOf("@")) {  // last dot must be after the @
        return false;
    } else if (email.indexOf("@") == email.length) {  // @ must not be the last character
        return false;
    } else if (email.indexOf("..") >=0) { // two periods in a row is not valid
  return false;
    } else if (email.indexOf(".") == email.length) {  // . must not be the last character
  return false;
    }
    return true;
}

function all_valid_chars(email) {
  var parsed = true;
  var validchars = "abcdefghijklmnopqrstuvwxyz0123456789@.-_";
  for (var i=0; i < email.length; i++) {
    var letter = email.charAt(i).toLowerCase();
    if (validchars.indexOf(letter) != -1)
      continue;
    parsed = false;
    break;
  }
  return parsed;
}

function is_whitespace(char_to_check) {
        var whitespaceChars = " \t\n\r\f";
        return (whitespaceChars.indexOf(char_to_check) != -1);
}

function ltrim(str) {
        for(var k = 0; k < str.length && is_whitespace(str.charAt(k)); k++);
        return str.substring(k, str.length);
}
function rtrim(str) {
        for(var j=str.length-1; j>=0 && is_whitespace(str.charAt(j)) ; j--) ;
        return str.substring(0,j+1);
}
function trim(str) {
        return ltrim(rtrim(str));
}
function days_between(date1, date2) {

	// The number of milliseconds in one day
	var ONE_DAY = 1000 * 60 * 60 * 24;

	// Convert both dates to milliseconds
	var date1_ms = date1.getTime();
	var date2_ms = date2.getTime();

	// Calculate the difference in milliseconds
	var difference_ms = date2_ms - date1_ms;
	
	// Convert back to days and return
	return Math.round(difference_ms/ONE_DAY);

}

