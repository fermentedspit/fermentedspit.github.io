/**
 * Conditional Code Helper Script
 * @author Nicsoft LLC
 * @version 914
 */
var fbc = {

    RackForms_Utility_Version: '914',

    animationDone: function (elt) { },

    working: false,
    
    debug: true,
    debugSelectorLogic: false,
    debugAnimationLogic: false,

    _disableTypes: Array('SELECT', 'INPUT', 'TEXTAREA'),

    // Hides a field on page load
    hideOnLoad: function () {

    },

    _disableField: function (elt) {
        if (document.getElementById(elt)) {
            elt.disabled = true;
            this.working = true;
        }
    },

    _enableField: function (elt) {
        if (document.getElementById(elt)) {
            elt.disabled = false;
            this.working = false;

            // Build 695 - Prevent Focus Check.
            if (elt.visible) {
                elt.focus();
            }

            // Build 710
            this.animationDone(elt);
        }
    },

    getSrcElement: function (evt) {
        var elt;

        event = evt || window.event;

        if (!event) var event = window.event;
        if (event.target) elt = event.target;
        else if (event.srcElement) elt = event.srcElement;
        if (elt.nodeType == 3) // defeat Safari bug
            elt = elt.parentNode;

        return elt;
    },
    
    fade: function ( elem, time, callback )
    {
    	var startOpacity = elem.style.opacity || 1;
    	elem.style.opacity = startOpacity;
    	
    	if(fbc.debugAnimationLogic)
    		console.log("Fade Start Opacity:", startOpacity);

    	(function go() {
    		elem.style.opacity -= startOpacity / ( time / 100 );
    		
    		if(fbc.debugAnimationLogic)
        		console.log("Fade: Animated Opacity Value:", elem.style.opacity);

    		// for IE
    		elem.style.filter = 'alpha(opacity=' + elem.style.opacity * 100 + ')';

    		if( elem.style.opacity > 0 ){
    			setTimeout( go, 20 );
    		} else {
    			elem.style.display = 'none';
    			
    			if(typeof(callback) == "function") {
    				callback();
    			}
    		}
    			
    	})();
    },
    
    appear: function ( elem, time, display_type, callback )
    {
    	
    	if(typeof(display_type) == "undefined") {
    		display_type = 'block';
    	}
    	
    	var startOpacity = elem.style.opacity || 0;
    	elem.style.opacity = startOpacity;
    	
    	if( elem.style.opacity < 1 ) {
    		elem.style.display = display_type;
    	} else {
    		if(typeof(callback) == "function") {
				callback();
			}
    		return;
    	}
			
    	
    	if(fbc.debugAnimationLogic)
    		console.log("Appear: Start Opacity:", elem.id, startOpacity, time / 100);

    	(function go() {
    		elem.style.opacity = parseFloat(elem.style.opacity) + 0.1;
    		
    		if(fbc.debugAnimationLogic)
        		console.log("Appear Animated Opacity Value:", elem.style.opacity);

    		// for IE
    		elem.style.filter = 'alpha(opacity=' + elem.style.opacity * 100 + ')';

    		if( elem.style.opacity < 1 ) {
    			setTimeout( go, 20 );
    		} else {
    			if(typeof(callback) == "function") {
    				callback();
    			}
    		}
    			
    			
    	})();
    },
    
    hide: function(fieldId, event, animation) {

    	if(fbc.debug)
    		console.log("Showing Field", fieldId);
    	
    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
    	if(fbc.debug)
    		console.log( "Event Status Of Hide Field Call", typeof(event), event );
    	
    	if(typeof(event) != "undefined" && event !== null) {
    		
    		var elt = this.getSrcElement(event);
            this._disableField(elt);
            
        	switch (animation) {
    	        case 'fade':
    	            this.working = true;
    	            
    	            var elm = document.getElementById('fb_fld-' + fieldId);
    	            fbc.fade(elm, 500, function () { fbc._enableField(elt); } );
    	            
    	            break;
    	        case 'blind':
    	            this.working = true;
    	            $('#fb_fld-' + fieldId).toggle("blind", function () { fbc._enableField(elt); });
    	            break;
    	        default:
    	            document.getElementById('fb_fld-' + fieldId).style.display = 'none';
    	            this._enableField(elt);
    	            break;
        	}
        	
    	} else {
    		
    		document.getElementById('fb_fld-' + fieldId).style.display = 'none';
    		
    	}
    	

    	if (document.getElementById(fieldId) != null) {

    		document.getElementById(fieldId).disabled = true;
    		
    	} else {
    		
    		// radio, checkbox
    		for(i = 0; i < 120; i++){
    			if (document.getElementById(fieldId + i) != null) {
    	        	document.getElementById(fieldId + i).disabled = true;
    	        	
    	        	if(fbc.debug)
    	        		console.log("Disabling Field:", fieldId + i);
    			}
    		}
    		
    		// calendar
    		if (document.getElementById('date-' + fieldId + '-1') != null) {
	        	document.getElementById('date-' + fieldId + '-1').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-1');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-2') != null) {
	        	document.getElementById('date-' + fieldId + '-2').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-2');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-3') != null) {
	        	document.getElementById('date-' + fieldId + '-3').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-3');
			}
    		
    	}
    	
    	
    },

    hideField: function (event, fieldId, animation) {
    	
    	if(fbc.debug)
    		console.log("Hiding Field:", fieldId);

    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
        var elt = this.getSrcElement(event);
        this._disableField(elt);
        
        if (document.getElementById(fieldId) != null) {
        	document.getElementById(fieldId).disabled = true;
    	} else {
    		
    		// radio, checkbox
    		for(i = 0; i < 120; i++){
    			if (document.getElementById(fieldId + i) != null) {
    	        	document.getElementById(fieldId + i).disabled = true;
    	        	
    	        	if(fbc.debug)
    	        		console.log("Disabling Field:", fieldId + i);
    			}
    		}
    		
    		// calendar
    		if (document.getElementById('date-' + fieldId + '-1') != null) {
	        	document.getElementById('date-' + fieldId + '-1').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-1');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-2') != null) {
	        	document.getElementById('date-' + fieldId + '-2').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-2');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-3') != null) {
	        	document.getElementById('date-' + fieldId + '-3').disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disable Calendar Field:", 'date-' + fieldId + '-3');
			}
    		
    	}

        switch (animation) {
            case 'fade':
                this.working = true;
                $('#fb_fld-' + fieldId).toggle("fade", function () { fbc._enableField(elt); });
                break;
            case 'blind':
                this.working = true;
                $('#fb_fld-' + fieldId).toggle("blind", function () { fbc._enableField(elt); });
                break;
            default:
                document.getElementById('fb_fld-' + fieldId).style.display = 'none';
                this._enableField(elt);
                break;
        }

    },
    
    show: function(fieldId, event, animation) {

    	if(fbc.debug)
    		console.log("Showing Field:", fieldId);
    	
    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
    	if(fbc.debug)
    		console.log( "Event Status Of Hide Field Call", typeof(event) );

    	if(typeof(event) != "undefined" && event !== null) {
    		
    		var elt = this.getSrcElement(event);
            this._disableField(elt);
            
        	switch (animation) {
    	        case 'fade':
    	            this.working = true;
    	            
    	            var elm = document.getElementById('fb_fld-' + fieldId);
    	            fbc.appear(elm, 250, 'table-row', function () { fbc._enableField(elt); } );

    	            break;
    	        case 'blind':
    	            this.working = true;
    	            $('#fb_fld-' + fieldId).toggle("blind", function () { fbc._enableField(elt); });
    	            break;
    	        default:
    	            document.getElementById('fb_fld-' + fieldId).style.display = 'block';
    	            this._enableField(elt);
    	            break;
        	}
        	
    	} else {
    		
    		document.getElementById('fb_fld-' + fieldId).style.display = 'block';
    		
    	}
    	
    	
    	
    	if (document.getElementById(fieldId) != null) {

    		document.getElementById(fieldId).disabled = false;
    		
    	} else {
    		
    		// radio, checkbox
    		for(i = 0; i < 120; i++){
    			if (document.getElementById(fieldId + i) != null) {
    	        	document.getElementById(fieldId + i).disabled = false;
    	        	
    	        	if(fbc.debug)
    	        		console.log("Enabling Field:", fieldId + i);
    			}
    		}
    		
    		// calendar
    		if (document.getElementById('date-' + fieldId + '-1') != null) {
	        	document.getElementById('date-' + fieldId + '-1').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-1');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-2') != null) {
	        	document.getElementById('date-' + fieldId + '-2').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-2');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-3') != null) {
	        	document.getElementById('date-' + fieldId + '-3').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-3');
			}
    		
    	}
    	
    	
    },

    showField: function (event, fieldId, animation) {

    	if(fbc.debug)
    		console.log("Showing Field:", fieldId);
    	
    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
        var elt = this.getSrcElement(event);
        this._disableField(elt);
        
        if (document.getElementById(fieldId) != null) {
        	document.getElementById(fieldId).disabled = false;
    	} else {
    		
    		// radio, checkbox
    		for(i = 0; i < 120; i++){
    			if (document.getElementById(fieldId + i) != null) {
    	        	document.getElementById(fieldId + i).disabled = false;
    	        	
    	        	if(fbc.debug)
    	        		console.log("Enabling Field:", fieldId + i);
    			}
    		}
    		
    		// calendar
    		if (document.getElementById('date-' + fieldId + '-1') != null) {
	        	document.getElementById('date-' + fieldId + '-1').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-1');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-2') != null) {
	        	document.getElementById('date-' + fieldId + '-2').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-2');
			}
    		
    		if (document.getElementById('date-' + fieldId + '-3') != null) {
	        	document.getElementById('date-' + fieldId + '-3').disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Enabling Calendar Field:", 'date-' + fieldId + '-3');
			}
    		
    	}

        switch (animation) {
            case 'fade':
                this.working = true;
                $('#fb_fld-' + fieldId).toggle("fade", function () { fbc._enableField(elt); });
                break;
            case 'blind':
                this.working = true;
                $('#fb_fld-' + fieldId).toggle("blind", function () { fbc._enableField(elt); });
                break;
            default:
                document.getElementById('fb_fld-' + fieldId).style.display = 'block';
                this._enableField(elt);
                break;
        }

    },

    disableField: function (event, fieldId, animation) {

        if (!document.getElementById(fieldId))
            return;

        var elt = this.getSrcElement(event);
        this._disableField(elt);

        document.getElementById(fieldId).disabled = true;
        this._enableField(elt);

    },

    enableField: function (event, fieldId, animation) {

    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
        if (!document.getElementById(fieldId))
            return;

        var elt = this.getSrcElement(event);
        this._disableField(elt);

        document.getElementById(fieldId).disabled = false;
        this._enableField(elt);

    },
    
    hasClassName: function(element, className) {
    	
    	if(fbc.debug)
    		console.log("Calling fbc.hasClassName With Element", element, 'Class Name:', className);
    	
    	var classList = element.className.split(/\s+/); 
    	for (var i = 0; i < classList.length; i++) {
    	    if (classList[i] === className) {
    	        return true;
    	    }
    	}
    	return false;
    },

    hideWrapper: function (event, wrapperId, animation) {

    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
    	if(event != null) {
    		
    		var elt = this.getSrcElement(event);
            this._disableField(elt);
    		
    	}
        
        switch (animation) {
            case 'fade':
                items = $('.' + wrapperId);
                items.each(function (idx, elt) {
                	if(fbc.debug)
                    	console.log("hideWrapper with fade:", elt.id);

                    $(this).toggle("fade", function () {
                    	document.getElementById(elt.id.replace('fb_fld-', '')).disabled = true;
                    });
                });
                break;
            case 'blind':
                items = $('.' + wrapperId);
                items.each(function (idx, elt) {
                	if(fbc.debug)
                    	console.log("hideWrapper with blind:", elt.id);
                    $(this).toggle("blind", function () { 
                    	document.getElementById(elt.id.replace('fb_fld-', '')).disabled = true;
                    });
                });
                break;
            default:
                // freeform, sortable
                i = 0;
                a = document.getElementsByTagName("div");
                while (element = a[i++]) {
                	if (element.hasAttribute("rf-field") && fbc.hasClassName(element, wrapperId) ) {
                        hit = true;
                        element.style.display = 'none';
                        
                        // disable form element
                        if(element.querySelector){
                        	var elem = element.querySelector('input, select, textarea');
                        	if(elem !== null){
                        		elem.disabled = true;
                        		if(fbc.debug)
                                	console.log("Disable Field:", elem.id, elem);
                        	}
                        	
                        }
                        
                    }
                }

                this._enableField(elt);
                break;
        }

    },

    showWrapper: function (event, wrapperId, animation) {

    	if(typeof(animation) == "undefined" || animation == ""){
    		animation = "";
    	}
    	
    	if(event != null) {
    		
    		var elt = this.getSrcElement(event);
            this._disableField(elt);
    		
    	}

    	switch (animation) {
            case 'fade':
                items = $('.' + wrapperId);
                items.each(function (idx, elt) {
                	if(fbc.debug)
                    	console.log("showWrapper with fade:", elt.id);
                	// enabled field
                	document.getElementById(elt.id.replace('fb_fld-', '')).disabled = false;
                    $(this).toggle("fade", function () {  });
                });
                break;
            case 'blind':
                items = $('.' + wrapperId);
                items.each(function (idx, elt) {
                	if(fbc.debug)
                    	console.log("showWrapper with blind:", elt.id);
                	document.getElementById(elt.id.replace('fb_fld-', '')).disabled = false;
                    $(this).toggle("blind", function () { });
                });
                break;
            default:
            	// freeform, sortable
                i = 0;
                a = document.getElementsByTagName("div");
                while (element = a[i++]) {
                    if (element.hasAttribute("rf-field") && fbc.hasClassName(element, wrapperId) ) {
                        hit = true;
                        element.style.display = 'block';
                        
                        // enable form element
                        if(element.querySelector){
                        	var elem = element.querySelector('input, select, textarea');
                        	if(elem !== null){
                        		elem.disabled = false;
                        		if(fbc.debug)
                                	console.log("Enable Field:", elem.id, elem);
                        	}
                        	
                        }
                        
                    }
                }

                this._enableField(elt);
                
                elt.disabled = false;
                
                if(fbc.debug)
                	console.log("showWrapper:", elt.id);
                
                break;
    	}

    },

    enableWrapper: function (event, wrapperId, animation) {

        var elt = this.getSrcElement(event);
        this._disableField(elt);


        // sortable
        i = 0;
        hit = false;
        a = document.getElementsByTagName("span");
        while (element = a[i++]) {
        	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                hit = true;
                var children = element.childNodes;
                for (j = 0; j < children.length; j++) {
                    if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                        children[j].disabled = false;
                    }
                }
            }
        }

        // free form, table mode
        if (!hit) {
            i = 0;
            a = document.getElementsByTagName("div");
            while (element = a[i++]) {
            	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                    hit = true;
                    
                    var children = element.childNodes;
                    
                    for (j = 0; j < children.length; j++) {
                    	
                    	if(fbc.debugSelectorLogic)
                    		console.log("Children:", children[j].id);
                        
                    	if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                            children[j].disabled = false;
                        }
                        
                    	
                        // Table Mode
                        var grandchildren = children[j].childNodes;
                        
                        if(typeof(grandchildren) != "undefined") {
                        
	                        for (k = 0; k < grandchildren.length; k++) {
	                        	
	                        	if(fbc.debugSelectorLogic)
	                        		console.log(">>Grandchildren:", grandchildren[k].className);
	                        	
	                            if (this._disableTypes.indexOf(grandchildren[k].tagName) != -1) {
	                            	grandchildren[k].disabled = false;
	                            }
	                            
	                            
	                            var greatgranchildren = grandchildren[k].childNodes;
	                            
	                            if(typeof(greatgranchildren) != "undefined") {
	                            	
	                            	for (s = 0; s < greatgranchildren.length; s++) {
	                                	
	                            		if(fbc.debugSelectorLogic)
	                            			console.log(">>>Greatgranchildren:", grandchildren[j].className);
	                                	
	                                    if (this._disableTypes.indexOf(greatgranchildren[s].tagName) != -1) {
	                                    	greatgranchildren[s].disabled = false;
	                                    }
	                                }
	                            	
	                            }
	                            
	                        }

                        } // Table Mode
                        
                        
                        
                    }
                }
            }
        }

        this._enableField(elt);

    },

    disableWrapper: function (event, wrapperId, animation) {

        var elt = this.getSrcElement(event);
        this._disableField(elt);


        // sortable
        i = 0;
        hit = false;
        a = document.getElementsByTagName("span");
        while (element = a[i++]) {
        	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                hit = true;
                var children = element.childNodes;
                for (j = 0; j < children.length; j++) {
                    if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                        children[j].disabled = true;
                    }
                }
            }
        }

        // free form
        if (!hit) {
            i = 0;
            a = document.getElementsByTagName("div");
            while (element = a[i++]) {
            	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                    hit = true;
                    
                    var children = element.childNodes;
                    
                    for (j = 0; j < children.length; j++) {
                        
                    	if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                            children[j].disabled = true;
                        }
                        
                        // Table Mode
                        var grandchildren = children[j].childNodes;
                        
                        if(typeof(grandchildren) != "undefined") {
                        
	                        for (k = 0; k < grandchildren.length; k++) {
	                        	
	                        	if(fbc.debugSelectorLogic)
	                        		console.log(">>Grandchildren:", grandchildren[k].className);
	                        	
	                            if (this._disableTypes.indexOf(grandchildren[k].tagName) != -1) {
	                            	grandchildren[k].disabled = true;
	                            }
	                            
	                            
	                            var greatgranchildren = grandchildren[k].childNodes;
	                            
	                            if(typeof(greatgranchildren) != "undefined") {
	                            	
	                            	for (s = 0; s < greatgranchildren.length; s++) {
	                                	
	                            		if(fbc.debugSelectorLogic)
	                            			console.log(">>>Greatgranchildren:", grandchildren[j].className);
	                                	
	                                    if (this._disableTypes.indexOf(greatgranchildren[s].tagName) != -1) {
	                                    	greatgranchildren[s].disabled = true;
	                                    }
	                                }
	                            	
	                            }
	                            
	                        }

                        } // Table Mode
                        
                    }
                }
            }
        }

        this._enableField(elt);

    },

    handleEvent: function (event, value, condition, conditionValue, action, field, wrapper, toggle, animation) {

        if (!document.getElementById('fb_fld-' + field) && wrapper == '')
            return;

        pass = false;
        override = false;

        if (condition !== '') {
            // test condition
            switch (condition) {
                case 'clicked':
                    pass = true;
                    break;
                case '!=':
                    if (value != conditionValue) { pass = true; }
                    break;
                case '==':
                    if (value == conditionValue) { pass = true; }
                    break;
                case '<':
                    if (parseInt(value)) {
                        if (parseInt(value) < parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) < parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '>':
                    if (parseInt(value)) {
                        if (parseInt(value) > parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) > parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '<=':
                    if (parseInt(value)) {
                        if (parseInt(value) <= parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) <= parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '>=':
                    if (parseInt(value)) {
                        if (parseInt(value) >= parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) >= parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case 'checked':
                    var targ;
                    if (!event) var event = window.event;
                    if (event.target) targ = event.target;
                    else if (event.srcElement) targ = event.srcElement;
                    if (targ.nodeType == 3) // defeat Safari bug
                        targ = targ.parentNode;
                    if (targ.checked) { pass = true; }
                    break;
                case 'unchecked':
                    var targ;
                    if (!event) var event = window.event;
                    if (event.target) targ = event.target;
                    else if (e.srcElement) targ = e.srcElement;
                    if (targ.nodeType == 3) // defeat Safari bug
                        targ = targ.parentNode;
                    if (!targ.checked) { pass = true; }
                    break;
                case 'notblank':
                	if (value != '') { pass = true; }
                    break;
                case 'blank':
                	if (value == '') { pass = true; }
                    break;
            }
        } else {
            // no condition, the event itself is the trigger to perform the action
            pass = true;
        }

        // Handle Multi-Items (checkbox, radio)
        var elt = this.getSrcElement(event);

        if (document.getElementById(elt.id).type == 'checkbox' || document.getElementById(elt.id).type == 'radio') {

            if (condition !== '') {
                // test condition
                switch (condition) {
                    case 'checked':
                        if (document.getElementById(elt.id).checked) {
                            pass = true;
                        }
                        break;
                    case 'unchecked':
                        if (!document.getElementById(elt.id).checked) {
                            pass = true;
                        }
                        break;
                    case '==': 
                    	if (value == conditionValue && document.getElementById(elt.id).checked) { pass = true; }
                    	if (value == conditionValue && !document.getElementById(elt.id).checked) { pass = false; }
                    	break;
                    case '!=': 
                    	if (value != conditionValue && document.getElementById(elt.id).checked) { pass = true; }
                    	if (value != conditionValue && !document.getElementById(elt.id).checked) { pass = false; }
                    	break;
                }
            }

        }

        // If true, and we failed the condition test, perform the opposite action, such as hide instead of show, then set pass to true
        // We prevent the action if it would be redundant (e.g., already hidden, do not hide again)
        if (toggle) {

            // 'Clicked' items have different toggle requirements because we never check a value, only a clicked action.
            // In other words, do the opposite of what is, not what's set or defined.
            if (condition == 'clicked') {

                action = '';

                currentMode = document.getElementById('fb_fld-' + field).style.display;

                // Buckets won't have a disabled state.
                currentDisabled = false;

                if (document.getElementById(field) != null)
                    currentDisabled = document.getElementById(field).disabled;

                // if the field is shown, it hides, if it's disabled, it's enabled.

                if (currentMode == 'none') {
                    action = 'show';
                } else {
                    action = 'hide';
                }

                // If we don't have an action set yet, check or disabled behavior.
                if (action == '') {
                    if (currentDisabled) {
                        action = 'enable';
                    } else {
                        action = 'disable';
                    }
                }

            } else {

                if (pass == false) {
                    switch (action) {
                        case 'hide':
                            action = 'show';
                            break;
                        case 'show':
                            action = 'hide';
                            break;
                        case 'enable':
                            action = 'disable';
                            break;
                        case 'disable':
                            action = 'enable';
                            break;
                    }
                }

            }

            if (wrapper === '') {
            	
            	// Build 861
            	if(field == "")
            		return;

                currentMode = document.getElementById('fb_fld-' + field).style.display;

                // Buckets won't have a disabled state.
                currentDisabled = false;

                if (document.getElementById(field) != null)
                    currentDisabled = document.getElementById(field).disabled;

                if (action == 'hide' && currentMode == 'none') {
                    return;
                }

                if (action == 'show' && currentMode == 'block' || action == 'show' && currentMode == '') {
                    return;
                }

                if (action == 'disable' && currentDisabled == true) {
                    return;
                }

                if (action == 'enable' && currentDisabled == false) {
                    return;
                }

            }

            if (wrapper !== '') {

                var currentMode = null;
                var currentDisabled = false;

                // sortable
                i = 0;
                a = document.getElementsByTagName("span");
                while (element = a[i++]) {
                	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapper + " ") != -1) {
                        currentMode = element.style.display;
                        // check disabled items
                        var children = element.childNodes;
                        for (j = 0; j < children.length; j++) {
                            if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                                currentDisabled = children[j].disabled;
                            }
                        }
                        break;
                    }
                }

                // free form
                if (currentMode == null) {
                    i = 0;
                    a = document.getElementsByTagName("div");
                    while (element = a[i++]) {
                    	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapper + " ") != -1) {
                            currentMode = element.style.display;
                            // check disabled items
                            var children = element.childNodes;
                            for (j = 0; j < children.length; j++) {
                                if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                                    currentDisabled = children[j].disabled;
                                }
                            }
                            break;
                        }
                    }
                }
                
                
                if(fbc.debug)
                	console.log("handleEvent() - currentMode", currentMode, "action", action);

                
                if (action == 'hide' && currentMode == 'none') {
                    return;
                }

                if (action == 'show' && currentMode == 'block' || action == 'show' && currentMode == '') {
                    return;
                }

                if (action == 'disable' && currentDisabled == true) {
                    return;
                }

                if (action == 'enable' && currentDisabled == false) {
                    return;
                }

            }

            pass = true;
        }

        if (pass) {

            switch (action) {
                case 'hide':
                    if (wrapper !== '') {
                        this.hideWrapper(event, wrapper, animation);
                        this.disableWrapper(event, wrapper, animation);
                    } else {
                        this.hideField(event, field, animation);
                        this.disableField(event, field, animation);
                    }
                    break;
                case 'show':
                    if (wrapper !== '') {
                        this.showWrapper(event, wrapper, animation);
                        this.enableWrapper(event, wrapper, animation);
                    } else {
                        this.showField(event, field, animation);
                        this.enableField(event, field, animation);
                    }
                    break;
                case 'enable':
                    if (wrapper !== '') {
                        this.enableWrapper(event, wrapper, animation);
                    } else {
                        this.enableField(event, field, animation);
                    }
                    break;
                case 'disable':
                    if (wrapper !== '') {
                        this.disableWrapper(event, wrapper, animation);
                    } else {
                        this.disableField(event, field, animation);
                    }
                    break;

            } // action switch

        } // if pass

    },



    /* on load - Build 710 - passing id is now optional */

    hideFieldLoad: function (id, fieldId, animation) {
    	
    	if(fbc.debug)
    		console.log("Hiding Field On Load", id, fieldId);

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        document.getElementById('fb_fld-' + fieldId).style.display = 'none';

        if (id != '') {
            this._enableField(elt);
        }
        
        if (document.getElementById(fieldId) != null) {
        	document.getElementById(fieldId).disabled = true;
    	}
        
        // radio, checkbox
		for(i = 0; i < 120; i++){
			if (document.getElementById(fieldId + i) != null) {
	        	document.getElementById(fieldId + i).disabled = true;
	        	
	        	if(fbc.debug)
	        		console.log("Disabling Field Load:", fieldId + i);
			}
		}
		
		// calendar
		if (document.getElementById('date-' + fieldId + '-1') != null) {
        	document.getElementById('date-' + fieldId + '-1').disabled = true;
        	
        	if(fbc.debug)
        		console.log("Disable Calendar Field Load:", 'date-' + fieldId + '-1');
		}
		
		if (document.getElementById('date-' + fieldId + '-2') != null) {
        	document.getElementById('date-' + fieldId + '-2').disabled = true;
        	
        	if(fbc.debug)
        		console.log("Disable Calendar Field Load:", 'date-' + fieldId + '-2');
		}
		
		if (document.getElementById('date-' + fieldId + '-3') != null) {
        	document.getElementById('date-' + fieldId + '-3').disabled = true;
        	
        	if(fbc.debug)
        		console.log("Disable Calendar Field Load:", 'date-' + fieldId + '-3');
		}

    },

    showFieldLoad: function (id, fieldId, animation) {
    	
    	if(fbc.debug)
    		console.log("Showing Field On Load",id, fieldId);

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        document.getElementById('fb_fld-' + fieldId).style.display = 'block';

        if (id != '') {
            this._enableField(elt);
        }
        
        if (document.getElementById(fieldId) != null) {
        	document.getElementById(fieldId).disabled = false;
    	}
        
        // radio, checkbox
		for(i = 0; i < 120; i++){
			if (document.getElementById(fieldId + i) != null) {
	        	document.getElementById(fieldId + i).disabled = false;
	        	
	        	if(fbc.debug)
	        		console.log("Showing Field Load:", fieldId + i);
			}
		}
		
		// calendar
		if (document.getElementById('date-' + fieldId + '-1') != null) {
        	document.getElementById('date-' + fieldId + '-1').disabled = false;
        	
        	if(fbc.debug)
        		console.log("Showing Calendar Field Load:", 'date-' + fieldId + '-1');
		}
		
		if (document.getElementById('date-' + fieldId + '-2') != null) {
        	document.getElementById('date-' + fieldId + '-2').disabled = false;
        	
        	if(fbc.debug)
        		console.log("Showing Calendar Field Load:", 'date-' + fieldId + '-2');
		}
		
		if (document.getElementById('date-' + fieldId + '-3') != null) {
        	document.getElementById('date-' + fieldId + '-3').disabled = false;
        	
        	if(fbc.debug)
        		console.log("Showing Calendar Field Load:", 'date-' + fieldId + '-3');
		}

    },

    disableFieldLoad: function (id, fieldId, animation) {

        if (!document.getElementById(fieldId))
            return;

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        document.getElementById(fieldId).disabled = true;
        this._enableField(elt);

    },

    enableFieldLoad: function (id, fieldId, animation) {

        if (!document.getElementById(fieldId))
            return;

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        document.getElementById(fieldId).disabled = false;
        this._enableField(elt);

    },

    hideWrapperLoad: function (id, wrapperId, animation) {

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        // sortable
        i = 0;
        hit = false;
        a = document.getElementsByTagName("span");
        while (element = a[i++]) {
        	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                hit = true;
                element.style.display = 'none';
            }
        }

        // free form
        if (!hit) {
            i = 0;
            a = document.getElementsByTagName("div");
            while (element = a[i++]) {
            	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                    hit = true;
                    element.style.display = 'none';
                    
                    // disable form element
                    if(element.querySelector){
                    	var elem = element.querySelector('input, select, textarea');
                    	if(elem !== null){
                    		elem.disabled = true;
                    		if(fbc.debug)
                            	console.log("Disable Field Load:", elem.id, elem);
                    	}
                    	
                    }
                    
                }
            }
        }

        if (id != '') {
            this._enableField(elt);
        }

    },

    showWrapperLoad: function (id, wrapperId, animation) {

        if (id != '') {
            elt = document.getElementById(id);
            this._disableField(elt);
        }

        i = 0;
        hit = false;
        a = document.getElementsByTagName("span");
        while (element = a[i++]) {
        	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                hit = true;
                element.style.display = 'block';
            }
        }

        // free form
        if (!hit) {
            i = 0;
            a = document.getElementsByTagName("div");
            while (element = a[i++]) {
            	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapperId + " ") != -1) {
                    hit = true;
                    element.style.display = 'block';
                    
                    // enable form element
                    if(element.querySelector){
                    	var elem = element.querySelector('input, select, textarea');
                    	if(elem !== null){
                    		elem.disabled = false;
                    		if(fbc.debug)
                            	console.log("Enable Field Load:", elem.id);
                    	}
                    	
                    }
                    
                }
            }
        }

        if (id != '') {
            this._enableField(elt);
        }

    },


    handleEventLoad: function (id, condition, conditionValue, action, field, wrapper, toggle, animation, iterated, samepage_value) {

        if (typeof(samepage_value) == "undefined" || samepage_value == '') {

            if (!document.getElementById(id)) // sample_value can be set no a blank value as in a select item
                return;

            value = document.getElementById(id).value;

        } else {

            value = samepage_value;

        }
        
        if(fbc.debug)
    		console.log("Called handleEventLoad() ",id, "Condition: ", condition, " Wrapper: ", wrapper);

        pass = false;
        override = false;

        if (condition !== '') {
            // test condition
            switch (condition) {
                case '!=':
                    if (value != conditionValue) { pass = true; }
                    break;
                case '==':
                    if (value == conditionValue) { pass = true; }
                    break;
                case '<':
                    if (parseInt(value)) {
                        if (parseInt(value) < parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) < parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '>':
                    if (parseInt(value)) {
                        if (parseInt(value) > parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) > parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '<=':
                    if (parseInt(value)) {
                        if (parseInt(value) <= parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) <= parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case '>=':
                    if (parseInt(value)) {
                        if (parseInt(value) >= parseInt(conditionValue)) { pass = true; }
                    } else if (parseFloat(value)) {
                        if (parseFloat(value) >= parseFloat(conditionValue)) { pass = true; }
                    } else {
                        pass = false;
                    }
                    break;
                case 'checked':
                    if (document.getElementById(id).checked) {
                        pass = true;
                    }
                    break;
                case 'unchecked':
                    if (!document.getElementById(id).checked) {
                        pass = true;
                    }
                case 'notblank':
                	if (value != '') { pass = true; }
                    break;
                case 'blank':
                	if (value == '') { pass = true; }
                    break;
            }
        } else {
            // no condition, the event itself is the trigger to perform the action
            pass = true;
        }
        
        if(fbc.debug)
    		console.log("handleEventLoad() Pass Value: ", pass);

        if (iterated) {

            // Handle Multi-Items (checkbox, radio)
            var elt = document.getElementById(id);

            if (elt.type == 'checkbox' || elt.type == 'radio') {
            	
            	pass = false; // reset for this check.

                if (condition !== '') {
                    
                	// test condition
                    switch (condition) {
                    
                    case '!=':
                        if (value != conditionValue && elt.checked) { pass = true; }
                        break;
                        
                    case '==':
                        if (value == conditionValue && elt.checked) { pass = true; }
                        break;
                        
                    case 'checked':
                        if (elt.checked) {
                            pass = true;
                        }
                        break;
                        
                    case 'unchecked':
                        if (!elt.checked) {
                            pass = true;
                        }
                        break;
                    }
                }

            }


        }

        // If true, and we failed the condition test, perform the opposite action, such as hide instead of show, then set pass to true
        // We prevent the action if it would be redundant (e.g., already hidden, do not hide again)
        if (toggle) {
            if (pass == false) {
                switch (action) {
                    case 'hide':
                        action = 'show';
                        break;
                    case 'show':
                        action = 'hide';
                        break;
                    case 'enable':
                        action = 'disable';
                        break;
                    case 'disable':
                        action = 'enable';
                        break;
                }
            }

            if (wrapper === '') {
            	
            	// Build 861
            	if(field == "")
            		return;

                currentMode = document.getElementById('fb_fld-' + field).style.display;

                // Buckets won't have a disabled state.
                currentDisabled = false;

                if (document.getElementById(field) != null)
                    currentDisabled = document.getElementById(field).disabled;

                if (action == 'hide' && currentMode == 'none') {                	
                    return false;
                }

                if (action == 'show' && currentMode == 'block' || action == 'show' && currentMode == '') {
                    return false;
                }

                if (action == 'disable' && currentDisabled == true) {
                    return;
                }

                if (action == 'enable' && currentDisabled == false) {
                    return;
                }

            }

            if (wrapper !== '') {

                var currentMode = null;
                var currentDisabled = false;

                // sortable
                i = 0;
                a = document.getElementsByTagName("div");
                while (element = a[i++]) {
                	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapper + " ") != -1) {
                        currentMode = element.style.display;
                        // check disabled items
                        var children = element.childNodes;
                        for (j = 0; j < children.length; j++) {
                            if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                                currentDisabled = children[j].disabled;
                            }
                        }
                        break;
                    }
                }

                // free form
                if (currentMode == null) {
                    i = 0;
                    a = document.getElementsByTagName("div");
                    while (element = a[i++]) {
                    	if (element.hasAttribute("rf-field") && element.className.indexOf(" " + wrapper + " ") != -1) {
                            currentMode = element.style.display;
                            // check disabled items
                            var children = element.childNodes;
                            for (j = 0; j < children.length; j++) {
                                if (this._disableTypes.indexOf(children[j].tagName) != -1) {
                                    currentDisabled = children[j].disabled;
                                }
                            }
                            break;
                        }
                    }
                }

                if (action == 'hide' && currentMode == 'none') {
                    return false;
                }

                if (action == 'show' && currentMode == 'block' || action == 'show' && currentMode == '') {
                    return false;
                }

                if (action == 'disable' && currentDisabled == true) {
                    return;
                }

                if (action == 'enable' && currentDisabled == false) {
                    return;
                }

            }

            pass = true;
        }

        if (pass) {

            switch (action) {
                case 'hide':
                    if (wrapper !== '') {
                        this.hideWrapperLoad(id, wrapper, animation);
                    } else {
                        this.hideFieldLoad(id, field, animation);
                        this.disableFieldLoad(id, field, animation);
                    }
                    break;
                case 'show':
                    if (wrapper !== '') {
                        this.showWrapperLoad(id, wrapper, animation);
                    } else {
                        this.showFieldLoad(id, field, animation);
                        this.enableFieldLoad(id, field, animation);
                    }
                    break;
                case 'enable':
                    if (wrapper !== '') {
                        this.enableWrapperLoad(id, wrapper, animation);
                    } else {
                        this.enableFieldLoad(id, field, animation);
                    }
                    break;
                case 'disable':
                    if (wrapper !== '') {
                        this.disableWrapperLoad(id, wrapper, animation);
                    } else {
                        this.disableFieldLoad(id, field, animation);
                    }
                    break;

            } // action switch

        } // if pass

        return pass;
    }

};



