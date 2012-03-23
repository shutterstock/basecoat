@body>
<div class="section_title">
Configuration over Convention
</div>
<p>While some frameworks are based around a "naming" convention to work, <?php echo Content::$page->sitename;?> opts for configuration over convention. 
There is no naming convention to learn where things magically work, but only if named properly.
There are very good and valid reasons for using one over the other.
<?php echo Content::$page->sitename;?> relies on a base configuration to get started, 
and then other configuration files are loaded as needed, 
based on a defined inheritance hierarchy and what is requested.
</p>
<div class="subsection_title">
bootstrap.inc.php
</div>
<p>This is the core "boot up" file that handles the base configuration that will be required for running in any mode (web, command line, etc).
This is the first configuration file that will need to be customized, and manages global configurations for: 
include paths, database, memcache, and any other configurations that need to be loaded by default.
It will also load instances of the core framework (core.class.php) classes and automatically determine which mode it is running under (web or command line).
<br />
After detecting the mode/environment, configuration variables are set (Config::$run_env, Config::$run_mode) so that the rest of the
code can check and adjust it's behavior accordingly.
</p>

<div class="subsection_title">
bootstrap.web.php
</div>
<p>When running under web mode, the <code>bootstrap.web.php</code> file is loaded. 
Web specific configurations are loaded and processed, like <code>config.web.php</code>.
Once everything is configured and loaded, the URL is parsed, a list of routes to run is created, and the first "route" to run is set.
Control is then passed back to the Front Controller (index.php), which then handles running the routes.
</p>

<div class="subsection_title">
config.web.php
</div>
<p>
This is the core web configuration file where web specific classes are loaded, you configure your primary URL routes, common includes (i.e. layout, header, footer), 
and other settings unique to your setup. The core of this configuration file is the <code>$_ROUTES_</code> associative array.
This is simply a name associated with a group of settings that the framework uses to figure out what to do and which file(s) to load for what URL.
The array "key" is the unique name the URL will map to. Each entry in the array must have a "file" setting, 
and typically a "template" setting.
This is how the framework determines which "file" to load, and which "template" to use.
</p>
<p>For example, for this page to load, the URL was parsed and the first named "route" to run was determined to be the "configuration" route.
The route name is looked up in the <code>$_ROUTES_</code> array, the file name entered in the "file" entry is then included as a php file.
For this page, that route file is <code>configuration.php</code> and the template to be used is <code>configuration.tpl.php</code>
<pre>
$_ROUTES_ = array(
...
'configuration'	=> array(
	'file' 		=> PATH_ROUTES . 'configuration.php',
	'template'	=> PATH_TEMPLATES . 'configuration.tpl.php',
),
...
);

</pre>

</p>

<div class="section_title">
URL Structure &amp; Mapping
</div>
<p>
Keeping flexibility in mind, parameter based URLs or server side URL rewrite rules can be used. 
You don't have to choose one over the other, the framework can toggle between them by just changing the value of a variable.
</p>
<p>
<div class="subsection_title">
Parameter Based URLs
</div>
The default URL parameter that specifies the routes is called "page". 
This is configureable in <code>config.web.php</code> by the variable <code>Config::$route_param</code>.
Multiple routes/subroutes can be specified by separating the route names with periods.
<br />
For example, to specify the configuration route and the settings subroute:
<pre>
http://hostname.com/?page=configuration.settings
</pre>
</p>

<p>
<div class="subsection_title">
Rewrite Based URLs
</div>
When using rewritten URLs, the framework parses the URL based on the / character.
For example:
<pre>
http://hostname.com/configuration/settings
</pre>
</p>
<div class="subsection_title">
URL Mapping
</div>
<p>
Once the URL is parsed, and the requested route is determined, the route list is checked. 
If there is no matching route found, the /templates/static directory will be checked for a matching file.
The .html suffix will be append to the requested route, and file check will be performed.
If a matching file is found, it will be loaded under the default html route and frame in the layout specified for the html route.
This behavior allows you to open up the /templates/static directory to designer, marketing or other groups 
for creating basic, static html pages without worrying about "breaking the site".
</p>
<p>
All of the code for parsing the URL and setting up the routes is contained in the <code>bootstrap.web.php</code> file.
So if you require your own special URL setup, you can modify it to fit your needs without having to
make extensive changes to the framework code.
</p>

<div class="section_title">
Also Known As / A.K.A Aliasing
</div>
<p>
If the same page needs be loaded under different "names", whether for backwards compatibility or SEO reasons, an alias can be set. 
For example, this page can also be loaded under <a href="?page=conf">?page=conf</a> or <a href="?page=ini">?page=ini</a>.
You can setup your own alias for this page by editing the <code>config.web.php</code> file and adding <code>$_ROUTES_['akafoo']= &$_ROUTES_['configuration'];</code> to the end of the file. 
You could then load this page under ?page=akafoo also.
<br />
Think about how easy it would be to setup unique, SEO friendly URLs in multiple languages. 
<pre>
$_ROUTES_ = array(
...
);
// Aliases for configuration route
$_ROUTES_['conf'] 	= &$_ROUTES_['configuration'];
$_ROUTES_['ini']	= &$_ROUTES_['configuration'];
$_ROUTES_['akafoo']	= &$_ROUTES_['configuration'];
</pre>

</p>

<div class="section_title">
Processing Hooks
</div>
<p>
There are 3 processing hooks available for including files are various points of processing.
Hooks are available for before routes are run, templates to include after all routes are run, and after page output.
</p>
<div class="subsection_title">
Config::$pre_includes
</div>
<p>
This array contains a list of files that should be included before any routes are run.
Typically some logic would be placed in the config.web.php file to determine what additional files should be included.
For example, if running and A/B test, a user's bucket can be determined the appropriate files an be included that would override default route settings, templates, etc.
</p>

<div class="subsection_title">
Config::$page_includes
</div>
<p>
Template files to load after all routes have been run.
Typically these will be common header, footer, navigation, etc.
Routes can modified and/or override this list to alter final output.
</p>

<div class="subsection_title">
Config::$post_includes
</div>
<p>
This array contains a list of files that should be included after page output.
Since the page output has been completely, this can be longer running processes that would normal delay page delivery.
Typically and logging would be registered to occur here.
</p>

