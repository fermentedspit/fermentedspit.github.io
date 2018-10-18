/* From scottandrew.com via simon.incutio.com */

var __DEBUG_VALIDATION = false;

var cachedBackgroundColor = [];

function addEvent(obj, evType, fn, useCapture) {
	if (obj && obj.addEventListener) {
		obj.addEventListener(evType, fn, useCapture);
		return true;
	} else if (obj && obj.attachEvent) {
		var r = obj.attachEvent("on" + evType, fn);
		return r;
	} else {
		// alert('Handler could not be attached');
		return false;
	}
};

// capture the event type (used for blur events)
var formevent;

// fetch xml file
var xmlDoc;
function importXML(url, func) {
	xmlDoc = false;
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest && !(window.ActiveXObject)) {
		try {
			xmlDoc = new XMLHttpRequest();
		} catch (e) {
			xmlDoc = false;
		}
		// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		try {
			xmlDoc = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlDoc = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				xmlDoc = false;
			}
		}
	}
	if (xmlDoc) {
		xmlDoc.onreadystatechange = processReqChange;
		xmlDoc.open("GET", url, true);
		xmlDoc.send("");
	}
};

/**
 * Get document name and calculate xml document to be imported.
 */
function getXmlUrl() {

	// Build 631
	var d = new Date();
	var randts = d.getTime();
	url = phppath + pageName + '?rand=' + randts;
	//alert(url);
	return url;

	// legacy
	var url = window.location.href;
	// if it ends with a get query, remove the query
	url = url.split('?')[0];
	// remove any anchor links
	url = url.split('#')[0];
	//Grab the filename
	if (url.match(/\w+\.[a-zA-Z0-9]+$/) !== null) {
		//Replace filename.extension with filename.xml
		url = url.match(/\w+\.[a-zA-Z0-9]+$/).toString();
	} else {
		url = 'index.php';
	}
	var dot = url.lastIndexOf('.');
	// 631 mod_rewrite protection
	if (dot == -1) {
		dot = url.length;
	}
	url = url.substring(0, dot);
	// Build 623 - added random seed
	var d = new Date();
	var randts = d.getTime();
	url = phppath + url + '.xml?rand=' + randts;
	//alert(url);
	return url;
};

function processReqChange() {
	// only if req shows "loaded"
	if (xmlDoc.readyState == 4) {
		// only if "OK"
		if (xmlDoc.status == 200) {
			parseFormXml();
		} else {
			alert("There was a problem retrieving the XML data:\n"
					+ xmlDoc.statusText);
		}
	}
};

// parse form
function parseFormXml() {
	
	form = document.getElementById(xmlDoc.responseXML.documentElement
			.getAttribute('id'));
	xmlDoc = xmlDoc.responseXML;
	
	// external lib check
	if (form === null) {
		// prototype
		if (typeof Prototype !== 'undefined') {
			form = $(xmlDoc.documentElement.getAttribute('id'));
		}
	}
	
	//onsubmit, form is checked
	if (!addEvent(form, 'submit', checkForm, false)) { // if using an image as submit
		
		addEvent(form, 'click', checkForm, false); // look for click event instead
	
	}
	
	validators = new Array();
	elements = xmlDoc.getElementsByTagName('element');
	
	// Create element validator objects and add to array
	for ( var i = 0; i < elements.length; i++) {
		
		val = new ElementValidator(elements[i]);
		validators[val.id] = val;

		if(val.element != null){
			
			if(val.element.type == 'radio' || val.element.type == 'checkbox'){
				
				addEvent(val.element, 'click', onClickCheck, false); // Click	
			
			} else if (val.element.type == "select-one" || val.element.type == "file") {
				
				addEvent(val.element, 'change', onChangeCheck, false); // Change
				
			} else {
				
				addEvent(val.element, 'blur', onblurCheck, false); // Blur
				
			}
			
		}	
		
	}
	
};

/**
 * Called on form submit; cycle through make sure all elements are valid.
 * 
 * @param event
 * @returns {Boolean}
 */
function checkForm(event) {
	
	formevent = 'submit';
	
	var pass = true;
	
	for ( var i in validators) {
		
		// External librairies such as Prototype will often extend properties and functions on to our elements, this check makes sure we only get valid elements.        
		if (typeof validators[i].check != 'undefined') {
			
			validators[i].check();
			
			if (validators[i].valid == false) {
				
				if(__DEBUG_VALIDATION)
					console.log("Failed Validation For Field: ", validators[i]);
				
				pass = false;
				
			}
		}
	}

	if (pass == false) {
		
		message = jspopup_errormessage;
		if (showAlert) {
			
			alert(message);
		
		}
		
		// Stop form submit

		if (document.all) { // IE

			if(document.getElementById && document.compatMode && window.XMLHttpRequest && document.documentMode && window.ScriptEngineMajorVersion() && window.ScriptEngineMajorVersion() >= 9){
				event.preventDefault();
			}
			
			// Pre-IE 9
			event.cancelBubble = true
			event.returnValue = false;
			
		}
		
		//standard w3c model - moz
		
		else {
			
			if(typeof(event) != "undefined") {
				
				event.preventDefault();
				
			}

		}
		
	} else { // Build 633 - Return For Image Submit
		
		return pass;
	
	}
	
	// enable submit button
	fb.enable_submit();
}

function onblurCheck(event) {
	//ie
	if (document.all) {
		id = event.srcElement.getAttribute('id');
	}
	//moz
	else {
		id = this.getAttribute('id');
	}
	validators[id].check();
}

function onChangeCheck(event) {
	//ie
	if (document.all) {
		id = event.srcElement.getAttribute('id');
	}
	//moz
	else {
		id = this.getAttribute('id');
	}
	validators[id].check();
}

function onClickCheck(event) {
	//ie
	if (document.all) {
		id = event.srcElement.getAttribute('id');
	}
	//moz
	else {
		id = this.getAttribute('id');
	}
	validators[id].check();
}


// Element Validator object
function ElementValidator(node) {
	this.id = node.getAttribute('id');
	this.element = document.getElementById(this.id);
	this.valid = true;
	this.min = node.getAttribute('min');
	this.max = node.getAttribute('max');
	
	// Build 668
	this.countmethod = node.getAttribute('countmethod');
	
	this.req = node.getAttribute('req') == "true";
	this.regs = new Array();
	regexes = node.getElementsByTagName('regex');
	for ( var i = 0; i < regexes.length; i++) {
		//grab text inside regex tag(s)
		this.regs[i] = new RegExp(regexes[i].childNodes[0].nodeValue);
	}
	
	// Build 828 - Add New Required Error Message Property.
	var reqerr = node.getElementsByTagName('requirederrormessage')[0];
	if (reqerr != null) {
		if(typeof(reqerr.childNodes[0]) != "undefined"){
			this.requirederrormessage = reqerr.childNodes[0].nodeValue;
		} else{
			this.requirederrormessage = "";
		}
		
	} else {
		this.requirederrormessage = null;
	}
	
	
	var err = node.getElementsByTagName('error')[0];
	if (err != null) {
		this.error = err.childNodes[0].nodeValue;
	} else {
		this.error = null;
	}
	this.name = node.getAttribute('name');
	this.sameAs = node.getAttribute('sameas');
	//get reference to element node that value should equal
	if (this.sameAs != null) {
		this.sameAs = document.getElementById(this.sameAs);
	}

	// enclosing elt - this caches our class name for resetting later during makeValid()
	if (this.element !== null) {
		this.parentClass = this.element.parentNode.className;
		this._cached_id = ""; // default value
	}

	this.check = function() {

		// Confirmation value. Must be same as confirming value + valid for confirming value's rules
		// Build 651 - Added element check and abort for IE
		if (this.element != null) {
			type = this.element.getAttribute('type');
		} else {
			return;
		}

		// Build 717 - Process Hidden Elements
		// Inspect each element and check if its parent container is hidden.
		
		// Create special case for calendar items, as they have a different naming convention.
		// That is, calendar items can pass three fields, we need to remove these extra bits.
		base_name = this.element.id;
		base_name = base_name.replace("date-", "");
		base_name = base_name.replace("-1", "");
		base_name = base_name.replace("-2", ""); // Build 724 - Check all(!) fields.
		base_name = base_name.replace("-3", "");
		
		if(base_name != this.element.id){
			
			if(document.getElementById('fb_fld-' + base_name).style.display == 'none'){
				this.makeValid(); // if no, mark as valid by default
				return;
			}
			
		} else { // all other items get standard check...
			
			if(type == 'checkbox' || type ==  'radio'){
				
				// Build 717 - Is this item visible?
				field_name = document.getElementById(this.element.id).getAttribute('name'); // get main node name, not indexed version (e.g. radio0 instead of radio01)
				
				// checkboxes have [], remove it
				field_name = field_name.replace('[]', '');
				
				var parent_invisible = hasInvisibleParentNode(document.getElementById('fb_fld-' + field_name));
				
				var self_invisible = document.getElementById('fb_fld-' + field_name).style.display == 'none' ? true : false;
				
				if(document.getElementById(this.element.id).style.display == 'none' || parent_invisible == true || self_invisible){
					this.makeValid(); // if no, mark as valid by default
					return;
				}
				
			} else {
		
				// Build 717 - Is this item or any of its parents invisible?
				parent_invisible = hasInvisibleParentNode(document.getElementById('fb_fld-' + this.element.id));
				if(document.getElementById('fb_fld-' + this.element.id).style.display == 'none' || parent_invisible == true){
					this.makeValid(); // if no, mark as valid by default
					return;
				}
				
			}
			
		}
		
		
		// process non-text elements, iterate through each
		switch (type) {
		
		case 'checkbox':
			element_name = this.element.name;
			form_name = this.element.form.id;
			element_id = this.element.id;
			var x = document.getElementsByName(element_name);

			if ((this.req == false)) {
				this.makeValid();
				return;
			}

			// default false
			pass = false;
			
			// Build 706 - how many are checked logic
			checked_count = 0;
			for (i = 0; i < x.length; i++) {
				if(x[i].checked){
					checked_count++;
				}				
			}
			
			if ((this.min != null && checked_count < this.min)
					|| (this.max != null && checked_count > this.max)) {
				this.makeInvalid(this.checkboxLenErrMsg()); // new error handler for this logic
				return;
			}
			
			for (i = 0; i < x.length; i++) {

				var chk = x[i].checked;
				if (this.req && chk == true) {
					pass = true;
					this.makeValid();
					return;
				} else if ((this.req == false) && (chk == false)) {
					this.makeInvalid(this.reqErrMsg());
					return;
				}
			}
		
			
			if (pass == true) {
				pass = true;
				this.makeValid();
				return;
			} else {
				this.makeInvalid(this.reqErrMsg());
				return;
			}
			break;
			
		case 'radio':
			element_name = this.element.name;
			form_name = this.element.form.id;
			element_id = this.element.id;
			var x = document.getElementsByName(element_name);

			if ((this.req == false)) {
				this.makeValid();
				return;
			}

			// default false
			pass = false;
			for (i = 0; i < x.length; i++) {

				var chk = x[i].checked;
				if (this.req && chk == true) {
					pass = true;
					this.makeValid();
					return;
				} else if ((this.req == false) && (chk == false)) {
					this.makeInvalid(this.reqErrMsg());
					return;
				}
			}
			if (pass == true) {
				pass = true;
				this.makeValid();
				return;
			} else {
				this.makeInvalid(this.reqErrMsg());
				return;
			}
			break;

		}
		//
		if (this.sameAs != null) {
			if (this.element.value == this.sameAs.value) {
				if (validators[this.sameAs.getAttribute('id')].valid == true) {
					this.makeValid();
				} else {
					// This is triggered if a user hasn't filled in the first element of a same-as block
					var otherName = validators[this.sameAs.getAttribute('id')].name;
					this.makeInvalid('Your ' + otherName + ' is not correct.');
					// You can change this message to something else if need be, for example:
					//this.makeInvalid('Please fill out ' + otherName + ' first.');
				}
			} else {
				var otherName = validators[this.sameAs.getAttribute('id')].name;
				var msg = 'This value must be identical to your ' + otherName + '.';
				this.makeInvalid(msg);
			}
		} else {
			var val = this.element.value;
			//first check for required
			if (this.req && val == "") {
				this.makeInvalid(this.reqErrMsg());
				return;
			}
			//if not required and no value, is valid
			else if ((this.req == false) && (val == "")) {
				this.makeValid();
				return;
			}
			
			// check for length :: Build 668 - character or word count
			switch(this.countmethod){
				case null :
					if ((this.min != null && val.length < this.min)
							|| (this.max != null && val.length > this.max)) {
						this.makeInvalid(this.lenErrMsg());
						return;
					}
					break;
				case 'cntChars' :
					if ((this.min != null && val.length < this.min)
							|| (this.max != null && val.length > this.max)) {
						this.makeInvalid(this.lenErrMsg());
						return;
					}
					break;
				case 'cntWords' :
					var y = val;
					var r = 0;
					a = y.replace(/\s/g, ' ');
					a = a.split(' ');
					for (z = 0; z < a.length; z++) {
						if (a[z].length > 0)
							r++;
					}
					if ((a.length != null && a.length < this.min)
							|| (this.max != null && a.length > this.max)) {
						this.makeInvalid(this.lenErrMsg());
						return;
					}
					break;
			
			}

			
			//check that it matches at least one supplied regex, if any supplied
			if (this.regs.length > 0) {
				var pass = false;
				for ( var i = 0; i < this.regs.length; i++) {
					if (this.regs[i].test(val)) {
						pass = true;
						break;
					}
				}
				if (pass == false) {
					this.makeInvalid(this.error);
					return;
				}
			}
			
			//passed all tests
			this.makeValid();
		}
		
	}
	
	// Build 706
	this.checkboxLenErrMsg = function() {
		var cap = this.name.substring(0, 1).toUpperCase();
		var capName = cap + this.name.substring(1, this.name.length);

		if (this.min != null && this.max != null) {
			// if min and max are the same
			if(this.min == this.max){
				ret = "You must select " + this.min + " items";
			} else {
				ret = 'You must select between ' + this.min + ' and ';
				ret += this.max + ' items.';
			}
			return ret;
		} else if (this.min != null) {
			return 'You must select at least ' + this.min + ' items.';
		} else //max, no min
		{
			return 'You cannot select more than ' + this.max + ' items.';
		}
	};

	this.lenErrMsg = function() {
		var cap = this.name.substring(0, 1).toUpperCase();
		var capName = cap + this.name.substring(1, this.name.length);
		if (this.min != null && this.max != null) {
			ret = capName + ' must be between ' + this.min + ' and ';
			ret += this.max + ' characters.';
			return ret;
		} else if (this.min != null) {
			return capName + ' must be more than ' + this.min + ' characters.';
		} else //max, no min
		{
			return capName + ' must be less than ' + this.max + ' characters.';
		}
	};

	this.reqErrMsg = function() {
		letter = this.name.substring(0, 1);
		switch (letter) {
		case 'a':
		case 'e':
		case 'i':
		case 'o':
		case 'u':
			word = 'an';
			break;
		default:
			word = 'a';
			break;
		}

		// Build 584
		val = this.name.replace(/_/g, " ");

		// Build 586 - Updated Build 828 To Include Custom Error Message For Required Fields.
		if (showDefault) {
			if(this.requirederrormessage != ""){
				return this.requirederrormessage;
			} else {
				return '-You must supply ' + word + ' ' + val + '.';
			}
			
		} else {
			return this.error;
		}

	};

	
	this.makeInvalid = function(errMsg) {
		
		switch(errorStyle){
		
			case 0 :
				
				//
				// Field Highlight
				//
				
				if(cachedBackgroundColor[this.id] === undefined){
					
					cachedBackgroundColor[this.id] = this.element.style.backgroundColor;
					
				}
				
				// Build 912 - What type of field is this?
				
				if(this.element.type == "radio" || this.element.type == "checkbox") {
					
					var _parent = getParentNodeByClassName('form-field-wrapper', this.element);
					
					if(_parent != null && _parent !== undefined) {
						
						if(_parent.className.indexOf('error') == -1) {
							
							_parent.className = _parent.className + " error";
							
						}
						
					}
					
				} else { // Text, textarea, select

					if (this.valid == false) {
						
						this.element.style.backgroundColor = errorColor;
						
					} else {
						
						this.element.style.backgroundColor = errorColor;
						
					}
					
				}
				
				if(__DEBUG_VALIDATION)
					console.log("Setting Invalid Field On Element: ", this.element.id);
				
				this.valid = false
				
				break;
				
			case 1 :
				
				//
				// Block Highlight
				//
				
				if(this.element.type == "radio" || this.element.type == "checkbox") {
					
					var _parent = getParentNodeByClassName('form-field-wrapper', this.element);
					
					if(_parent != null && _parent !== undefined) {
						
						if(__DEBUG_VALIDATION)
							console.log("Setting Element: ", this.element.id, " Class Name: ", _parent.className);
						
						if(_parent.className.indexOf('error') == -1) {
							
							_parent.className = _parent.className + " error";
							
						}

					}
					
				} else {
					
					this.element.parentNode.className = this.parentClass + " error";
					
					// Insert error message
					// If already invalid will have an error message already
					
					if (this.valid == false) {
						
						errorNode = document.getElementById(this.id + 'errmsg');
						if (showMessage) {
							textNode = document.createTextNode(errMsg);
						} else {
							textNode = document.createTextNode('');
						}
						//childnodes[0] is the old error text
						errorNode.replaceChild(textNode, errorNode.childNodes[0]);
						
					} else {
						
						//create and add to <li> a span with an error message
						span = document.createElement('span');
						span.className = "errormsg";
						span.setAttribute("id", this.id + "errmsg");
						
						if (showMessage) {
							textNode = document.createTextNode(errMsg);
						} else {
							textNode = document.createTextNode('');
						}
						
						span.appendChild(textNode);
						this.element.parentNode.appendChild(span);
						
					}
					
				}

				this.valid = false;
				
				break;

			case 2 :
				
				//
				// Simple Icon
				//
				
				// get id of node
				var node = this.element;
				
				while(node !== null){	
					
					if(node.id.search(/fb_fld-[\S]/) != -1){
						
						idtmp = node.id.split('fb_fld-');
						id = idtmp[1];
						
						if(__DEBUG_VALIDATION)
							console.log("Checking Icon Element: ", id);
						
						// Calendar Fields. 
						if(!document.getElementById(id)) {
							
							if(document.getElementById('date-' + id + '-1')){
								
								id = 'date-' + id + '-1';
								
							}
							
						}
						
						document.getElementById(id + '-validation-style-3-icon').style.display = "block";
						
						if(tablemode != 1){
							document.getElementById(id + '-validation-style-3-line').style.display = "block";
						}
						
						if(showMessage && tablemode != 1 && layout != 1){
							document.getElementById(id + '-validation-style-3-message').innerHTML = errMsg;
							document.getElementById(id + '-validation-style-3-message').style.display = "block";
						}
						
						// custom style for tablemode - places icons to the direct left of field.
						if(tablemode == 1){
							document.getElementById(id + '-validation-style-3-icon').style.right = "-8px";
							document.getElementById(id + '-validation-style-3-icon').style.top = "-13px";
						}
						
						this._cached_id = id;
						break;
						
					} else {
						node = node.parentNode;
						
					}
				}

			
				this.valid = false;
			
				break;
		
		}

		// Build 663
		if(formevent == 'submit'){ // only scroll to when we submit
			
			ScrollToElement(document.getElementById('fb_fld-' + this.id));
			
		}
		
		formevent = null;
		
	};

	
	this.makeValid = function() {
		
		switch(errorStyle){
		
			case 0 :
				
				//
				// Field Highlght
				//

				if(cachedBackgroundColor[this.id] !== undefined){
					
					if(this.element.type == 'radio' || this.element.type == 'checkbox'){
						
						var _parent = getParentNodeByClassName('form-field-wrapper', this.element);
						
						if(_parent != null && _parent !== undefined) {
							
							_parent.className = _parent.className.replace('error', '').trim();
							
						}
						
					} else {
						
						if(cachedBackgroundColor[this.id] != ""){
							
							this.element.style.backgroundColor = cachedBackgroundColor[this.id];
							
						} else {
							
							this.element.style.backgroundColor = "";
							
						}
						
					}
					
				}
				
				if(__DEBUG_VALIDATION)
					console.log("Setting Valid Field On Element: ", this.element.id);

				this.valid = true;

				break;	
				
			case 1 :
				
				//
				// Block Highlight
				//
				
				if(this.element.type == 'radio' || this.element.type == 'checkbox'){
					
					var _parent = getParentNodeByClassName('form-field-wrapper', this.element);
					
					if(_parent != null && _parent !== undefined) {
						
						_parent.className = _parent.className.replace('error', '').trim();
						
					}
					
				} else {
					
					// remove error message if present
					if (this.valid == false) {
						errorNode = document.getElementById(this.id + 'errmsg');
						this.element.parentNode.removeChild(errorNode);
					}
					
					this.element.parentNode.className = this.parentClass;
					
				}
				
				this.valid = true;
				
				break;
				
			case 2 :
				
				//
				// Simple Icon
				//
				
				// Clear items
				if(this._cached_id !== "" && this._cached_id !== undefined){
					
					// hide icon 
					document.getElementById(this._cached_id + '-validation-style-3-icon').style.display = "none";
					
					// hide line 
					document.getElementById(this._cached_id + '-validation-style-3-line').style.display = "none";
					
					// hide message 
					document.getElementById(this._cached_id + '-validation-style-3-message').style.display = "none";
					
				}
				
				this.valid = true;
				

				break;
		}
	};
	
}

// Build 663
function ScrollToElement(theElement) {

	var selectedPosX = 0;
	var selectedPosY = 0;

	while (theElement != null) {
		selectedPosX += theElement.offsetLeft;
		selectedPosY += theElement.offsetTop;
		theElement = theElement.offsetParent;
	}

	window.scrollTo(selectedPosX, selectedPosY);

}

function cntMCEChars(w, eid, mx) {
	var y = w.length;
	document.getElementById('count_' + eid).innerHTML = y;
	document.getElementById('left_' + eid).innerHTML = (mx - y);
	if (y > mx) {
		document.getElementById(eid + '_err').innerHTML = "Too many characters!";
	} else {
		document.getElementById(eid + '_err').innerHTML = "";
	}
}

function cntChars(w, eid, mx) {
	var y = w.value.length;
	document.getElementById('count_' + eid).innerHTML = y;
	document.getElementById('left_' + eid).innerHTML = (mx - y);
	if (y > mx) {
		document.getElementById(eid + '_err').innerHTML = "Too many characters!";
	} else {
		document.getElementById(eid + '_err').innerHTML = "";
	}
}

function cntMCEWords(w, eid, mx) {
	var y = w;
	var r = 0;
	a = y.replace(/\s/g, ' ');
	a = a.split(' ');
	for (z = 0; z < a.length; z++) {
		if (a[z].length > 0)
			r++;
	}
	document.getElementById('count_' + eid).innerHTML = a.length;
	document.getElementById('left_' + eid).innerHTML = (mx - a.length);
	if (a.length > mx) {
		document.getElementById(eid + '_err').innerHTML = "Too many words!";
	} else {
		document.getElementById(eid + '_err').innerHTML = "";
	}
}

function cntWords(w, eid, mx) {
	var y = w.value;
	var r = 0;
	a = y.replace(/\s/g, ' ');
	a = a.split(' ');
	for (z = 0; z < a.length; z++) {
		if (a[z].length > 0)
			r++;
	}
	document.getElementById('count_' + eid).innerHTML = a.length;
	document.getElementById('left_' + eid).innerHTML = (mx - a.length);
	if (a.length > mx) {
		document.getElementById(eid + '_err').innerHTML = "Too many words!";
	} else {
		document.getElementById(eid + '_err').innerHTML = "";
	}
}

function hasInvisibleParentNode(node){
	
	if(typeof(node) == "undefined") { return false; }
	if(typeof(node) === null) { return false; }
	if(typeof(node.id) === null || node.id == "") { return false; } // Build 893
	
	while(node.parentNode !== null){
		node = node.parentNode;
		if(node.style!== undefined && node.style.display == 'none'){
			return true;
		} else {
			hasInvisibleParentNode(node);
		}
	}
	
	return false;
}

/**
 * Utility Functions for common tasks
 */

var fb = {

	RackForms_Utility_Version :'637',

	/**
	 * Toggle a field's default value on and off, but only if the fields value
	 * has not been changed by the user.
	 * @since 637
	 * @author nicSoft
	 */
	toggle : {

		fb_flds :new Array(),
		fb_flds_original :new Array(),
		fb_tripped :new Array(),

		// Save a text fields value and id to an array
		save : function(elt) {
			id = elt.id;
			value = elt.value;
			// save unique id in hash
			hit = false;
			for (i = 0; i < fb_tripped.length; i++) {
				if (fb_tripped[i] == id) {
					hit = true;
				}
			}
			// remove on first hit
			if (!hit) {
				fb_tripped.push(id);
				// store the value
				fb_flds[id] = value;
				// remove the value
				document.getElementById(id).value = "";
			} else {
				// remove if later hit
				for (i = 0; i < fb_tripped.length; i++) {
					if (fb_tripped[i] == id && fb_flds[id] == value) {
						document.getElementById(id).value = "";
					}
				}
			} // else
		},

		revert : function(elt) {
			id = elt.id;
			value = elt.value;
			if (value == '') {
				document.getElementById(id).value = fb_flds[id];
			}
		}
	},
	
	// end toggle
	
	button_elt: '',
	button_text: '',
	
	disable_submit: function(event){
		
		elt = fbc.getSrcElement(event);
		//elt.disabled = true; // breaks Chrome
		elt.enabled = false; 
		
		// save elt
		this.button_elt = elt;
		
		// save value
		this.button_text = elt.value;
		
		// write new value
		elt.value = 'Working...';
		
	},
	
	enable_submit: function(){
		
		if(this.button_elt != '' && this.button_text != ''){

			//this.button_elt.disabled = false; // breaks Chrome
			this.button_elt.enabled = true; 
			this.button_elt.value = this.button_text;
			
		}
		
	}
	
	// end disable_submit
	


}; // end fb



function checkCookie(){
    var cookieEnabled=(navigator.cookieEnabled)? true : false   
    if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled){ 
        document.cookie="testcookie";
        cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)? true : false;
    }
    return (cookieEnabled)?true:showCookieFail();
}

function showCookieFail(){

	// Disable Form Submit Button On Fail.
	var buttons = document.getElementsByTagName('input');
	for (var i = 0; i < buttons.length; i++) {
	    var button = buttons[i];
	    var type = button.getAttribute('type') || 'submit'; // Submit is the default

	    if(type == "submit"){
	    	button.disabled = true;
	    }
	    
	}

	alert("ERROR: Cookies are blocked or not supported by your browser. You must enable cookies to use this form.");
}


/**
 * IE 5.5+, Firefox, Opera, Chrome, Safari XHR object
 * 
 * https://gist.github.com/Xeoncross/7663273
 * 
 * Example Usage:
 * 

<script type="text/javascript">

function save_callback() {
	alert('Form Data Saved.');
}

function save() {

	// Call AJAX
	ajax('page0_process.php', save_callback, ajax.serialize(this.form), null);

}
			
</script>

 * 
 * @param string url
 * @param object callback
 * @param mixed data
 * @param null x
 */
var ajax = function(url, callback, data, cache) {

    // Must encode data
    if(data && typeof(data) === 'object') {
        var y = '', e = encodeURIComponent;
        for (x in data) {
            y += '&' + e(x) + '=' + e(data[x]);
        }
        data = y.slice(1) + (! cache ? '&_t=' + new Date : '');
    }

    try {
        var x = new(this.XMLHttpRequest || ActiveXObject)('MSXML2.XMLHTTP.3.0');
        x.open(data ? 'POST' : 'GET', url, 1);
        x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        x.onreadystatechange = function () {
            x.readyState > 3 && callback && callback(x.responseText, x);
        };
        x.send(data)
    } catch (e) {
        window.console && console.log(e);
    }
};

ajax.uriEncode = function(o) {
    var x, y = '', e = encodeURIComponent;
    for (x in o) y += '&' + e(x) + '=' + e(o[x]);
    return y.slice(1);
};

ajax.collect = function(a, f) {
    var n = [];
    for (var i = 0; i < a.length; i++) {
        var v = f(a[i]);
        if (v != null) n.push(v);
    }
    return n;
};

ajax.serialize = function(f) {
    function g(n) {
        return f.getElementsByTagName(n);
    };
    var nv = function (e) {
        if (e.name) return encodeURIComponent(e.name) + '=' + encodeURIComponent(e.value);
    };
    var i = ajax.collect(g('input'), function (i) {
        if ((i.type != 'radio' && i.type != 'checkbox') || i.checked) return nv(i);
    });
    var s = ajax.collect(g('select'), nv);
    var t = ajax.collect(g('textarea'), nv);
    return i.concat(s).concat(t).join('&');
};


/**
 * Date Functions
 */

/*
* Date Format 1.2.3
* (c) 2007-2009 Steven Levithan <stevenlevithan.com>
* MIT license
*
* Includes enhancements by Scott Trenda <scott.trenda.net>
* and Kris Kowal <cixar.com/~kris.kowal/>
*
* Accepts a date, a mask, or a date and a mask.
* Returns a formatted version of the given date.
* The date defaults to the current date/time.
* The mask defaults to dateFormat.masks.default.
* 
* Example Usage:
* 
* dateFormat(addDays(new Date(), 5), "mmmm dd, yyyy")
* 
* Creates a formatted date 5 days in the future.
* 
* Used within calendar item:
* 
* calcalendar1.addDisabledDates(null, dateFormat(addDays(new Date(), 5), "mmmm dd, yyyy"));
* 
*/

var dateFormat = function() {
	
	var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g, timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g, timezoneClip = /[^-+\dA-Z]/g, pad = function(
			val, len) {
		val = String(val);
		len = len || 2;
		while (val.length < len)
			val = "0" + val;
		return val;
	};

	// Regexes and supporting functions are cached through closure
	return function(date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1
				&& Object.prototype.toString.call(date) == "[object String]"
				&& !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date))
			throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var _ = utc ? "getUTC" : "get", d = date[_ + "Date"](), D = date[_
				+ "Day"](), m = date[_ + "Month"](), y = date[_ + "FullYear"](), H = date[_
				+ "Hours"](), M = date[_ + "Minutes"](), s = date[_ + "Seconds"]
				(), L = date[_ + "Milliseconds"](), o = utc ? 0 : date
				.getTimezoneOffset(), flags = {
			d : d,
			dd : pad(d),
			ddd : dF.i18n.dayNames[D],
			dddd : dF.i18n.dayNames[D + 7],
			m : m + 1,
			mm : pad(m + 1),
			mmm : dF.i18n.monthNames[m],
			mmmm : dF.i18n.monthNames[m + 12],
			yy : String(y).slice(2),
			yyyy : y,
			h : H % 12 || 12,
			hh : pad(H % 12 || 12),
			H : H,
			HH : pad(H),
			M : M,
			MM : pad(M),
			s : s,
			ss : pad(s),
			l : pad(L, 3),
			L : pad(L > 99 ? Math.round(L / 10) : L),
			t : H < 12 ? "a" : "p",
			tt : H < 12 ? "am" : "pm",
			T : H < 12 ? "A" : "P",
			TT : H < 12 ? "AM" : "PM",
			Z : utc ? "UTC" : (String(date).match(timezone) || [ "" ]).pop()
					.replace(timezoneClip, ""),
			o : (o > 0 ? "-" : "+")
					+ pad(
							Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o)
									% 60, 4),
			S : [ "th", "st", "nd", "rd" ][d % 10 > 3 ? 0
					: (d % 100 - d % 10 != 10) * d % 10]
		};

		return mask.replace(token, function($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default" : "ddd mmm dd yyyy HH:MM:ss",
	shortDate : "m/d/yy",
	mediumDate : "mmm d, yyyy",
	longDate : "mmmm d, yyyy",
	fullDate : "dddd, mmmm d, yyyy",
	shortTime : "h:MM TT",
	mediumTime : "h:MM:ss TT",
	longTime : "h:MM:ss TT Z",
	isoDate : "yyyy-mm-dd",
	isoTime : "HH:MM:ss",
	isoDateTime : "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime : "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames : [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sunday",
			"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	monthNames : [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
			"Sep", "Oct", "Nov", "Dec", "January", "February", "March",
			"April", "May", "June", "July", "August", "September", "October",
			"November", "December" ]
};

// For convenience...
Date.prototype.format = function(mask, utc) {
	return dateFormat(this, mask, utc);
};

function addDays(theDate, days) {
	return new Date(theDate.getTime() + days * 24 * 60 * 60 * 1000);
}
