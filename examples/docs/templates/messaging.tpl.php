<br />
<h2>
Messaging
</h2>
<p>
{{:sitename}} comes with a basic user message display system that includes 3 display types: info, warn and error.
The <code>Messages</code> class is part of the <code>Content</code> class. 
An instance of the messages class is automatically created when {{:sitename}} is instantiated and can be referenced like <code>$basecoat->messages</code>.
{{:sitename}} comes with a message template that is used by default and is in templates/messages.tpl.php in the {{:sitename}} template directory.
</p>
<p>
Messages are stored in the session so that they are not lost if there is a page redirect.
Any messages that have been registered will automatically be displayed on the next normal full page render via the <code>$basecoat->processRequest()</code> method.
Messages can also be output on demand by calling the <code>display()</code> function.
Any layout set to display messages should have an output line <code>&lt;?php echo $this-&gt;messages; ?&gt;</code>.
If this code is not present anywhere messages will not be displayed and will be lost.
</p>
<p>
Registering a message for display is simply a matter of calling the appropriate function for the type of message to output.
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

