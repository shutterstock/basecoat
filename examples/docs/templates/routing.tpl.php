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
{{:sitename}} uses named routes and a hierarchical approach to processing routes. Rather than a full URL representing a single route, each directory in the URL is placed on a "stack" and processed as an individual route. Each route can continue processing the next item in the stack or modify the behavior based on current state. This approach is based on the concept that top level routes need to run some code common to the subroutes and can act as a gatekeeper and/or preparer. This setup also allows the registering of routes on demand. It is the job of the parent route to configure and register all the valid subroutes. 
</p>
<p>
For example, if a route and it's subroutes requires a user to be logged in, the parent route can check to see if the user is logged in. If the user is not logged, route stack is modified so that the next route to load is the login route instead of the requested route(s). After all, the URL is a request not a demand. This also allows the loading of any route under any URL, largely doing away with the need for expensive redirects.
<pre>
// Requested URL:
http://www.hostname.com/route/subroute1/subroute1/

// Route list:
route
subroute1
subroute2
</pre>

</p>

<h2>
Route Configuration
</h2>
<p>
Routes are configured by declaring an associative array of named routes. The array key is the name of the route, which is used to perform an indexed lookup for the proper route to run. This provides a very fast, scalable route processing architecture. Each route has an array of configuration information associated within that is used to determine how to execute the route. Each route can have a function, an include file, or both associated with it to perform the route processing. Defined route configuration options are as follows:

<ul>
<li>function => (function) a callable function (i.e. anonymous function) to execute for processing</li>
<li>file => (string) valid include path and file to load to process the route</li>
<li>require_secure => (integer) automatically redirect the user to the secure version of the URL requested</li>
<li>cacheable => (array) provides a way of declaring that a route is cacheable and how to cache.
	<ul>
	<li>expires => (string) a valid strtotime string indicating how long to cache the content for. Currently only adds cache headers to the output.</li>
	</ul>
</ul>

<pre>
$routes = array(
	'/' => array(
		'file' => '/path/to/route_file.php',
		'template' => 'index_tpl.php',
	),
	'example' => array(
		'file' => '/path/to/example_route.php',
		'template' => 'route_tpl.php',
		'require_secure' => 1,
	),
	'static' => array(
		'file' => '/path/to/static_route.php',
		'cacheable' => array(
			'expires' => '1 hour'
		)
	),
	'not_found' => array(
		'file' => '/path/to/404_route.php',
		'template' => '404_tpl.php',
	),

);
</pre>

The function/file configuration options essentially indicate which controller(s) to use for the route. If both are declared, both are executed with the function being run first. You can add any other route parameters that can be read and processed by your routing code. For example, adding "layout" and  "template" options to indicate which layout and template to use for output.
</p>





<br />
<br />

The entire process flow is based around the concept of routes.
Every request that comes in is parsed to determine what route should be run.
Each route is self contained and is typically only concerned with compiling a subset of the content to be inserted into the defined layout.
The Front Controller performs some basic checks to make sure a route can be run under the current conditions.
This can include https and/or login dependencies, and whether to load from cache or not.
Routes and dependency settings are set in the <code>Core::$routes</code> configuration array. 
This is an associative array where the key is the name of the route and the value is an array of settings for the route.
Each route must have a 'file' setting, which is the file that will be "included" to execute the route.
Typically a template is also defined, which is what the route will use for output.
<pre>
Core::$routes	= array(
'home'	=> array(
	'file' 		=> PATH_ROUTES . 'home.php',
	'template'	=> PATH_TEMPLATES . 'home.tpl.php'
	),
'preferences'	=> array(
	'file' 		=> PATH_ROUTES . 'prefs.php',
	'template'	=> PATH_TEMPLATES . 'prefs.tpl.php'
	),
'search'	=> array(
	'file' 		=> PATH_ROUTES . 'search.php',
	'template'	=> PATH_TEMPLATES . 'search.tpl.php'
	),
...
);
</pre>
</p>

<br />
<h3>
Route Stack
</h3>
<p>
When a web request comes in, the <code>url_parser.php</code> file will analyze the URL, parse it into routes that need to run,
and verify that the initial route is a valid one.
The name/key of the current route is stored in the <code>Core::$current_route</code> variable.
Additional routes that need to be run are stored in the <code>Core::$run_routes</code> array and should be "popped" off the stack as they are run.
For example, the url http://exmaple.com/route1/route2/route3/ would result in the current route being set to "route1", then "route2" and "route3" would be added to the run routes array.
<pre>
http://example.com/route1/route2/route3/

Core::$current_route	= "route1";
Core::$run_routes	= array("route2","route3");
</pre>

</p>
<p>
Every URL does not require an entry in the <code>Config::$routes</code> array, only the first level URLs require a route configuration.
It is the responsibility of the current route to check if there is an additional route to run, "pop" it off the stack and set it as the current route.
The route stack is simply a queue that needs to be processed, routes can add or remove routes from the stack as needed.
Routes can also add to the list of valid routes, or overload routes already defined in the list.
This allows chaining and dynamic loading of different routes based on conditions, instead of using redirects.
</p>

<br />
<h3>
Subroutes
</h3>
<p>
The Front Controller will execute routes as long as the Core::$current_route does not match the previous route that was run.
It is the responsibility of the currently running route to chain the routes by changing the Core::$current_route value.
Typically a subroute will set the Core::$current_route to the next route on the the stack after performing configuration changes
and validation checks.
This creates a hierarchy so all possible URLs do not need to be defined from the start, only the primary ones.
<br />
Any subroutes that are defined must be merged into the master <code>Config::$routes</code> list since this is the only list
referenced by the Front Controller. Below is an example of how a subroute controller can be implemented.
<pre>
// Define subroutes
$_SUBROUTES_ = array(
	'subroute1'	=> array(
		'file' 		=> PATH_ROUTES . 'subroute/index.php',
		'template'	=> PATH_TEMPLATES . 'subroute/index.tpl.php',
	),
	'subroute2'	=> array(
		'file' 		=> PATH_ROUTES . 'subroute/route2.php',
		'template'	=> PATH_TEMPLATES . 'subroute/route2.tpl.php',
	),
	...
);

// Merge subroute into master list
Config::$routes = array_merge(Config::$routes, $_SUBROUTES_);

// Set next route to run
Core::$current_route	= 'subroute';

// Return control to Front Controller/index.php
return;

</pre>
</p>

<br />
<h3>
Route Settings
</h3>
<p>
You can add any name/value settings to each route entry to adjust it's behavior.
By default the framework supports <code>require_secure</code> and
<code>require_login</code> and will automatically check for and enforce these setting on the Front Controller level (index.php). 
<pre>
Config::$routes = array(
...
'example'	=> array(
	'file' 		=> PATH_ROUTES . 'example.php',
	'template'	=> PATH_TEMPLATES . 'example.tpl.php',
	'require_secure'	=> 1,
	'require_login'		=> 1,
	'custom_setting'	=> 'abc'
),
...
);
</pre>
</p>
<p>
The <code>require_secure</code> configuration directive supports 3 values:
<ul>
<li>0 - load only under http</li>
<li>1 - load only under https</li>
<li>2 - can load under either http or https is fine (i.e. ajax calls)</li>
</ul>
The framework will automatically redirect if it detects it is in the "wrong" security mode. Note that POST information is not preserved.
</p>

<br />
<h3>
Re-routing
</h3>
<p>
Since one route can initiate loading of another, this allows almost any route to be loaded under any URL.
This largely obviates the need for performing redirects, which can be expensive and complicated when form POSTs are involved.
An ideal example is requiring login for a page to be accessed. 
While this is handled by the framework by default, it does use the concept of re-routing and chaining.
</p>
<p>
When a route is requested that requires login, the login state is checked and if it fails, the current route is
set to the login route. Thus the login route loads under the current URL, rather redirecting to a login page.
The login route sets the login form to POST to the current URL.
Once submitted, the same login check is run and the login route loaded instead.
The login route can then check for a POST and run the appropriate checks.
If the login is successful, the current route is set to the requested route and the requested page is loaded as normal.
<br />
The database section is configured to require login as an example of how to implement this type of processing flow.
</p>


<br />
<h3>
Also Known As / A.K.A. Aliasing
</h3>
<p>
If the same page needs be loaded under different "names", whether for backwards compatibility or SEO reasons, an alias can be set. 
For example, this page can also be loaded under <a href="?page=conf">?page=conf</a> or <a href="?page=ini">?page=ini</a>.
Think about how easy it would be to setup unique, SEO friendly URLs in multiple languages. 
<pre>
Core::$routes = array(
...
);
// Aliases for configuration route
Core::$routes['conf'] 	= &Core::$routes['configuration'];
Core::$routes['ini']	= &Core::$routes['configuration'];
Core::$routes['akafoo']	= &Core::$routes['configuration'];
</pre>

</p>

<br />
<h3>
Introspection
</h3>
<p>
Since the primary settings for what is run is in a single array, it is easy to perform introspection to review the settings.
Introspection is used by most routes to determine what template file to load with the route. 
The same route file can be loaded under different "names", and different template files used for each "name".
This keeps the business logic/controller separate from the view.
<br />
For example, a route will determine which template to load by referencing the <code>Config::$routes</code> configuration array.
<pre>
$template = Config::$routes[Core::$current_route]['template'];
</pre>
</p>

<p>
Below is a list of all the primary routes that are currently defined, and can be run. 

<br />
<h3>
Primary Routes List (<?php echo count($this->routes);?>)
</h3>

<div>
<table class="table table-striped table-bordered">
<th>Route</th>
<th>Data Only</th>
<th>HTTPS</th>
<th>Login Required</th>
</tr>
<?php
foreach($this->routes as $params) {
	echo '<tr><td>'.$params['name'].'</td>';
	echo '<td>'.$params['data_only'].'</td>';
	echo '<td>'.$params['require_secure'].'</td>';
	echo '<td>'.$params['require_login'].'</td></tr>';
}
?>
</table>
</div>
</p>
