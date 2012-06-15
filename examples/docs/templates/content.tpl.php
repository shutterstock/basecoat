<h2>
Content, Templates &amp; Layouts
</h2>
<p>
Content management and output is based around layouts and templates.
The built-in templating system uses standard PHP for building pages and outputting the result.
Pages can be divided into as many sections on needed, and content "inserted" into each section, allowing modular, out of order building of a page.
</p>
<p>
The core <code>content.class.php</code> provides a "namespaced" building environment to create output snippets and merge them into a full page. 
There is also support for message type of info, warn, and error for output.
The <code>init.php</code> file automatically loads and creates the "master" instance of the content class where all output eventually gets merged. 
This instance is stored in the static variable <code>Content::$page</code>, which is part of the content class.
Each route creates it's own instance of the content class for loading and processing templates.
Once the route has completed processing, the content is merge into the master instance for final output by the Front Controller.
</p>

<br />
<h3>
Layouts
</h3>
<p>
Layouts are the base page structure that snippets are merged into.
A typical site will have very few layout files since they contain only content common across multiple pages
like headers, footers, core css/javascript, meta tags, etc.
</p>
<p>
Layouts are named/defined in <code>config.web.php</code>, just like routes.
The list of defined layouts is stored in the <code>Config::$layouts</code> array, and a default route is defined (<code>Config::$layouts['default']</code>).
If no layout is defined for the requested route, the default layout is used for output.
</p>
<p>
A typical layout file will have the standard sections of an HTML document, plus any custom sections needed for the website.
The custom sections can have any valid PHP variable names.
The content class will handle initializing and managing the content sections defined.
All layouts are loaded within the context of a content class instance, so variables should be referenced with <code>$this->variable</code>.
</p>
An example of a layout file:
<pre>
<?php
echo str_replace(array('<','>'), array('&lt;', '&gt;'), <<<TEXT
<html lang="<?php echo \$this->lang; ?>">
<head>
<title><?php echo $this->title; ?></title>
<?php echo \$this->head; ?>;

<style type="text/css">
h1 { font-weight:bold;background-color:#f0f0f0; }
<?php echo \$this->css; ?>
</style>

<script type="text/javascript">
<?php echo \$this->script; ?>
</script>
</head>
<body>
<?php echo \$this->page_header; ?>
<?php echo \$this->messages; ?>

</body>
</html>
TEXT
);?>
</pre>

<br />
<h3>
Templates
</h3>
<p>
Templates are loaded by the route file, processed by the content class, and are eventually merged into the defined layout for the page.
By default, it is assumed a template file contains only content to be placed in the body of the page.
However, content in templates can have defined "namespaces" to be merged into the corresponding sections in the layout file.
If no namespaces are defined in the template, the content is merge into the "body" of the layout.
</p>
<p>
<strong>@namespaces&gt;</strong>
<p>
Namespaces in a template file are in the format of @namespace>, where "namespace" can be any text string.
The namespace tag must always be on it's own line, begin with @, end with &gt; and contain no spaces.
Only the start of a namespace is required to be declared, the closing "tag" is assumed to be the next namespace declaration or the end of the file.
Namespace tags should have a matching output variable in the layout template being used.
This allows building up content in an out of order method that results in grouped output.
For example, being able to declare multiple javascript blocks that get combined in the header or footer (end of page) of the layout file.
</p>
An example of a typical template file:
<pre>
@header&gt;
&lt;meta name="description" content="Example of information to be added to the header"&gt;

@script&gt;
This will be added to the javascript "script" block

@body&gt;
This will be added to the "body" of the layout.
Example of a variable inside the a template.
This variable's value is: &lt;?php echo $this->variable_name;?&gt;

@widget1&gt;
Content to be placed in the widget1 area
&lt;?php echo $this->widget1;?&gt;

@script&gt;
More stuff to be added to the javascript "script" block
</pre>
</p>

<p>
An example of what a typical route file would contain:
<pre>
$content	= new Content();
$content->add('variable_name', 'Variable Content' );
$widgets	= array(
	'widget1'	=> 'Today is '.date('l, F n, Y'),
	'widget2'	=> 'It is '.date('z').' day of the year'
);
$content->multiadd( $widgets );

// Load the template file defined for the current route
$content->processTemplate($_ROUTES_[Core::$current_route]['template']);

// Merge loaded template into the Page
$content->addToPage();
unset($content);
</pre>
</p>

<br />
<h3>
Content Class
</h3>
<p>
The <code>content.class.php</code> is the key to managing content that is merged with templates and layouts.
Each route should create it's own instance of the content class for "registering" data to be used in the templates and layouts.
Templates and layouts are loaded into the instance, creating a closed environment to prevent any naming conflicts.
A master content instance is always available in the <code>Content::$page</code> variable and can be referenced from anywhere.
Any data that needs to be globally available can be registered with the core instance.
</p>

<p>
<strong>add($name, $content)</strong>
<br />
Register $content under the variable $name. $content can be any data type (scalar, arrays, objects, etc).
</p>

<p>
<strong>multiadd($name_vals, $prefix=null)</strong>
<br />
A convenience function to register multiple content items using an associative array. 
Keys are used for the variable $name, values are assigned to the variables.
</p>

<p>
<strong>addBlock($block_name, $content)</strong>
<br />
Add a raw block of content under the $block_name namespace.
</p>

<p>
<strong>processTemplate($tpl, $parse=true )</strong>
<br />
Load and process a file. By default, template blocks contained in the file are parsed and added to the content list.
If <code>$parse</code> is false, then the result of the processed file is returned.
</p>

<p>
<strong>parseBlocks($tpl)</strong>
<br />
Parses the template text into the specified namespaces.
If no namespaces are found, the content is add to the default namespace (i.e. body).
</p>

<p>
<strong>clear()</strong>
<br />
Clear any content that is currently stored in the instance.
</p>

<p>
<strong>getData()</strong>
<br />
Returns array of all the data variables that currently exist in the instance.
</p>

<p>
<strong>getBlocks()</strong>
<br />
Returns array of all the content blocks that currently exist in the instance.
</p>

<p>
<strong>addToPage()</strong>
<br />
Merges the namespaced content blocks into the <code>Content::$page</code> instance.
</p>

<br />
<br />
<br />
<br />
<br />
<br />
