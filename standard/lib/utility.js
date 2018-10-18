// FormBoss JavaScript Utility File

/**
 * Scroll To Page Top
 * 
 * Build 747 - scrollType determins if we scroll in parent or just current page.
 * Build 835 - Added Page Size Code, DOM Loaded.
 * 
 * @param scrollType
 * @return
 */
function scrollFormToTop(scrollType) {

    switch (scrollType) {

        case 1 : // scroll to top of page

            // most browsers
            document.location = '#top';

            // Firefox 5+
            document.getElementById('top').scrollIntoView();

            break;

        case 2 : // scroll to top of parent page (such as in iFrames)

            window.parent.scrollTo(0, 0);
            break;

    }

};

/**
 * Get Parent Element By Class Name Given Child Element.
 * 
 * @since 912
 * @return DOMElement
 */
function getParentNodeByClassName(parentClassName, childObj) {

	var _debug = false;

	if(childObj == null){
		
		return null;
		
	}
	
    var testObj = childObj.parentNode;
    
    if(_debug)
    	console.log("Parent Object Found With Class Name: ", testObj.className);

    var count = 1;

    while(testObj.className.indexOf(parentClassName) == -1) {

    	if(testObj == null){
    		
    		return null;
    		
    	}
    		
    	
        testObj = testObj.parentNode;
        
        if(testObj.className === undefined){
        	
        	return null;
        	
        }
        
        if(_debug)
        	console.log("Inner Parent Object Found With Class Name: ", testObj.className);

        count++;

    }

    if(_debug)
    	console.log('Found: ' + testObj + ' after going up ' + count + ' level(s) through the DOM tree');
    
    return testObj;

}

/**
 * Calculation Functions
 */

var RF_Calculation = {
    RackForms_Calculation_Version: '731',
    /**
     * Format For US Currency
     * @since 729
     * @author nicSoft
     * 
     * @param num
     */
    format_Currency_US: function(num) {

        // pass in number, returns formatted string
        // Credit: http://javascript.internet.com/forms/currency-format.htm
        num = num.toString().replace(/\$|\,/g, '');
        if (isNaN(num))
            num = "0";
        sign = (num === (num = Math.abs(num)));
        num = Math.floor(num * 100 + 0.50000000001);
        cents = num % 100;
        num = Math.floor(num / 100).toString();
        if (cents < 10)
            cents = "0" + cents;
        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                    num.substring(num.length - (4 * i + 3));
        return (((sign) ? '' : '-') + '$' + num + '.' + cents);

    },
    format_Currency_GBP: function(sNumberInput) {
        // Remove any characters other than numbers and periods from the string, then parse the number
        var nNumberToFormat = parseFloat(String(sNumberInput).replace(/[^0-9\.]/g, ''));
        // Escape when this number is invalid (parseFloat returns NaN on failure, we can detect this with isNaN)
        if (isNaN(nNumberToFormat))
            return '�--';

        // Split number string by decimal separator (.)
        var aNumberParts = nNumberToFormat.toFixed(2).split('.');

        // Get first part = integer part
        var sFirstPart = aNumberParts[0];
        // Determine the position after which to start grouping
        var nGroupingStart = sFirstPart.length % 3;
        // Shift three to the right when first group is empty
        nGroupingStart += (nGroupingStart === 0) ? 3 : 0;
        // Start first result with ungrouped first part
        var sFirstResult = sFirstPart.substr(0, nGroupingStart);
        // Add grouped parts by looping through the remaining numbers
        for (var i = nGroupingStart, len = sFirstPart.length; i < len; i += 3) {
            sFirstResult += ',' + sFirstPart.substr(i, 3);
        }

        // Get second part = fractional part
        var sSecondResult = aNumberParts[1] ? '.' + aNumberParts[1] : '';

        // Combine the parts and return the result
        return '� ' + sFirstResult + sSecondResult;
    },
    format_Currency_Euro: function(number) {
        var numberStr = parseFloat(number).toFixed(2).toString();
        var numFormatDec = numberStr.slice(-2); /*decimal 00*/
        numberStr = numberStr.substring(0, numberStr.length - 3); /*cut last 3 strings*/
        var numFormat = new Array;
        while (numberStr.length > 3) {
            numFormat.unshift(numberStr.slice(-3));
            numberStr = numberStr.substring(0, numberStr.length - 3);
        }
        numFormat.unshift(numberStr);
        return numFormat.join('.') + ',' + numFormatDec; /*format 000.000.000,00 */
    },
    /**
     * Utility Functions
     */
    getSrcElement: function(event) {
        var elt;
        if (!event)
            var event = window.event;
        if (event.target)
            elt = event.target;
        else if (event.srcElement)
            elt = event.srcElement;
        if (elt.nodeType === 3) // defeat Safari bug
            elt = elt.parentNode;

        return elt;
    }


}; // end calculation


/**
 * Animate an object's opacity.
 * 
 * @since 779
 */
var RF_AnimateSimple = {
    fading_div: null,
    done: true,
    time: 20,
    init: function(elt, operation, time, maxopacity) {
        this.fading_div = document.getElementById(elt) ? document.getElementById(elt) : elt; // can be an id or object
        this.time = time;
        this.maxopacity = maxopacity; // 0 - 100
        operation === "in" ? this._fade_in() : this._fade_out();
    },
    function_opacity: function(opacity_value) {
        this.fading_div.style.opacity = opacity_value / 100;
        this.fading_div.style.filter = 'alpha(opacity=' + opacity_value + ')';
    },
    function_fade_out: function(opacity_value) {
        this.function_opacity(opacity_value);
        if (opacity_value === 1) {
            this.fading_div.style.display = 'none';
            this.done = true;
        }
    },
    function_fade_in: function(opacity_value) {
        this.function_opacity(opacity_value);
        if (opacity_value === 1) {
            this.fading_div.style.display = 'block';
        }
        if (opacity_value === this.maxopacity) {
            this.done = true;
        }
    },
    // fade in
    _fade_in: function() {
        if (this.done && this.fading_div.style.opacity !== '1') {
            this.done = false;
            for (var i = 1; i <= this.maxopacity; i++) {
                var that = this;
                setTimeout((function(x) {
                    return function() {
                        that.function_fade_in(x);
                    };
                })(i), i * 2);
            }
        }
    },
    // fade out
    _fade_out: function() {
        if (this.done && this.fading_div.style.opacity !== '0') {
            this.done = false;
            for (var i = 1; i <= this.maxopacity; i++) {
                var that = this;
                setTimeout((function(x) {
                    return function() {
                        that.function_fade_out(x);
                    };
                })(this.maxopacity - i), i * 2);
            }
        }
    }
}; // End RF_AnimateSimple

var RackForms_ToolTip_Event_Bound = false;

function RackForms_ToolTip() {

    this.ele = null;
    this.tip = null;

    this.options = {
        targetelement: "",
        targettooltip: "",
        placement: 'left-center',
        timeout: 500, // in milliseconds
        offset: [10, 0], // x,y
        activation: "over", // click, over
        animation: "", // none|blank, fade,
        a_time: 20,
        a_maxopacity: 1 * 100
    };

    this.init = function(options) {

        // init options
        this.options.targetelement = options[0];
        this.options.targettooltip = options[1];
        this.options.placement = options[2];
        this.options.timeout = options[3]; // in milliseconds
        this.options.offset = options[4]; // x,y
        this.options.activation = options[5]; // click, over
        this.options.animation = options[6]; // none|blank, fade,
        this.options.a_time = options[7];
        this.options.a_maxopacity = options[8] * 100;


        // init tooltip
        this.tooltip = document.getElementById(this.options.targetelement);

        switch (this.options.activation) {
            case "over" :
                this.tooltip.addEventListener('focus', this.showtip.bind(this));
                this.tooltip.addEventListener('blur', this.hidetip.bind(this));
                this.tooltip.addEventListener('mouseover', this.showtip.bind(this));
                this.tooltip.addEventListener('mouseout', this.hidetip.bind(this));
                break;
            case "click" :
                this.tooltip.addEventListener('click', this.toggletip.bind(this));
                if (!RackForms_ToolTip_Event_Bound) {
                    document.body.addEventListener('click', this.bodyClick.bind(this));
                    RackForms_ToolTip_Event_Bound = true;
                }


                break;
        }

        var tips = document.getElementsByClassName(this.options.targettooltip);
        this.tip = tips[0];

    };

    // click events
    this.toggletip = function(e) {

        var elt = e.target;

        this.ele = e.target; // used in positiontip()

        if (window.event) {
            window.event.cancelBubble = true;
        } else {
            e.stopPropagation();
        }

        if (this.tip.style.display === "block") {
            switch (this.options.animation) {
                case "fade" :
                    this.tip.style.opacity = this.options.a_maxopacity;
                    this.tip.style.display = "block";
                    RF_AnimateSimple.init(this.tip, "out", this.options.a_time, this.options.a_maxopacity);
                    break;
                default :
                    // hide tip with no animation
                    this.tip.style.display = "none";
            }
        } else {

            this.positiontip();

            switch (this.options.animation) {
                case "fade" :
                    this.tip.style.opacity = 0;
                    this.tip.style.display = "block";
                    RF_AnimateSimple.init(this.tip, "in", this.options.a_time, this.options.a_maxopacity);
                    break;
                default :
                    // show tip
                    this.tip.style.opacity = this.options.a_maxopacity;
                    this.tip.style.display = "block";
            }
        }

    };

    // hide all tips when we bind click events and then click on the document body
    this.bodyClick = function(e) {

        // body click, hide all with no fade
        var tips = document.getElementsByClassName("fbtooltip");
        for (i = 0; i < tips.length; i++) {
            tips[i].style.display = "none";
        }

    };

    // rollovers
    this.showtip = function(e) {

        this.ele = e.target;

        this.positiontip();

        switch (this.options.animation) {
            case "fade" :
                this.tip.style.opacity = 0;
                this.tip.style.display = "block";
                RF_AnimateSimple.init(this.tip, "in", this.options.a_time, this.options.a_maxopacity);
                break;
            default :
                // show tip
                this.tip.style.opacity = this.options.a_maxopacity;
                this.tip.style.display = "block";
        }

    };

    this.hidetip = function() {

        var that = this;

        if (this.options.timeout !== 0) {
            setTimeout(function() {
                that.delayedhide();
            }, this.options.timeout);
        } else {
            switch (this.options.animation) {
                case "fade" :
                    this.tip.style.opacity = this.options.a_maxopacity;
                    this.tip.style.display = "block";
                    RF_AnimateSimple.init(this.tip, "out", this.options.a_time, this.options.a_maxopacity);
                    break;
                default :
                    // hide tip
                    this.tip.style.opacity = 0;
                    this.tip.style.display = "none";
            }
        }
    };

    this.delayedhide = function() {
        switch (this.options.animation) {
            case "fade" :
                this.tip.style.opacity = this.options.a_maxopacity;
                this.tip.style.display = "block";
                RF_AnimateSimple.init(this.tip, "out", this.options.a_time, this.options.a_maxopacity);
                break;
            default :
                // hide tip
                this.tip.style.display = "none";
        }
    };

    this.positiontip = function() {

        placement = this.ele.dataset.placement || this.options.placement;

        // display:none prevents offset from being measured, so we toggle state.
        this.tip.style.opacity = 0;
        this.tip.style.display = "block";

        actualWidth = this.tip.offsetWidth;
        actualHeight = this.tip.offsetHeight;

        this.tip.style.opacity = this.options.a_maxopacity;
        this.tip.style.display = "none";

        pos = {
            width: this.ele.offsetWidth,
            height: this.ele.offsetHeight,
            top: this.ele.offsetTop,
            left: this.ele.offsetLeft
        };

        switch (placement) {
            case 'bottom':
                this.tip.style.top = (pos.top + pos.height) + this.options.offset[1] + 'px';
                this.tip.style.left = this.options.offset[0] + 'px';
                break;
            case 'bottom-center':
                this.tip.style.top = (pos.top + pos.height) + this.options.offset[1] + 'px';
                this.tip.style.left = pos.left + pos.width / 2 - actualWidth / 2 + this.options.offset[0] + 'px';
                break;
            case 'top':
                this.tip.style.top = (pos.top - actualHeight) - this.options.offset[1] + 'px';
                this.tip.style.left = this.options.offset[0] + 'px';
                break;
            case 'top-center':
                this.tip.style.top = (pos.top - actualHeight) - this.options.offset[1] + 'px';
                this.tip.style.left = pos.left + pos.width / 2 - actualWidth / 2 + this.options.offset[0] + 'px';
                break;
            case 'left':
                this.tip.style.top = this.options.offset[1] + 'px';
                this.tip.style.left = (pos.left - actualWidth) - this.options.offset[0] + 'px';
                break;
            case 'left-center':
                this.tip.style.top = (pos.top + pos.height / 2 - actualHeight / 2) + this.options.offset[1] + 'px';
                this.tip.style.left = (pos.left - actualWidth) + this.options.offset[0] + 'px';
                break;
            case 'right':
                this.tip.style.top = this.options.offset[1] + 'px';
                this.tip.style.left = (pos.left + pos.width) + this.options.offset[0] + 'px';
                break;
            case 'right-center':
                this.tip.style.top = (pos.top + pos.height / 2 - actualHeight / 2) + this.options.offset[1] + 'px';
                this.tip.style.left = (pos.left + pos.width) + this.options.offset[0] + 'px';
                break;
        }
    };

}
;


function walkTheDOM(node, func) {
    func(node);
    node = node.firstChild;
    while (node) {
        walkTheDOM(node, func);
        node = node.nextSibling;
    }
}

function getElementsByClassName(className) {
    var results = [];
    walkTheDOM(document.body, function(node) {
        var a, c = node.className,
                i;
        if (c) {
            a = c.split(' ');
            for (i = 0; i < a.length; i++) {
                if (a[i] === className) {
                    results.push(node);
                    break;
                }
            }
        }
    });
    return results;
}

/**
 * Get a parameter by name.
 * Credit: http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
 * 
 * // query string: ?foo=lorem&bar=&baz
 * Usage
 * var foo = getParameterByName('foo'); // "lorem"
 * var bar = getParameterByName('bar'); // "" (present with empty value)
 * var baz = getParameterByName('baz'); // === null
 * 
 * @since 891
 * @param name
 * @param url
 * @returns
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


//http://code.google.com/p/domready/
//This way we do not need the entire jQuery libraries.
(function(){

 var DomReady = window.DomReady = {};

	// Everything that has to do with properly supporting our document ready event. Brought over from the most awesome jQuery. 

 var userAgent = navigator.userAgent.toLowerCase();

 // Figure out what browser is being used
 var browser = {
 	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [])[1],
 	safari: /webkit/.test(userAgent),
 	opera: /opera/.test(userAgent),
 	msie: (/msie/.test(userAgent)) && (!/opera/.test( userAgent )),
 	mozilla: (/mozilla/.test(userAgent)) && (!/(compatible|webkit)/.test(userAgent))
 };    

	var readyBound = false;	
	var isReady = false;
	var readyList = [];

	// Handle when the DOM is ready
	function domReady() {
		// Make sure that the DOM is not already loaded
		if(!isReady) {
			// Remember that the DOM is ready
			isReady = true;
     
	        if(readyList) {
	            for(var fn = 0; fn < readyList.length; fn++) {
	                readyList[fn].call(window, []);
	            }
         
	            readyList = [];
	        }
		}
	};

	// From Simon Willison. A safe way to fire onload w/o screwing up everyone else.
	function addLoadEvent(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != 'function') {
	    window.onload = func;
	  } else {
	    window.onload = function() {
	      if (oldonload) {
	        oldonload();
	      }
	      func();
	    }
	  }
	};

	// does the heavy work of working through the browsers idiosyncracies (let's call them that) to hook onload.
	function bindReady() {
		if(readyBound) {
		    return;
	    }
	
		readyBound = true;

		// Mozilla, Opera (see further below for it) and webkit nightlies currently support this event
		if (document.addEventListener && !browser.opera) {
			// Use the handy event callback
			document.addEventListener("DOMContentLoaded", domReady, false);
		}

		// If IE is used and is not in a frame
		// Continually check to see if the document is ready
		if (browser.msie && window == top) (function(){
			if (isReady) return;
			try {
				// If IE is used, use the trick by Diego Perini
				// http://javascript.nwbox.com/IEContentLoaded/
				document.documentElement.doScroll("left");
			} catch(error) {
				setTimeout(arguments.callee, 0);
				return;
			}
			// and execute any waiting functions
		    domReady();
		})();

		if(browser.opera) {
			document.addEventListener( "DOMContentLoaded", function () {
				if (isReady) return;
				for (var i = 0; i < document.styleSheets.length; i++)
					if (document.styleSheets[i].disabled) {
						setTimeout( arguments.callee, 0 );
						return;
					}
				// and execute any waiting functions
	            domReady();
			}, false);
		}

		if(browser.safari) {
		    var numStyles;
			(function(){
				if (isReady) return;
				if (document.readyState != "loaded" && document.readyState != "complete") {
					setTimeout( arguments.callee, 0 );
					return;
				}
				if (numStyles === undefined) {
	                var links = document.getElementsByTagName("link");
	                for (var i=0; i < links.length; i++) {
	                	if(links[i].getAttribute('rel') == 'stylesheet') {
	                	    numStyles++;
	                	}
	                }
	                var styles = document.getElementsByTagName("style");
	                numStyles += styles.length;
				}
				if (document.styleSheets.length != numStyles) {
					setTimeout( arguments.callee, 0 );
					return;
				}
			
				// and execute any waiting functions
				domReady();
			})();
		}

		// A fallback to window.onload, that will always work
	    addLoadEvent(domReady);
	};

	// This is the public function that people can use to hook up ready.
	DomReady.ready = function(fn, args) {
		// Attach the listeners
		bindReady();
 
		// If the DOM is already ready
		if (isReady) {
			// Execute the function immediately
			fn.call(window, []);
	    } else {
			// Add the function to the wait list
	        readyList.push( function() { return fn.call(window, []); } );
	    }
	};
 
	bindReady();
	
})();

// get document height / width
function getDocHeight(D) {
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function getDocWidth(D) {
    return Math.max(
    	D.documentElement["clientWidth"], D.body["scrollWidth"], D.documentElement["scrollWidth"], D.body["offsetWidth"], D.documentElement["offsetWidth"]);
}

function getRFOutputDIVHeight(D) {
	  
	D = D.getElementsByClassName('rackforms-output-div')[0];
	
	if(typeof(D) == "undefined") {
		return 0;
	}
	  
	return Math.max(
	    Math.max(D.scrollHeight, D.scrollHeight),
	    Math.max(D.offsetHeight, D.offsetHeight),
	    Math.max(D.clientHeight, D.clientHeight)
	);
}

function getRFOutputDIVWidth(D) {
	  
	D = D.getElementsByClassName('rackforms-output-div')[0];
	  
	return Math.max(
		    Math.max(D.scrollWidth, D.scrollWidth),
		    Math.max(D.offsetWidth, D.offsetWidth),
		    Math.max(D.clientWidth, D.clientWidth)
		);
}

// get window size
function getWinSize(){
	var iWidth = 0, iHeight = 0;
	
	if (document.getElementById){
		iWidth = window.innerWidth;
		iHeight = window.innerHeight;
	} else if (document.all){
		iWidth = document.body.offsetWidth;
 		iHeight = document.body.offsetHeight;
	}
	
	return {width:iWidth, height:iHeight};
};


/*
Developed by Robert Nyman, http://www.robertnyman.com
Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/
var getElementsByClassName = function (className, tag, elm){
if (document.getElementsByClassName) {
	getElementsByClassName = function (className, tag, elm) {
		elm = elm || document;
		var elements = elm.getElementsByClassName(className),
			nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
			returnElements = [],
			current;
		for(var i=0, il=elements.length; i<il; i+=1){
			current = elements[i];
			if(!nodeName || nodeName.test(current.nodeName)) {
				returnElements.push(current);
			}
		}
		return returnElements;
	};
}
else if (document.evaluate) {
	getElementsByClassName = function (className, tag, elm) {
		tag = tag || "*";
		elm = elm || document;
		var classes = className.split(" "),
			classesToCheck = "",
			xhtmlNamespace = "http://www.w3.org/1999/xhtml",
			namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
			returnElements = [],
			elements,
			node;
		for(var j=0, jl=classes.length; j<jl; j+=1){
			classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
		}
		try	{
			elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
		}
		catch (e) {
			elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
		}
		while ((node = elements.iterateNext())) {
			returnElements.push(node);
		}
		return returnElements;
	};
}
else {
	getElementsByClassName = function (className, tag, elm) {
		tag = tag || "*";
		elm = elm || document;
		var classes = className.split(" "),
			classesToCheck = [],
			elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
			current,
			returnElements = [],
			match;
		for(var k=0, kl=classes.length; k<kl; k+=1){
			classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
		}
		for(var l=0, ll=elements.length; l<ll; l+=1){
			current = elements[l];
			match = false;
			for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
				match = classesToCheck[m].test(current.className);
				if (!match) {
					break;
				}
			}
			if (match) {
				returnElements.push(current);
			}
		}
		return returnElements;
	};
}
return getElementsByClassName(className, tag, elm);
};