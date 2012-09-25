<h2>Overview</h2>
Basecoat is designed to cover the basics: MVC, Front Controller, Templating, and Database abstraction. It is designed to be "included" in your code and using it as a full framework is optional. This allows one to build parts of a website in the framework and migrate over time. It does not enforce a coding style, naming convention, or have many dependencies. Basecoat is designed to be a centralized core code base that websites are built on. The same core code base can be loaded by many different web sites and/or applications. The configurations that dictate the framework's behavior are part of the website code, not the framework. Pulling in updates won't overwrite any of your own code. 

The entire framework is less than 10 files, so it is very easy to learn and start using. You should be able start creating web pages within a few hours (possibly 1 hour) of downloading the framework. Performance is quite good, handling hundreds of pages per second, even without accelerators like APC. It requires no special setup or configuration, not even mod_rewrite. It can be configured to use parameter based URLs or if you want "pretty urls", mod_rewrite or something similar.

<h2>Documentation</h2>
To get started, simply download the framework, including the examples. Place the whole framework in a web accessible directory and point your browser at basecoat/examples/docs/www/. The documentation is written using the framework, so you can view the code and see how it is working while you read through the documentation. Typically you would only have the www directory web accessible, but that is up to you.

<h2>Quickstart</h2>
There is also a quickstart directory in the example directory that can be used as a template for creating new websites. Simply make a copy of the quickstart directory and modify it to fit the needs to your new website.
<ol>
<li>Make a copy of the quickstart directory and put it wherever you want your website</li>
<li>Edit the index.php and change the value of the $basecoat_init_file variable to the location of the basecoat init.php file.</li>
<li>Edit the config.web.php file and create the routes for your site by editing the Config::$routes array.</li>
</ol>
Load your new site by pointing your browser at the www directory of your new site.