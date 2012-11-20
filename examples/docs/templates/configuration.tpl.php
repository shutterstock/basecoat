<h2>
Overview
</h2>

<p>
{{:sitename}} configurations are typically based around names assigned to configuration blocks. For example, names are assigned to individual routes and layouts. This allows code to reference items by name, what the name represents is dynamic based on conditions and events. This also allows aliasing, where multiple names can be assigned to the same configuration. For example, multi-lingual URLs that all route to the same code.
</p>
<p>
Loading {{:sitename}} is as easy as including a file and creating an instance. What gets run and when is up to you. {{:sitename}} can automatically parse the current URL or you can specify a URL for it to parse and use. Configure by specifying the layout files, the default layout to use and the template path for loading template files. Routes specify the file and/or function to run, and which template to use.
</p>

<p>
{{:sitename}} is an "include" framework. You do not need to build your website "inside" the framework, 
the framework is loaded by your code by instantiating the {{:sitename}} class. This setup allows multiple websites to be built around a single, common install of the framework. It also allows migration of an existing website to the {{:sitename}} framework since you call only the functions and methods you need. There is no naming convention to learn to get things to "magically" work.
</p>

<h4>Basic Processing Example</h4>
<pre>
require_once('/basecoat/basecoat.php');
$basecoat = new \Basecoat\Basecoat();

$basecoat->view->setLayouts(
	array(
		'default' => 'layouts/path/common.php'
	)
);
$basecoat->view->setLayout('default');
$basecoat->view->setTemplatesPath('templates/path');

$basecoat->routing->setRoutes(
	array(
		'/'	=> array(
			'file' => 'routes/path/index.php',
			'template' => 'index.tpl.php',
		),
		'example' => array(
			'function'	=> function() use ($basecoat) {
			   ...
			},
			'template' => 'exmaple.tpl.php'
		)
	)
);

$output	= $basecoat->processRequest();
echo $output;
</pre>

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

<br />
<h4>
URL Structure
</h4>
<p>
Most web sites use mod_rewrite or something similar to create friendly/pretty URLs, instead of using URL parameters.
Since some websites may be hosted in a shared hosting environment, or have complex development environments where rewrite rules aren't available, the framework can be used in either mode. 
Switching modes does not require extensive code changes, just a configuration toggle, so it's possible to develop using URLs parameters and switch to using pretty urls in production. 
</p>
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
The configuration options below assume that {{:sitename}} was instantiated with the variable name <code>$basecoat</code>. Of course, you can use any variable name you want. There are a few configuration settings that have defaults, but also allow you to add your own settings and values for use by your own code. This allows your to store your settings in {{:sitename}} if you like. In general, {{:sitename}} variables and setting are public to allow you to do as you please. {{:sitename}} does not try to protect yourself.
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

<dt>$basecoat->routing->set('name', 'value');</dt>
<dd>Name/value pair settings, add your own in addition to the defaults shown below.
<br />
<code>use_pretty_urls:</code> default true. Whether to use mod_rewrite or parameter based routing<br />
<code>route_param:</code> default page. Routing parameter name if using parameter based routing<br />
<code>profiling:</code> default false. Enable/Disable route profiling.<br />
</dd>
</p>

<p>
<dt>$basecoat->view;</dt>
<dd>Variable to hold the primary instance of the View controller. If you want to replace the one that comes with {{:sitename}}, load your own instance into this variable.
</dd>

<dt>$basecoat->routing;</dt>
<dd>Variable to hold the primary instance of the Routing controller. If you want to replace the one that comes with {{:sitename}}, load your own instance into this variable.
</dd>
</p>

<h2>
Processing Hooks
</h2>
<p>
There are 4 processing hooks available for running custom code at various points of processing. Hooks are added by passing a callable function to the appropriate method. Multiple functions per hook is supported. All hooks for a specific processing point can be cleared by calling the appropriate clear method.
<ol>
<li>Before rendering of content for output<br />
<dt>$basecoat->addBeforeRender( function );</dt>
<dt>$basecoat->clearBeforeRender();</dt><br />
</li>
<li>After rendering of content for output<br />
<dt>$basecoat->addAfterRender( function );</dt>
<dt>$basecoat->clearAfterRender();</dt><br />
</li>
<li>Before processing of each route<br />
<dt>$basecoat->routing->addBeforeEach( function );</dt>
<dt>$basecoat->routing->clearBeforeEach();</dt><br />
</li>
<li>After processing of each route<br />
<dt>$basecoat->routing->addAfterEach( function );</dt>
<dt>$basecoat->routing->clearAfterEach();</dt><br />
</li>
</ol>
</p>
<br />
