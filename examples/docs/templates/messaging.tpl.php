<br />
<h2>
Messaging
</h2>
<p>
{{:sitename}} comes with a basic user message display system that includes 3 display types: info, warn and error. The <code>Messages</code> class uses the <code>Content</code> class when processing messages for display. An instance of the messages class is automatically created when {{:sitename}} is instantiated and can be referenced using <code>$basecoat->messages</code>.
{{:sitename}} comes with a message template that is used by default and is in templates/messages.tpl.php in the {{:sitename}} template directory.
</p>
<p>
Messages are stored in the session so that they are not lost if there is a page redirect or error. Any messages that have been registered will automatically be displayed on the next normal full page render via the <code>$basecoat->processRequest()</code> method.
Messages can also be output on demand by calling the <code>display()</code> function.
Any layout set to display messages should have an output line <code>&lt;?php echo $this-&gt;messages; ?&gt;</code>.
If this code is not present anywhere, messages will not be displayed and will be lost.
</p>
<p>
Registering a message for display is done by calling the appropriate function for the type of message to output.
Each function can be called multiple times. Similar messages will automatically be aggregated when displayed.
</p>

<p>
<strong>setTemplate($tpl_file)</strong>
<br />
Set the template file, relative to template directory, for the messaging class to use. 
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
<strong>display($view, $clear=true)</strong>
<br />
Process the current set of messages and add to the passed View controller. Messages are cleared by default.
This function is automatically called by {{:sitename}} before generating the final output.
</p>
<p>
<strong>clear($msg_type=null)</strong>
<br />
Clear all messages currently set, or optionally pass a message type (info, warn, error) to clear.
</p>

