@css>
#config_grid {
	border: 1px solid #f0f0f0;
}
#config_grid tr td {
	padding: 4px 10px;
	text-align: center;
}
#config_grid tr th {
	padding: 2px 10px;
	background-color: #f0f0f0;
}
#config_grid tr td:first-child {
	text-align: left;
}

@body>
<h2>
Overview
</h2>
<p>
{{:sitename}} uses named routes and a traversal/hierarchical approach to processing routes. Rather than a full URL representing a single route, each "directory" in the URL is placed on a "stack" and processed as an individual route. Each route can continue processing the next item in the stack or modify the behavior/stack based on the current state. This approach is based on the concept that top level routes need to run some code common to the subroutes and can act as a gatekeeper and/or preparer. This setup also allows the registering of routes on demand, only when needed. It is the job of the parent route to configure and register all the valid subroutes. 
</p>
<p>
For example, if a route and it's subroutes requires a user to be logged in, the parent route can check to see if the user is logged in. If the user is not logged, the route stack can be modified so that the next route to load is the login route instead of the requested route(s). This also allows the loading of any route under any URL, largely doing away with the need for expensive redirects.
</p>
All route functions, methods and variables are referenced via the routing instance.
<h4>$basecoat->routing->function()</h4>
<br />
Load initial routes using an associative array of route configurations. See Configuration section for options.
<pre>$basecoat->routing->setRoutes( array )</pre>

Add additional routes after the initial routes are loaded. Routes with the same name will be overwritten.
<pre>$basecoat->routing->addRoutes( array )</pre>

<br />

<h2>
Processing
</h2>
<p>
The function/file configuration options indicate which controller(s) to use to process the route (see Route Configuration in the Configuration section). If both are declared, both are executed with the function being run first. This allows simple routes to be declared as functions and larger routes to be declared as files that are included. By declaring both, the function can run pre-check code to see if the required file should be loaded (i.e. login check), or configure the environment.
<br />
Custom route parameters and meta data is support and can be read and processed by your routing code. For example, adding a "log_prefix" or "require_login" option that can be checked and used for processing.
</p>

<p>
<h4>setUrl()</h4>
The default URL to process is the currently requested URL. Optionally set a different URL to process, typically used on the command line for unit testing or running scripts.
</p>

<p>
<h4>processRequest()</h4>
Allow {{:sitename}} to automatically start the processing of the current URL by calling this function. An optional URL parameter can be provided to process a URL other than the requested one. Typically this would be done for unit tests or command line processing where multiple "URLs" need to be processed and this function is called multiple times. 
<br />{{:sitename}} will only process the first route in the URL, it is up to the route controller to call the next route in the stack by calling <code>runNext()</code>. This allows other routes to be run, new routes registered, configurations changed, before the next route is processed. Since routing is hierarchical, subroute configurations are loaded dynamically using the <code>addRoutes()</code> method. Typically additional route configurations are registered by the current route controller.
<blockquote>
<b>Typical processing flow:</b><br />
+ route controller executed<br />
+ route controller configures/registers subroutes<br />
-> <code>runNext()</code> called<br />
<i>repeat as needed</i>
</blockquote>

<p>
<h4>run( $route_name )</h4>
Run a configured route. Pass the name of the route as configured in the registered routes array.
</p>

<h4>runNext()</h4> 
This function can be called whether there is another route on the stack to process or not. If there are no more routes on the route stack, control simply returns to {{:sitename}} for final processing.
<br />


<p>
<h2>Example of URL processing</h2>
<b>URL:</b> http://example.com/route1/route2/<br />
<b>Route stack:</b> route1, route2<br />
<code>$basecoat->routing->run_routes = array('route1', 'route2');</code><br />
<br />
<b>index.php</b> loads primary routes and initiates processing
<pre>
// Configure and set routes
$routes = array(
	'route1'	=> array(
		'file' 		=> 'route1.php',
		'template'	=> 'route1.tpl.php',
	),
);
$basecoat->routing->setRoutes($routes);

// Process current URL
$basecoat->processRequest();
</pre>
<br />
<b>route1.php</b> processes first route on the stack, loads subroutes
<pre>
// Add route content to page
$content	= $basecoat->view->newView();
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

// Configure and register new routes
$routes = array(
	'route2'	=> array(
		'file' 		=> 'route2.php',
		'template'	=> 'route2.tpl.php',
	),
);
$basecoat->routing->addRoutes($routes);
$basecoat->routing->runNext();
</pre>

<br />
<b>route2.php</b> processes next route on stack, loads subroutes
<pre>
// Add route content to page
$content	= $basecoat->view->newView();
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

// Always safe to call, even if no more routes to run
$basecoat->routing->runNext();
</pre>

</p>


<h2>Static Files &amp; Special Routes</h2>
<p>
{{:sitename}} provides some built-in functionality for certain standard default routes. These special routes are: static, undefined. They are used for processing static content files and to process an undefined route respectively.
</p>
<h4>static</h4>
<p>
The static route is used for processing static content files (i.e. html) and merging them with the configured layout. There is no need to configure a route for each file if the file has no special processing requirements. When a requested URL is not found in the configured route list, the static templates directory will be checked for a file with a matching name. Both the "route" name and the route name + .html are checked. The route name is sanitized (leading and trailing / and spaces are removed) before checking the for a file. If a file exists, an alias of the "static" route will be dynamically configured and run. 
<br />By default, a route called <code>static</code> should always be configured. The name of this route can be customized with the <code>setStatic([routename])</code> method.
</p>

<h4>undefined</h4>
<p>
If there is no matching configured route found, and no static file matching the requested route, then the <code>undefined</code> route will be run. By default, a route called <code>undefined</code> should always be configured. The name of this route can be customized with the <code>setUndefined([routename])</code> method.
</p>

<h2>Routing Class Variables</h2>
<h4>requested_route (string)</h4>
<p>
The original route that was requested to be run, persists through all routes.
</p>
<h4>run_routes (array)</h4>
<p>
The list of routes to be run. This is treated as a stack, whereby each route is taken off the top and run. The <code>runNext()</code> method is used to process and remove the next route in the stack.
</p>
<h4>running_route (string)</h4>
<p>
The name of the current route being run.
</p>
<h4>current (array)</h4>
<p>
The configuration information about the currently running route.
</p>
<h4>profiling (array)</h4>
<p>
Array of profiling information for each route run and how long it took. Helpful for debugging and performance tuning.
<pre>
// Example output of profiling with 1 route run
Array
(
    [start] => 1355678392.207
    [routes] => Array
        (
            [0] => Array
                (
                    [route] => /
                    [time] => 0.00099992752075195
                    [start] => 1355678392.207
                    [end] => 1355678392.208
                    [seq] => 1
                )

        )

)
</pre>
</p>

<br />
<br />
