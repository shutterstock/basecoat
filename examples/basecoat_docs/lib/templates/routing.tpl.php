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
<div class="section_title">
Routing
</div>
<p>
The entire framework process flow is based around the concept of routes.
Every request that comes in is analyzed to determine what route should be run.
Each route is self contained and is typical only concerned with compiling content for output.
The Front Controller performs some basic checks to make sure a route can be run.
This can include https and/or login dependencies, and whether to load from cache or not.
Dependency settings are set in the <code>$_ROUTES_</code> configuration array.
</p>

<div class="subsection_title">
Route Settings
</div>
<p>
You can add any name/value settings to each route entry to adjust it's behavior.
By default the framework supports <code>require_secure</code> and
<code>require_login</code> and will automatically check for and enforce these setting on the Front Controller level (index.php). 
<pre>
$_ROUTES_ = array(
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
The framework will automatically redirect if it detects it is in the "wrong" secure mode. Note that POST information is not preserved.
</p>

<div class="subsection_title">
Route Stack
</div>
<p>
When a web request comes in, the <code>bootstrap.web.php</code> will analyze the URL, parse it into routes that need to run
and verify that the initial route is a valid one.
The name/key of the current route is stored in the <code>Core::$current_route</code> variable.
Additional routes that need to be run are stored in the <code>Core::$run_routes</code> array and should be "popped" off
the stack as they are run.
<br />
Every URL does not require an entry in the <code>$_ROUTES_</code> array, only the first level URLs require a route configuration.
It is the responsibility of the current route to check for, validate and run any additional routes that are on the stack.
The route stack is simply a queue that needs to be processed, routes can add or remove routes from the stack as needed.
Routes can also add to the list of valid routes, or overload routes already defined in the list.
</p>

<div class="subsection_title">
Subroutes
</div>
<p>
The Front Controller will execute routes as long as the Core::$current_route does not match the previous route that was run.
It is the responsibility of the currently running route to chain the routes by changing the Core::$current_route value.
Typically a subroute will set the Core::$current_route to the next route on the the stack after performing configuration changes
and validation checks.
This creates a hierarchy so all possible URLs do not need to be defined from the start.
<br />
Any subroutes that are defined must be merged into the master <code>$_ROUTES_</code> list since this is the only list
referenced by the Front Controller. Below is an example on how a subroute controller can be implemented.
<pre>
// Define subroutes
$_SUBROUTES_ = array(
	'subroute'	=> array(
		'file' 		=> PATH_ROUTES . 'subroute.php',
		'template'	=> PATH_TEMPLATES . 'subroute.tpl.php',
	),
);

// Merge subroute into master list
$_ROUTES_ = array_merge($_ROUTES_, $_SUBROUTES_);

// Set next route to run
Core::$current_route	= 'subroute';

// Return control to Front Controller/index.php
return;

</pre>
</p>

<div class="section_title">
Re-routing
</div>
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

<div class="section_title">
Introspection
</div>
<p>
Since the primary settings for what is run is in a single array, it is easy to perform introspection to review the settings.
Introspection is used by most routes to determine what template file to load with the route.
This keeps the business logic/controller separate from the view.
<br />
For example, a route will determine which template to load by referencing the <code>$_ROUTES_</code> configuration array.
<pre>
$template = $_ROUTES_[Core::$current_route]['template'];
</pre>
</p>

<p>
Below is a list of all the primary routes that are currently defined, and can be run. 

<div class="subsection_title">
Primary Routes List (<?php echo count($this->routes);?>)
</div>

<div>
<table class="table table-striped table-condensed">
<th>Route</th>
<th>Data Only</th>
<th>HTTPS</th>
<th>Login Required</th>
</tr>
<?php
foreach($this->routes as $params) {
	echo '<tr><td><strong>'.$params['name'].'</strong></td>';
	echo '<td>'.$params['data_only'].'</td>';
	echo '<td>'.$params['require_secure'].'</td>';
	echo '<td>'.$params['require_login'].'</td></tr>';
}
?>
</table>
</div>
</p>
