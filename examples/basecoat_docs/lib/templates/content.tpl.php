<div class="section_title">
Content, Templates &amp; Layouts
</div>
<p>
Content management and output is based around layouts and templates.
The built in templating system uses standard PHP for building pages and outputting the result.
The core <code>content.class.php</code> provides a "namespaced" building environment
to create output snippets and merge them into a full page. 
<br />
Page layouts and snippets should all be stored in the /templates directory.
By default, this directory contains subdirectories for /layouts, /static pages, and /common output snippets.
Other subdirectories can be created for better organization.
<br />
The <code>bootstrap.web.php</code> automatically creates the "master" instance on the content class
where all output eventually gets merge into. This instance is stored in the static variable
<code>Content::$page</code>, which is part of the content class.
Each route creates it's own instance of the content class for loading and processing templates.
Once the route has completed processing, the content is merge into the master instance
for final output by the Front Controller.
</p>

<div class="subsection_title">
Layouts
</div>
<p>
Layouts are the base page structure that snippets are merged into.
A typical site will have very few layout files since they contain only content common across multiple pages
like headers, footers, core css/javascript, meta tags, etc.
</p>
<p>
Layouts are defined in <code>config.web.php</code>, just like routes.
The list of defined layouts is stored in the <code>$_LAYOUTS_</code> array, and a default route is defined (<code>$_LAYOUTS_['default']</code>).
If no layout is defined for the requested route, the default layout is used for output.
</p>

<div class="subsection_title">
Templates
</div>
<p>
Template are processed by the route file and are eventually merged into the defined layout for the route.
By default, it is assumed a template file contains only content to be placed in the body of the page.
However, content can be "wrapped" in "namespaces" and merged into the matching namespaces in the layout file.
</p>
<p>
<strong>@namespace&gt;</strong> tags
<br />
Namespaces in a template file are in the format of @namespace>, where "namespace" can be any text string.
The namespace tag must always be on it's own line.
Only the start of a namespace is required to be declared, the closing "tag" is assumed to be the next namespace declaration or the end of the file.
Namespace tags should have a matching output equivalent in the layout template being used.
This allows building up content in an out of order execution that results in grouped output.
For example, being able to declare multiple javascript blocks that get combined in the footer (end of page) of the layout file.
<pre>
example.tpl.php

@header&gt;
&lt;meta name="description" content="Example of information to be added to the header"&gt;

@script&gt;
This will be added to the javascript "script" block

@body&gt;
This will be added to the "body" of the layout

@script&gt;
More stuff to be added to the javascript "script" block
</pre>
</p>

<div class="subsection_title">
Content Class
</div>
<p>
The <code>content.class.php</code> is the key to managing content that is merged with templates and layouts.
Each route should create it's own instance of the content class for "registering" data to be used in the templates and layouts.
Templates and layouts are loaded into the instance, creating namespaced environment to prevent any conflicts.
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


</p>
<br />
<pre>
// Route Code example

$content	= new Content();
$content->add('variable_name', 'Variable Content' );

// Load the template file defined for the current route
$content->processTemplate($_ROUTES_[Core::$current_route]['template']);

// Merge loaded template into the Page
$content->addToPage();
unset($content);
</pre>

