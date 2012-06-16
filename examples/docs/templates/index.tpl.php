<h2 class="page-header">
Introduction
</h2>
<p>
<?php echo Content::$page->sitename;?> is a flexible PHP framework based on modern design patterns like Front Controller, MVC and Inversion of Control.
</p><p>
<?php echo Content::$page->sitename;?> is first and foremost designed to be quick and easy to start using, not a framework you start learning how to use. 
Within an hour or two, you should be able create your first pages using the framework.
By focusing on simplicity the framework is fast and scalable. While abstracting some tasks, 
it is a minimalist framework that still allows "raw" PHP to be used for optimal performance and flexibility.
</p>
<p>
<ul>
<li>Templating system based on native PHP, but can also be modified to support systems like Twig and Smarty.</li>
<li>Support for parameter based URLs, or SEO friendly "pretty" URLs</li>
<li>Comes with an optional database class that is based on PDO, uses data bindings, and supports master/slave configurations.</li>
<li>Support for command line scripts</li>
</ul>
</p>

<p>
Almost all website now use "pretty" URLs. This requires Apache mod_rewrite rules, .htaccess files, or other special server configuration.
Since not all hosts allow the required configuration permissions, <?php echo Content::$page->sitename;?> supports both "pretty" URLs and parameter based URLs by simply
changing the value of a configuration variable.
</p><p>
Since most web sites also require backend processes to run periodically, <?php echo Content::$page->sitename;?> also supports running scripts from the command line.
It automatically detects whether it is web or command line mode and will adjust behavior accordingly.
</p>

<p>
For convenience, an implementation of this documentation using the <?php echo Content::$page->sitename;?> framework is available.
The sample implementation can be used to understand how the framework works from the code level.
</p>

<h2 class="page-header">
Setup Overview
</h2>
<br />
<h3>
An Include Framework
</h3>
<p>
There are many very good PHP frameworks available for creating new websites quickly.
However, there aren't many frameworks that allow migration of an existing website to a new framework.
<?php echo Content::$page->sitename;?> is an "include" framework. You do not need to build your website "inside" the framework, 
the framework can be included inside your website. This setup also allows multiple websites to be built around a single, 
common instance of the framework. It also allows migration of an existing website to the <?php echo Content::$page->sitename;?> framework.
</p>

<br />
<h3>
URL Structure
</h3>
<p>
Most web sites use mod_rewrite or something similar to create friendly/pretty URLs, instead of using URL parameters.
Since some websites may be hosted in a shared hosting environment, or have complex development environment where rewrite rule aren't available,
 where mod_rewrite may not be possible, the framework can be used in either mode. 
Switching modes does not require extensive code changes, just a configuration toglle, so it's possible to develop using URLs parameters 
and switch to using mod_rewrite in production.
</p><p>
Simply toggling the <code>Config::$use_pretty_urls</code> variable in <code>config.web.php</code> will switch
between "pretty" URLs and parameter based URLs. This switch can be automated by checking the environment variable.

</p>

<br />
<h3>
File/Folder Structure
</h3>
<p><?php echo Content::$page->sitename;?> is based on the Front Controller design pattern 
and thus everything is processed through the <code>init.php</code> file.
That is the only file that is required in order to load the framework.
The init file initializes the framework, detects whether it is in web or command line mode, and loads your configuration files.
If in web mode, it will also initiate processing of the web request. 
</p><p>
The <code>init.php</code> file relies on configuration files you create in order to determine what routes need to be run.
There are 2 configuration files, <code>config.php</code> contains the common configuration for commad line and web, 
<code>config.web.php</code> contains web specific configuration settings. 
Both files should be contained in your sites directory, not in the framework directory.
</p><p>
<pre class="pre-scrollable">
/basecoat
	init.php
	router.php
	classes
		content.class.php
		core.class.php
		db.pdo.php
	
/your_site
	/www
		index.php
		/css
		/img
		/js
	/config
		config.php *
		config.web.php *
	/routes
	/templates
		/layouts
	/css
	/files
	/img
	/js
</pre>
</p>
<br />
<br />
<br />
