<h2>
Overview
</h2>

<p>
{{:sitename}} configurations are typically based around associative arrays that assign names to configuration blocks. For example, names are assigned to individual routes and layouts. This allows code to reference items by name, what the name represents can be dynamic based on conditions and events (overloading). This also allows aliasing, where multiple names can be assigned to the same configuration. For example, multi-lingual URLs that all route to the same code.
</p>

<p>
{{:sitename}} is an "include" framework. You do not need to build your website "inside" the framework, 
the framework is loaded by your code by instantiating the {{:sitename}} class. This setup allows multiple websites to be built around a single, common install of the framework.
</p>

<br />
<h4>
File/Folder Structure
</h4>
<pre class="pre-scrollable">
/basecoat
	basecoat.php
	/classes
		content.class.php
		routing.class.php
		db.pdo.php
	/templates
		messages.tpl.php
	
/your_site
</pre>

<h2>
Initialization
</h2>
<p>
Initializing {{:sitename}} is done by creating and instance of the core {{:sitename}} class. Most implementations will then configure the following:
<ol>
<li>Configure Layouts and set default layout<br />
<code>$basecoat->view->setLayouts( array );</code><br />
<code>$basecoat->view->setLayout('layout ID');</code>
</li>
<li>Set the Template directory path<br />
<code>$basecoat->view->setTemplatesPath('templates/path');</code>
</li>
<li>Configure the Routes<br />
<code>$basecoat->routing->setRoutes( array );</code>
</li>
</ol>
</p>


<br />
<h4>
URL Structure
</h4>
<p>
Typically web sites use mod_rewrite or something similar to create friendly/pretty URLs, instead of using URL parameters.
Since some websites may be hosted in a shared hosting environment, or have complex development environments where rewrite rules aren't available, the framework can be used in either mode. 
Switching modes does not require extensive code changes, just a configuration toggle, so it's possible to develop using URL parameters and switch to using "pretty" urls in production. 
<br />
<pre>
$basecoat->routing->set('use_pretty_urls', true);
$basecoat->routing->set('route_param', 'page');
</pre>
</p>
<p>
The default route parameter is <code>page</code>, but is configureable to be anything. Multiple routes can be specified by using <code>.</code> as the delimiter.
<p>
Example of parameter based URL with multiple routes specified:
<pre>
http://hostname.com/?page=configuration.settings
</pre>

Example of mod_rewrite based URL with multiple routes specified. Note: an ending / is optional and does not affect processing:
<pre>
http://hostname.com/configuration/settings
</pre>

</p>

<h2>
Configuration Options
</h2>
{{:sitename}} configures some default settings, but also supports registering any name/value pair as a setting. This allows you to centralize  your site settings in {{:sitename}}. In general, {{:sitename}} variables and setting are public to allow you to do as you please. {{:sitename}} does not try to protect you from yourself.
<p>
<dt>$basecoat->setTemplatesPath();</dt>
<dd>The root directory where the View controller will look for template files. This value prefixes each template specified in the route configuration.
</dd>

<dt>$basecoat->headers( array );</dt>
<dd>Array of name/value pairs to output as headers. Defaults listed below.
<br />
<code>Content-type:</code> text/html; charset=UTF-8<br/>
<code>X-Powered-By:</code> Basecoat PHP framework
</dd>

</p>

<p>
<dt>$basecoat->view;</dt>
<dd>Primary instance of the View controller. In general, it will be this instance that all other instances of the View controller are merged into.
</dd>

<dt>$basecoat->routing;</dt>
<dd>Primary instance of the Routing controller. If you want to replace the one that comes with {{:sitename}}, load your own instance into this variable.
</dd>

<dt>$basecoat->routing->set('name', 'value');</dt>
<dd>Name/value pair settings, add your own in addition to the defaults shown below.
<br />
<code>use_pretty_urls:</code> default <b>true</b>. Whether to use mod_rewrite or parameter based routing<br />
<code>route_param:</code> default <b>page</b>. Routing parameter name if using parameter based routing<br />
<code>profiling:</code> default <b>false</b>. Enable/Disable route profiling.<br />
</dd>

</p>

<h2>
Route Configuration
</h2>
<p>
Routes are configured by declaring an associative array of named routes. The array key is used to map the route name to the route configuration and route meta data. The route name  is used to perform an indexed lookup for the proper route to run. This provides a very fast, scalable route processing architecture. Each route has an array of configuration information associated with it that is used to determine how to process the route. Each route can have a function, an include file, or both, associated with it to perform the route processing. Defined route configuration options are as follows:

<ul>
<li><b>function</b> (function) a callable function (i.e. anonymous function) to execute for processing</li>
<li><b>file</b> (string) valid include path and file to load to process the route</li>
<li><b>template</b> (string) a valid file name in the templates directory. This parameter is used internally by {{:sitename}} for special processing (i.e. servicing static files).
<li><b>require_secure</b> (integer) automatically redirect the user to the secure version of the URL requested</li>
<li><b>cacheable</b> (array) provides a way of declaring that a route is cacheable and how to cache.
	<ul>
	<li><b>expires</b> (string) a valid <code>strtotime</code> string indicating how long to cache the content for. Currently only adds cache headers to the output.</li>
	</ul>
<li><b>layout</b> (string) name of the layout to use for rendering. Must map to a layout in the defined layouts list.</li>
</ul>

<pre>
// Example route configuration array
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
</p>

<h4>Aliases</h4>
<p>
Since the route configuration is just an associative array, configuring multiple names for the same route is very simple. This is helpful when you have multiple URLs that load the same or very similar content. The same route configuration can be assigned aliases. The route itself could then check which name it was loaded under and adjust it's behavior accordingly.
<pre>
$routes['alias_name'] =& $routes['example'];
$routes['seo_name'] =& $routes['example'];
</pre>
</p>

<h4>Meta Data</h4>
<p>
{{:sitename}} has some predefined route parameters that it checks for, but you can add any meta data as additional array items. This extra meta data can then be referenced by the routes. For example, a <code>require_login</code> meta data item can be configure and a processing hook (next section) can be configure to check for this parameter before allowing route execution. Another example is specifying a canonical URL to support looking up URLs for other routes to support redirects.
</p>

<h2>
Processing Hooks
</h2>
<p>
There are 4 processing hooks available for running custom code at various points of processing. Hooks are added by passing a callable function to the appropriate method. Multiple functions per hook is supported. All hooks for a specific processing point can be cleared by calling the appropriate clear method.
<ol>
<li>Before processing of each route<br />
<dt>$basecoat->routing->addBeforeEach( function );</dt>
<dt>$basecoat->routing->clearBeforeEach();</dt><br />
</li>
<li>After processing of each route<br />
<dt>$basecoat->routing->addAfterEach( function );</dt>
<dt>$basecoat->routing->clearAfterEach();</dt><br />
</li>
<li>Before rendering of content for output<br />
<dt>$basecoat->addBeforeRender( function );</dt>
<dt>$basecoat->clearBeforeRender();</dt><br />
</li>
<li>After rendering of content for output<br />
<dt>$basecoat->addAfterRender( function );</dt>
<dt>$basecoat->clearAfterRender();</dt><br />
</li>
</ol>
</p>
<br />
