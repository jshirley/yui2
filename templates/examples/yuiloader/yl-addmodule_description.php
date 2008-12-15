<h2 class="first">Using <code>addModule</code> to Add Custom Modules via YUILoader</h2>

<p>The purpose of this example is to illustrate one mechanism for adding custom (non-YUI) content to the page using the <a href="http://developer.yahoo.com/yui/yuiloader/">YUILoader Utility</a>.  For full documentation on this feature of the YUILoader, please refer to the <a href="http://developer.yahoo.com/yui/yuiloader/#addmodule">"Using <code>addModule</code>" section of the YUILoader Utility User's Guide</a>.</p>

<h3>Identifying an External Module to Load: The JSON Utility</h3>

<p>Douglas Crockford, the inventor of <a href="http://json.org">JSON</a>, has written a <a href="http://www.json.org/json.js">JSON Utility</a> that helps you verify that information you've brought into the page as JSON is indeed limited to constructs that fit the JSON specification; this utility helps prevent some forms of malicious attacks embedded in JSON data from being successful in damaging you application or compromising its security.</p>

<p>In this example, we'll use YUILoader's <code>addModule</code> function to load the JSON Utility from <code>http://www.json.org/json.js</code>.</p>

<h3>Invoke <code>addModule</code> to Make YUILoader Aware of the JSON Utility</h3>

<pThis is a simple step in which we invoke <code>addModule</code> and pass in relevant metadata about our new module:</p>

<textarea name="code" class="JScript" cols="60" rows="1">//Add the module to YUILoader
loader.addModule({
	name: "json", //module name; must be unique
	type: "js", //can be "js" or "css"
    fullpath: "http://www.json.org/json2.js", //can use a path instead, extending base path
    varName: "JSON" // a variable that will be available when the script is loaded.  Needed
                    // in order to act on the script immediately in Safari 2.x and below.
	//requires: ['yahoo', 'event'] //if this module had dependencies, we could define here
});</textarea>

<p>Note that in this case we're not setting up a dependency relationship between the JSON Utility and any other YUI components.  However, in the commented-out last line above, we could use the <code>requires</code> member of the configuration object to make the JSON Utility depend on other YUI components or other custom components that we've defined.</p>

<h3>Full Source for This Example</h3>

<p>The <code>addModule</code> step is the most important elements of this example with respect to adding non-YUI content to the page via YUILoader.  The full source of the example, including the use of the YUI Button Control to actuate the loading of the JSON Utility, follows here.</p>

<p>The varName property is required for external scripts that need to run in Safari 2.x or lower.
   This is the name a variable that the downloaded script will contain.  The onSuccess handler
   will only be executed once this property is detected.</p>

<textarea name="code" class="JScript" cols="60" rows="1"><!--Container for our call-to-action button-->
<div id="jsonInsertButtonContainer"></div>
 
<!--Container to which we'll append success message-->
<div id="jsonContainer"></div>

<script type="text/javascript">
//create our Button Instance which will trigger Loader to
//load the JSON utility from json.org.
YAHOO.example.buttoninit = function() {

	//Create the button instance:
	var oButton = new YAHOO.widget.Button({ 
		id: "jsonInsertButton",  
		type: "button",  
		label: "Click here to load JSON Utility",  
		container: "jsonInsertButtonContainer"  
	});
	YAHOO.log("Button created; click button to begin loading JSON.", "info", "example");

	//Create a handler that handles the button click;
	//it logs the click, hides the button, then fires 
	//the function (loaderinit) that brings in JSON:
	var onButtonClick = function() {
		YAHOO.log("Button clicked; hiding button now and loading JSON", "info", "example");
		YAHOO.util.Dom.setStyle("jsonInsertButtonContainer", "display", "none");
		YAHOO.example.loaderinit();
	}
	
	//attach the handler to the Button's click event:
	oButton.on("click", onButtonClick);
};

//Once the jsonInsertButtonContainer element is on the page, we can create
//our button instance; in this case, the onContentReady deferral is unnecessary,
//because we're writing the element to the page before this script,
//but in many cases the onContentReady wrapper gives you added
//flexibility and it comes at low expense:
YAHOO.util.Event.onAvailable("jsonInsertButtonContainer", 
								YAHOO.example.buttoninit);

//Once JSON is loaded, we want to simply display a message that indicates
//we were successful in bringing it into the page:
YAHOO.example.onJsonLoad = function( ){
	
	//Indicate on the page that the operation succeeded:
	YAHOO.util.Dom.get("jsonContainer").innerHTML = "The JSON utility was successfully loaded into the page.  Scroll through the Logger Console output at right to review the timeline of steps that were followed by the script; note that most recent log messages appear at the top.";
	
	//Log the completion of the process:
	YAHOO.log("JSON utility was successfully loaded into the page, and the page was updated to indicate success.  The process is complete.", "info", "example");

};

YAHOO.example.loaderinit = function() {
	YAHOO.log("YAHOO.example.loaderinit firing; we'll define our custom JSON module and load it now.", "info", "example");
	
	//Begin by creating a new Loader instance:
	var loader = new YAHOO.util.YUILoader();
	
	//Add the module to YUILoader
    loader.addModule({
        name: "json", //module name; must be unique
        type: "js", //can be "js" or "css"
        fullpath: "http://www.json.org/json2.js", //can use a path instead, extending base path
        varName: "JSON" // a variable that will be available when the script is loaded.  Needed
                        // in order to act on the script immediately in Safari 2.x and below.
		//requires: ['yahoo', 'event'] //if this module had dependencies, we could define here
    });

    loader.require("json"); //include the new  module

    // set the function that should exectute when the file loads
    loader.onSuccess = YAHOO.example.onJsonLoad;

	//Insert JSON utility on the page
    loader.insert();
	
};
</script></textarea>

<h3 id="overrideskins">Overriding skins</h3>
<p>In version 2.4.1 of the YUILoader, defining modules that override existing YUI
skins requires that you load them in two phases: 1) The YUI component, 2) The custom component:</p>

<textarea name="code" class="JScript" cols="60" rows="1">// first load treeview
new YAHOO.util.YUILoader({
    require: ['treeview'],
    onSuccess: function() {
        // when treeview is finished loading, define and load the custom tree modules
        var loader = new YAHOO.util.YUILoader();
        // define the css module that overrides the default treeview css
        loader.addModule({
          name:'customtreecss',
          type:'css',
          fullpath:'http://domain/customtree.css',
          requires:['treeview']
        }); 
        // define the script module that extends treeview
        loader.addModule({
          name:'customtree',
          type:'js',
          fullpath:'http://domain/customtree.js',
          // make the css file a requirement for the script
          requires:['customtreecss']
        }); 
        loader.onSuccess = function() {
            // build the custom tree
        };
        loader.insert();
    }
}).insert(); // insert the treeview component immediately
});</textarea>