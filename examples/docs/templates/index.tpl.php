<h3>
Why Another PHP Framework?
</h3>
<ol>
<li><b>No Commitment.</b> Most of the monolithic frameworks, and some of the lightweight ones, present an all or nothing scenario. You must commit to their naming and programming conventions to get everything working optimally. {{:sitename}} allows you to migrate to the framework without using "magic". Migrate a single page, or even part of a part. {{:sitename}} can even work within another framework.
</li>
<li><b>No Magic.</b> At it's core, {{:sitename}} uses standard PHP as it's programming "convention". Any code written to run under {{:sitename}} can run without the framework with minimal modifications. This also means legacy code could run under the {{:sitename}} framework with minimal changes.
</li>
<li><b>Easy to use, high performance.</b> If you know PHP, you know how to use {{:sitename}}. You should never struggle to figure out how to do something in {{:sitename}} that you already know how to do in PHP. By avoiding complex abstraction layers, {{:sitename}} performs well and scales even without accelerators, caching, and complex configurations. Loading and initializing the {{:sitename}} framework requires about 5 files, including your index/configuration file.
</li>
<li><b>Modular, Independent.</b> {{:sitename}}'s routing, content and database modules are standalone. They can be use outside the framework, or replaced inside the framework by a third party module. The content/view module is also focused on content blocks/modules, not entire pages. A page is just another module that can contain other modules, with no dependencies.
</li>
<li><b>Master/Slave Database Abstraction Layer</b> The PDO database module that comes with {{:sitename}} is designed to handle all of the basic database functions: connection management, escaping, error recovery, and profiling. Connections are established on demand when you need them. Master or slave connection is used automatically depending on the method being used.<br />
<i>Note: currently designed to work only with MySQL</i> 
<li><b>Centralized Installation.</b> {{:sitename}} is designed to be loaded and initialized by your code, like a typical class file. This design allows a single install to be used by multiple websites.
</li>
</ol>

<h3>
Introduction
</h3>
<p>
{{:sitename}} is a flexible PHP framework based on modern design patterns like Front Controller, MVC and Inversion of Control. It provides the basic tools to handle what every website requires: routing, templating, database abstraction. {{:sitename}} is purposely light on modules. There are many robust modules and libraries public available for authentication, validation, payment processing, etc. 
</p>
<p>
For convenience, an implementation of this documentation using the {{:sitename}} framework is available in the examples directory.
The sample implementation can be used to understand how the framework works from the code level.
</p>
<p>
{{:sitename}} is first and foremost designed to be quick and easy to start using, not a framework you start learning how to use. 
In less than an hour, you should be able to start creating a website using the framework. While abstracting some tasks, 
it is a minimalist framework that still allows "raw" PHP to be used for optimal performance and flexibility.
</p>
<p>
<ul>
<li>Templating system based on native PHP, but systems like Twig or Smarty can be used instead.</li>
<li>Support for parameter based URLs, or SEO friendly "pretty" URLs. Automatic redirects for secure pages.</li>
<li>Hooks for running custom code at various execution points.</li>
<li>Comes with an optional database class that is based on PDO, uses data bindings, and supports master/slave configurations.</li>
<li>Support for command line scripts</li>
</ul>
</p>

<p>
Almost all website now use "pretty" URLs. This requires Apache mod_rewrite rules, .htaccess files, or other special server configuration. Since not all hosts allow the required configuration permissions, {{:sitename}} supports both "pretty" URLs and parameter based URLs by simply changing the value of a configuration variable.
</p><p>
Since most web sites also require backend processes to run periodically, {{:sitename}} also supports running scripts from the command line. It does not assume it is always processing a web page.
</p>

<h3>Quick Start</h3>
<h4>Load, Configure and Process Request</h4>
<pre>
// Load
require_once('/basecoat/basecoat.php');
$basecoat = new \Basecoat\Basecoat();

// Configure Layouts, Templates
$basecoat->view->setLayouts(
	array(
		'default' => 'layouts/path/common.php'
	)
);
// Set layout to use
$basecoat->view->setLayout('default');
// Set path to templates directory
$basecoat->view->setTemplatesPath('templates/path');

// Define Routes
$basecoat->routing->setRoutes(
	array(
		'/'	=> array(
			'file' => 'routes/path/index.php',
			'template' => 'index.tpl.php',
		),
		// High performance route
		'hello' => array(
			'function'	=> function() {
			   exit('Hello World!');
			}
		)
	)
);

// Process Request
$output	= $basecoat->processRequest();
echo $output;
</pre>

