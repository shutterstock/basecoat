<h2>Overview</h2>
<p>
The content module that comes with {{:sitename}} is based around template modules and layouts that these modules are merged into. Templates and layouts use standard PHP as the templating language. Each file is "included" within a view controller so that any embedded PHP is executed, but within the view controller's context. Each template can have sections so that a single template can distribute content into multiple sections in the parent template and/or layout. This allows a single template to add content to the header, body, footer and any other section, or to be output as json, xml or any other format. Output buffering is used throughout, so there is no output until specified.
</p>

<h2>
View Controller
</h2>
<p>
A View Controller has no concept of a page, template or layout. It simply manages the "view" for specific content that is being created. This could be a module, a section or an entire page. Views are designed to be merged into other views in order to create a modular system of content creation. A master View instance is automatically created by {{:sitename}} and can be referenced using <code>$basecoat->view</code>. The master View is typically what would be used to merge the content blocks with a page layout for final output. Typical view processing is as follows:
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

<h4>But that's a lot of typing to output a variable... Data Tags</h4>
<p>
{{:sitename}} supports the concept of "data tags" to make outputting the value of variables a bit easier. This is a very simple search and replace system, not part of a templating markup language. The default data tag structure is: {{:var_name}}. A data tag in that structure will output the contents of $this-&gt;var_name.<br />
Data tags are are replaced after the template file has been loaded and the PHP code embedded in it has been run. Only scalar variables are supported.
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
Layouts do not have to be just HTML. Since they are just PHP and text, they generate any content desired (i.e. json, xml). A View Controller only loads and processes the template/layout file it is directed to, so any valid PHP file can be passed for processing. There are functions available to access all the data that has been loaded already. Below is an example of a layout that would output JSON instead of HTML. For example, if processing an AJAX call, the same routes can be run and content templates loaded, but the layout can be changed to alter the output format.
</p>
<pre>
&lt;?php
// Get content loaded by previous views
$content_blocks	= $this->getData();
// Output as JSON
exit( json_encode($content_blocks) );
</pre>

<h3>Merging Views</h3>
<p>
Any view can be merged with another view to build up output in a modular process. The content blocks of one view become the data input of another view. 
</p>

<br />
<h3>
Static Content
</h3>
<p>
Most web sites have pages that contain no dynamic content and are plain html or text. A special "static" route exists in {{:sitename}} that can be configured to process static template files.
</p>

<h3>
Functions and Methods
</h3>
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
