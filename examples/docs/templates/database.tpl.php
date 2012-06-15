@body>
<div class="section_title">
Database
</div>
<p>
<?php echo Content::$page->sitename;?> comes with a stand alone database class that uses PDO and is designed to use MySQL as the backend. 
While the database class is fairly powerful, it is not designed to be an ORM and does require knowledge of SQL to use.
The database class is designed to be a low level tool that will handle the most common tasks in a high scalability environment.
A place where ORMs often fail. The framework does not require this class and it is included for convenience. 
</p>
<p>
<strong>Features:</strong>
<ul>
<li>On-demand connection - multiple connections can be defined, but a connection is only established once a query is run</li>
<li>Connection Management - Every instance has a master and slave connection defined. But only one master connection is established and shared across all instances. By default, connections are singleton (1 connection per server) to prevent creating too many connections.</li>
<li>Master/Multiple Slave support - queries that modify data are automatically routed through the Master connection. All other queries default to running through the slave connection, but can be forced to run against the master. </li>
<li>Automatic Escaping - PDO data binding is used to prevent SQL injection.</li>
<li>Bulk Inserts - an insert call automatically detects if a bulk insert should be performed. 
Very large bulk inserts are automatically broken up, and multiple bulk inserts performed based on a max insert size setting to prevent slave/replication lag.</li>
<li>Auto-reconnect - a dropped connection will automatically be detected, a new connection established and the last failed query rerun.</li>
<li>Profiling - when profiling is enabled, all queries are timed and recorded. Number of connections established is also available.</li>
</ul>

</p>


<div class="section_title">
Loading and Configuration
</div>
<p>
The class requires that it be configured for use prior to creating instances of the class. 
Each instance of the class will have 2 connection handles, one to the database instance specified on instantiation (i.e. slave) and one to the master. 
There is a single connection to the master database shared across all instances of the database class, and thus needs to be specified when configuring. 
Profiling also aggregates across all instances.
</p>
<p>
The <code>setServerConfig()</code> function must be called to load the database configuration before creating any instances. 
This function accepts an array, where each item contains parameters for connecting to each database server. 
An optional "label" parameter can be included with each configuration which will be used to "name" the connection. 
The <code>setServerConfig()</code> function accepts a second parameter, which specifies which configuration record is the master.

<pre>
$servers = array(
    0 => array('host' => HostName, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Master'),
    1 => array('host' => HostName, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 1'),
    2 => array('host' => HostName, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 2'),
    3 => array('host' => HostName, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 3'),
    ...
    );
// Set configuration and specify 0 as the master connection
DB::setServerConfig($servers, 0);
</pre>

Finer control over connections can be specified with an additional 'attr' parameter option in the <code>$servers</code> list. 
The 'attr' parameter will accept any valid PDO configuration options. 
For example, by default the 'attr' parameter specifies using buffered queries when creating a connection:

<pre>
array(
	'host' => IpAddress, 
	'username' => UserName, 
	'db' => DBName, 
	'password' => Password, 
	'attr'=>array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true)
)
</pre>

</p>

<div class="subsection_title">
Instantiation
</div>
<p>
To create an instance of the database class, call the <code>getServerInstance()</code> function. 
Specify which connection setting to use by passing the "key" of the item in the <code>$server</code> configuration list to use. 
If any instance already exists with that connection, the existing instance will be returned. 
You can force a new instance to be created by passing "true" as the second parameter.
<pre>
// Create an instance with a connection to server 2
$db2 = DB::getServerInstance(2);

// Force a new instance/connection to be established to the same server
$db2_1 = DB::getServerInstance(2, true);
</pre>
Note: A connection to the database is not made until a query is run that requires the connection, so there is little overhead in creating database instances.
</p>


<div class="section_title">
SELECT
</div>
<p>
SELECT queries are run against the default connection for the instance being used (i.e. slave connection). 
This behavior can be overridden by specifying and extra "use master" parameter when called, if real time data is needed. 
When SELECT queries are run, the data is not retrieved until a "fetch" function is run, or the "fetch all" parameter is used in the select function.
The class allows records to be retrieved in multiple ways. 
For example, fetching records and using a field value as the key for the array created.
</p>
<p>
The select function supports an optional "bindings" parameter which should be used so proper escaping is used when using external values. 
To use "bindings" insert a placeholder in your query that will be replaced by the appropriate binding values. 
While you can use "?" as placeholder, it is preferred that named placeholders be used to better document which values are being inserted where.
In addition, with named bindings, the order of the parameters is not relevant.
</p>
<p>
This function will always return a number as a result, which represents the number of records selected.
If the number returned is negative, that indicate an error occurred and the <code>errorMsg</code> and/or <code>errorCode</code> class variables should be checked to determine what error occurred.
</p>

<p>
<strong>select( $query, $bindings=null, $useMaster=false )</strong>
</p>
<pre>
// Using named data bindings
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = :value1 AND field2 >= :value2';
$bindings = array('value1'=>'some text', 'value2'=>4);
$qresult = Core::$db->select( $query, $bindings);

// Using ? for data binding
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = ? AND field2 >= ?';
$bindings = array('some text', 4);
$qresult = Core::$db->select( $query, $bindings);

// No data bindings
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = "some text" AND field2 >= 4';
$qresult = Core::$db->select( $query );
</pre>
</p>

<p>
<strong>selectOne( $query, $bindings=null, $useMaster=false )</strong>
<br />
Some times you know your query will only find one record, or only want to retrieve a single record. 
For cases like this, use the selectOne function. 
This works exactly like the <code>select</code> function, except that it retrieves the record if one is found.
This eliminates the need to do a <code>fetch</code> after a select. 

<pre>
$query = 'SELECT field1, field2, field2 FROM table1 WHERE field1 = :value1 LIMIT 1';
$data = Core::$db->selectOne( $query, array('value1'=>'X') );
</pre>
</p>

<div class="subsection_title">
Fetching Data
</div>
<p>
Once a select is run, the result set would need to be retrieved. 
This can be done using the <code>fetch</code> or <code>fetchAll</code> functions.
The <code>fetch</code> function retreives one record at a time and is used for "walking" through the result set.
The <code>fetchAll</code> function will retrieve the entire result set, the <code>fetch</code> function will retrieve the next record. 
<code>fetchAll</code> requires a variable to be passed where the result set will be stored.
</p>
<p>
<b>NOTE:</b> These functions are closely linked with the <code>select</code> function. 
Records must be fetched over the same connection that the SELECT was run.
If the <code>$useMaster</code> parameter was specified in the <code>select</code> function, it must also be specified when using the <code>fetch</code> or <code>fetchAll</code> functions.
</p>

<p>
<strong>fetch( $useMaster=false, $method=null )</strong>
<br />
The second optional parameter can be any valid PDO retrieval method.
The default method is PDO::FETCH_ASSOC.
<pre>
// fetch one record at a time
$qresult = Core::$db->select( $query, $bindings );
if ( $qresult>0 ) {
	$result_var	= array();
	while ( $row = Core::$db->fetch() ) {
		$result_var[] = $row;
	}
} else if ( $qresult<0 ) {
	echo 'Error: '.Core::$db->errorCode.'-'.Core::$db->errorMsg;
}
</pre>
</p>

<p>
<strong>fetchAll( &$resultVar, $keyField=null, $grouped=false, $userMaster=false, $method=null )</strong>
<br />
This function can do much more than just return and array of records.
You can specify which field value should be used for the array key.
You can also specify that the results be grouped by the specified field, with the array key being the grouping key.
This will return a hierchical array.
<pre>
$query	= 'SELECT article_id,comment FROM comments WHERE comment_date<=DATE_SUB(NOW(),INTERVAL 1 DAY)';
$qresult = Core::$db->select( $query );
$results_var = array();

// Fetch all records at once
Core::$db->fetchAll($results_var);
// Result
array(
	0=>array('article_id'=>'123', 'comment'=>'this is great!'),
	1=>array('article_id'=>'789', 'comment'=>'easier than doing it myself'),
	2=>array('article_id'=>'123', 'comment'=>'very convenient'),
	3=>array('article_id'=>'789', 'comment'=>'great time saver)'
);

// Group records by article_id
Core::$db->fetchAll($results_var, 'article_id', true);
// Result
array(
'123'=> array(
	0=>array('article_id'=>'123', 'comment'=>'this is great!'),
	1=>array('article_id'=>'123', 'comment'=>'very convenient'),
	),
'789'=> array(
	0=>array('article_id'=>'789', 'comment'=>'easier than doing it myself'),
	1=>array('article_id'=>'789', 'comment'=>'great time saver'),
	),
);
</pre>
</p>


<div class="section_title">
INSERT
</div>
<p>
Inserts couldn't be simpler, just pass a table name and an associative array.
The associative array is a set a field/value pairs that match the table fields.
All escaping and sanitizing is done automatically.
It will even detects a multi-dimensional array and perform a bulk insert.
Large bulk inserts are automatically broken up into multiple, smaller (configureable) bulk inserts to prevent possible locking and slave lag issues.
Inserts are automatically performed using the master database connection.
<br />
The function also supports a number of additional parameters to alter the default behavior of the <code>insert</code> function.
The value returned is the number of records that were inserted. This is true even for an insert that was split across multiple bulk inserts.
</p>

<p>
<strong>insert($tablename, &$data, $modifiers='', $useMaster=true, $action='INSERT')</strong>
<br />
The <code>$modifiers</code> parameter supports the IGNORE modifier.
The <code>$action</code> parameter can be change to REPLACE instead of the default INSERT.
<br />
<pre>
// Single record insert
$data = array('field1'=>'some text', 'field2'=>'other words', 'field3'=>4);
BSP::$db->insert('tablename', $data);

//Bulk insert
$data = array(
   0=>array('field1'=>'some text', 'field2'=>'very easy', 'field3'=>4),
   1=>array('field1'=>'text here, 'field2'=>'no worries', 'field3'=>12),
   2=>array('field1'=>'here is some text', 'field2'=>'fast', 'field3'=>9),
);
BSP::$db->insert('tablename', $data);
</pre>
</p>

<p>
<strong>getLastInsertId($useMaster=true)</strong>
<br />
Returns the auto increment value of the last inserted record.
</p>


<div class="section_title">
UPDATE
</div>
<p>
The <code>update</code> function is very similar to the <code>insert</code> function, except that it includes a filter for a parameter.



<div class="subsection_title">
Special Inserts
</div>
<p>
Sometimes there is a need to perform an insert with formulas and/or special functions (i.e. NOW(), field+1). In cases like these, a name/value data pairing will not work. The prepare/execute combination should be used when you need to do special inserts like these. This still permits using the PDO bindings for escaping the data.
Note: You must specify to use the master connection for both prepare and execute, both functions default to using the slave connection.

<pre>
$query = 'INSERT INTO table1 (field1, field2, field3) VALUES (:value1, NOW(), :value2)';
$presult = BSP::$db->prepare($query, true);
if ( $presult==1 ) {
  BSP::$db->execute( array('value1'=>'X', 'value2'=>'Y'), true);
  $insertId = BSP::$db->getLastInsertId();
}
</pre>

</p>