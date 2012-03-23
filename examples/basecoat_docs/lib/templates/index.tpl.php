<div class="section_title">
Introduction
</div>
<p>
<?php echo Content::$page->sitename;?> is a flexible PHP framework based on modern design patterns like Front Controller, MVC and Inversion of Control.
<?php echo Content::$page->sitename;?> is first and foremost designed to be quick and easy to start using, not a framework you learn how to use. 
By the time you finish reading this page, you should be able to add your own pages to this documentation.
By focusing on simplicity the framework is fast and scalable.
However, extensibility has not been sacrificed in the name of simplicity.
<ul>
<li>Support for multiple languages in both the URL and content through a simple configuration.</li>
<li>Templating system based on native PHP, but can also be modified to support systems like Twig and Smarty.</li>
<li>Support for parameter based URLs, or SEO friendly "pretty" URLs</li>
<li>Comes with an optional database class that is based on PDO, uses data bindings, and supports master/slave configurations.</li>
<li>Support for command line scripts</li>
</ul>
Since most web sites also require backend processes to run periodically, <?php echo Content::$page->sitename;?> also supports running scripts from the command.
It automatically detects whether it is web or command line mode and will adjust behavior accordingly (i.e. suppressing session start).
</p>

<p>
Almost all website now use "pretty" URLs. This requires Apache mod_rewrite rules, .htaccess files, or other special server configuration.
Since not all hosts allow the required configuration permissions, <?php echo Content::$page->sitename;?> supports either both "pretty" URLs or parameter based URLs by simply
changing the value of a configuration variable.
</p>

<p>
For convenience, an implementation of this documentation using the <?php echo Content::$page->sitename;?> framework is available.
The sample implementation can be used to understand how the framework works from the code level.
</p>

<div class="section_title">
File/Folder Structure
</div>
<p><?php echo Content::$page->sitename;?> is based on the Front Controller design pattern 
and thus everything is processed through the <code>index.php</code> file.
That is the only PHP file that is required to be in a publicly accessible directory.
All other files are the in the /lib directory, which may placed outside of the web root for extra security.
The <code>index.php</code> file must declare where the /lib directory is located, and then load the <code>bootstrap.web.php</code> file.
All include paths are then configured automatically relative to /lib.</p>

<pre>
/www
	index.php
	/css
	/files
	/img
	/js
/lib
	bootstrap.inc.php
	bootstrap.web.php
	/classes
	/config 		//customize files in this directory for your setup
		config.web.php	//web specific configuration
		config.php	//configuration and code to load for both cli and web
	/includes
	/routes
	/scripts
	/templates
		/common
		/layouts
</pre>
</p>

<div class="section_title">
URL Structure
</div>
<p>
Most web sites use mod_rewrite or something similar to create friendly/pretty URLs, instead of using URL parameters.
Since some websites may be hosted in a shared hosting environment, or complex development environment,
 where mod_rewrite may not be possible, the framework can be used in either mode. 
Switching modes does not require any code changes, so it's possible to develop using URLs parameters 
and switch to using mod_rewrite in production.
Simple toggling the <code>Config::$use_pretty_urls</code> variable in <code>config.web.php</code> will switch
between "pretty" URLs and parameter based URLs.

</p>

<div class="section_title">
Flow Overview
</div>
<br />
<strong>index.php</strong>
<p>This is the Front Controller of the framework, almost everything starts and ends here. 
It's primary purpose is to get things started, delegate control (inversion of control) and 
then wrap things up when/if control is passed back to it.
Typically this file will only need to be modified to add hooks onto routes.
</p>
<p>Things get started by loading the <code>bootstrap.inc.php</code> file, which determines how to process the current run request, web or command line.
When running under a web request, the <code>bootstrap.web.php</code> file is loaded, 
which loads web specific settings and configurations like <code>config.web.php</code>. In addition, the core <code>Content.class.php</code>
(View Controller) file is loaded, the URL is parsed and route to run is determined, then the session is started
and control is passed back to <code>index.php</code>.
</p>
<p>Now that the index knows what route to run, it checks the route parameters and passes control to the route. 
Any route can modify the current run settings (inversion of control) and/or set other routes to run (route chaining) 
before passing control back to the index or exiting (i.e. ajax request).
Once all the routes have been run, the index puts together all the content registered with the View Controller and outputs the finished page.
By default, all the routes that were run are profiled and can be inspected for run duration.
</p>

