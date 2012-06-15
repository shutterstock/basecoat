<div class="section_title">
Messaging
</div>
<p>
<?php echo Content::$page->sitename;?> comes with a basic message display system that includes 3 display types: info, warn and error.
Any messages that have been registered will automatically be displayed on the next full page output.
Messages are stored in the session so that they are not lost if there is a page redirect.
<br />
The Messages class is part of the Content class since the message class registers the instance of itself in the <code>Content::$messages</code> variable.
</p>

<p>
<strong>setTemplate()</strong>
<br />
Set the template for the messaging class to use. 
The default template is /templates/common/messages.tpl.php.
</p>

<p>
<strong>info($message)</strong>
<br />
Add an informational message. Convenience function for add();
</p>
<p>
<strong>warn($message)</strong>
<br />
Add an warning/alert message. Convenience function for add();
</p>
<p>
<strong>error($message)</strong>
<br />
Add an error message. Convenience function for add();
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

