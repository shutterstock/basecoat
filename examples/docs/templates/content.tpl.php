<h2>Overview</h2>
<p>
The content module that comes with {{:sitename}} is based around template modules and layouts that these modules are merged into. Templates and layouts use standard PHP as the templating language. Each file is "included" within a view controller so that any embedded PHP is executed, but within the view controller's context. Each template can have sections so that a single template can distribute content into multiple sections in the parent template and/or layout. This allows a single template to add content to the header, body, footer and any other section, or to be output as json, xml or any other format. Output buffering is used throughout, so there is no output until your code calls for output.
</p>

<h2>
View Controller
</h2>
<p>
A View Controller has no concept of a page, template or layout. It simply manages the "view" for specific content that is being created. This could be a module, a section or an entire page. Views are designed to be merged into other views in order to create a modular system of content creation. A master View instance is automatically created by {{:sitename}} and can be referenced using <code>$basecoat->view</code>. The master View is typically what would be used to merge the content blocks with a layout for final output. Typical view processing is as follows:
<ol>
<li>Create a view instance
<pre>$content = new \Basecoat\View();</pre>
</li>
<li>Register data to be used by the view
<pre>$content->add('variable_name', 'data');</pre>
</li>
<li>Process a template with the registered data
<pre>$content->processTemplate('path/to/template/file.php');</pre>
</li>
<li>Merge with another view, or output content with specified layout
<pre>$content->addToView($basecoat->view);
echo $basecoat->render();</pre>
</li>
</ol>
</p>

<h2>Section Tags</h2>
<p>Section Tags are {{:sitename}}'s way of allowing content to be delimited so that it can be parsed and merged with content with the same section tags from other views, or rendered in a layout. The content in section tags are stored in variables of same name in the View controller. There is only one pre-configured tag, which is used for assigning content that does not have a section tag specified. By default, this tag is the "body" tag, but can be changed to any desired name through the <code>$default_namespace</code> class variable.<br />
<b>@sectiontag&gt;</b><br />
Tags in a template file are in the format of @tagname>, where "tagname" can be any text string. The tag must always be on it's own line, begin with @, end with &gt; and contain no spaces. Only the start of a section is required to be declared with a tag. The closing "tag" for a section is assumed to be the next tag declaration or the end of the file.
</p>
<p>
An example of a typical template file with tags:
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

<h2>Template Variables</h2>
<p>Template files are processed in the context of the View controller, outside of the global namespace. Data can be loaded into the View controller and made accessible in the template. Data is loaded in a simple name/value structure, whereby the "name" will be the name of the variable. Bulk loading using associative arrays is also supported. Since the templating system uses plain PHP, it is possible to reference data in the global namespace.
</p>
<p>
Example of loading data for use by a template:
<pre>
$content	= new Content();
$content->add('var1', 'Variable Content' );
$content->add('var2', 'More Content' );
$content->add('var3', array('supports','any','datatype') );
$widgets	= array(
	'widget1'	=> 'Today is '.date('l, F n, Y'),
	'widget2'	=> 'It is '.date('z').' day of the year'
);
$content->multiadd( $widgets );
</pre>

Data that has been loaded into the View can then be referenced in the template:
<pre>
@body_head&gt;
<?php
echo str_replace(array('<','>'), array('&lt;', '&gt;'), <<<TEXT
This should be put in the Body Head. <?php echo \$this->widget1;?>
TEXT
);
?>


@body&gt;
<?php
echo str_replace(array('<','>'), array('&lt;', '&gt;'), <<<TEXT
Some content output with a variable <?php echo \$this->var1;?>.
But there is <?php echo \$this->var2;?> to be shown.
A list of words:
<?php
foreach(\$this->var3 as \$word) {
	echo \$word.'<br />';
}
TEXT
);
?>
</pre>

<h3>Data Tags</h3>
<h4>But that's a lot of typing just to output a variable...</h4>
<p>
{{:sitename}} supports the concept of "data tags" to make outputting the value of variables a bit easier. This is a very simple search and replace system, not part of a templating markup language. The default data tag structure is: <code>{{:var_name}}</code>. A data tag in that structure will output the contents of <code>$this-&gt;var_name</code>.<br />
Data tags are replaced after the template file has been loaded and the PHP code embedded in it has been run. Only scalar variables are supported.<br />
<h4>Global Data Tags</h4>
By default, data tags that were not replaced by a variable persist for future processing. Values for global data tags can added to the default view for processing. For example, a {{:page_title}} data tag can be added to the default view. Since the default view is not processed until the very end, this value can then be modified by routes.
<pre>
// Register a value for global page title prefix
$basecoat->view->add('page_title', 'Basecoat');
...

// Append text to the page title variable
$basecoat->view->add('page_title', ': Content', true);
</pre>
</p>


<h3>Layouts</h3>
<p>
Layouts are the final round of template output. A Layout is generally where all the content generated by previous views is merged into a template for output. Any section tags that were declared and used in previous views are available for output as variables of the same name. A typical website will usually have only a few layouts, sometimes only one. A typical HTML layout file will have the standard sections of an HTML document like header, title, style, script, etc.
</p>
An example of a layout file:
<pre>
<?php
echo str_replace(array('<','>'), array('&lt;', '&gt;'), <<<TEXT
<html lang="<?php echo \$this->lang; ?>">
<head>
<title><?php echo \$this->title; ?></title>
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
<?php echo \$this->body; ?>
<?php echo \$this->footer; ?>

</body>
</html>
TEXT
);?>
</pre>

<p>
Layouts do not have to be just HTML. Since they are just PHP and text, they can generate any content format desired (i.e. json, xml). A View Controller only loads and processes the template/layout file it is directed to, so any valid PHP file can be passed for processing. There are functions available to access all the data that has been loaded already. Below is an example of a layout that would output JSON instead of HTML. For example, if processing an AJAX call, the same routes can be run and content templates loaded, but the layout can be changed to alter the output format.
</p>
<pre>
&lt;?php
// Get content loaded and merged by previous views
$content_blocks	= $this->getData();
// Output as JSON
exit( json_encode($content_blocks) );
</pre>

The simplest way to implement this would be to configure a "json" route that switches the currently declared layout for the "json" layout. The "json" route can then be appended to any URL to output JSON. This is possible when each route is configured to call <code>runNext()</code> when done processing.<br />

<h3>Merging Views</h3>
<p>
Any view can be merged with another view to build up output in a modular process. The content blocks of one view become the data input of another view. 
<pre>
$view1 = new \Basecoat\View();
...
$view1->processTemplate('template_file1.php');

$view2 = new \Basecoat\View();
// Merge view1 into view2
$view1->addToView($view2);
...
$view2->processTemplate('template_file2.php');

// Merge into main view
$view2->addToView($basecoat->view);

</pre>

</p>

<br />
<h3>
Static Content
</h3>
<p>
Most web sites have pages that contain no dynamic content and are plain html or text. A special "static" route exists in {{:sitename}} that can be configured to process static template files. This route file will process all static templates. The simplest implementation of this is:
<pre>
$content = new \Basecoat\View();

// Load template file
$tpl = file_get_contents($basecoat->view->templates_path . $basecoat->routing->current['template']);
// Parse section tags
$content->parseBlocks($tpl);

// Add route content to page
$content->addToView($basecoat->view);
</pre>

</p>

<h3>Class Variables</h3>
<p><strong>$default_namespace</strong> (string)
Default section tag (body) to place content in if none is specified.
</p>
<p><strong>$block_tag_regex</strong> (string)
Regular expression for parsing section tags.
</p>
<p><strong>$enable_data_tags</strong> (boolean)
Enable/disable search and replace of data tags.
</p>
<p><strong>$data_tags_delimiters</strong> (array)
Delimiters of data tags.
</p>


<h3>
Functions
</h3>
<p>
<strong>setLayouts($layouts, $default=null)</strong><br />
Load the list of layouts. An associative array where the key is the layout name and the value is the relative path to the layout file from the templates directory. Optionally pass name of default layout.
</p>
<p>
<strong>setLayout($layout_name)</strong><br />
Set the layout to use for output. Name must match one set with setLayouts().
</p>
<p>
<strong>getLayout($layout_name=null)</strong><br />
Get the relative path to a layout file. Default is layout set with setLayout().
</p>
<p>
<strong>setTemplatesPath($path)</strong><br />
Path to templates directory. Use as a prefix when referencing templates and layouts.
</p>

<p>
<strong>add($name, $content, $append=true)</strong>
<br />
Register $content under the variable $name. $content can be any data type (scalar, arrays, objects, etc). If the same variable $name is used, it is assumed to be a scalar variable and $content is appended to the current variable.
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
<strong>replaceDataTags($tpl)</strong>
<br />
Search and replace data tags in the passed template text.
</p>

<p>
<strong>stripDataTags(&$tpl)</strong>
Strip any data tags in the passed template text.
<br />

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
Returns array of all the data variables that currently exist in the instance. Content blocks parsed from a template are converted to  "data" input when added to another view.
</p>

<p>
<strong>getBlocks()</strong>
<br />
Returns array of all the content blocks that were parsed from the processing of a template. 
</p>

<p>
<strong>addToView($view)</strong>
<br />
Converts the content blocks parsed from <code>processTemplate()</code> to input data of the specified view.
</p>

<br />
<br />
<br />
<br />
<br />
<br />
