<div class="section_title">
Messaging
</div>
<p>
<?php echo Content::$page->sitename;?> comes with a basic user message display system that includes 3 display types: info, warn and error.
The Messages class is part of the Content class and registers the instance of itself in the <code>Content::$messages</code> variable.
<?php echo Content::$page->sitename;?> comes with a message template that is used by default and is in templates/messages.tpl.php.
</p>
<p>
Messages are stored in the session so that they are not lost if there is a page redirect.
Any messages that have been registered will automatically be displayed on the next full page output.
Messages can also be output on demand by calling the <code>display()</code> function.
Messages set for output are stored in the content variable <code>$messages</code> and any layout set to display messages should have an output line <code>&lt;?php echo $this-&gt;messages; ?&gt;</code>.
</p>
<p>
Register a message for display is simply a matter of calling the appropriate function for the type of message to output.
Each function can be called multiple times. Similar messages will automatically be aggregated when displayed.
</p>

<p>
<strong>setTemplate($tpl_file)</strong>
<br />
Set the template for the messaging class to use. 
</p>

<p>
<strong>info($message)</strong>
<br />
Add an informational message.
</p>
<p>
<strong>warn($message)</strong>
<br />
Add an warning/alert message.
</p>
<p>
<strong>error($message)</strong>
<br />
Add an error message.
</p>
<p>
<strong>get($ms_type=null)</strong>
<br />
Retrieve the list of messages currently set. Optionally retrieve messages of specific type.
</p>
<p>
<strong>display()</strong>
<br />
Process the current set of messages and add to the page output.
This function is automatically called by the Front Controller before generating the final output page.
</p>
<p>
<strong>clear($msg_type=null)</strong>
<br />
Clear all messages currently set, or optionally pass a message type (info, warn, error) to clear.
</p>

