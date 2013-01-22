@body>
<h2 class="page-header">
Database
</h2>
<p>
{{:sitename}} comes with a stand alone database abstraction layer that uses PDO and is designed to use MySQL as the backend. 
While the database class is fairly powerful, it is not designed to be an ORM and does require some knowledge of SQL to use.
The database class is designed to be a low level tool that will handle the most common tasks in a high scalability environment.
A place where ORMs often fail. The framework does not require this class and it is included for convenience. 
</p>
<p>
<strong>Features:</strong>
<ul>
<li>On-demand connection - multiple connections can be defined, but a connection is only established once a query is run on that connection.</li>
<li>Connection Management - Every instance has a master and slave connection defined. But only one master connection is established and shared across all instances. By default, connections are singleton (1 connection per server) to prevent creating too many connections.</li>
<li>Master/Multiple Slave support - INSERT, UPDATE and DELETE are automatically routed through the Master connection. All other queries default to running through the slave connection, but can be forced to run against the master on demand. </li>
<li>Automatic Escaping - PDO data binding is used to prevent SQL injection.</li>
<li>Bulk Inserts - an insert call automatically detects if a bulk insert should be performed. 
Very large bulk inserts are automatically broken up, and multiple bulk inserts performed based on a max insert size setting to alleviate slave/replication lag.</li>
<li>Auto-reconnect - a dropped connection will automatically be detected, a new connection established and the last failed query rerun.</li>
<li>Profiling - when profiling is enabled, all queries are timed and recorded to help with debugging. Number of connections established is also available.</li>
</ul>

</p>

<br />
<h3>
Loading and Configuration
</h3>
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
For example, the 'attr' parameter specifies using buffered queries when creating a connection:
<pre>
array(
	'host' => IpAddress, 
	'username' => UserName, 
	'db' => DBName, 
	'password' => Password, 
	'attr'=>array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true)
)
</pre>
<br />
{{:sitename}} defaults to setting 2 attributes for every connection. These default attributes are merged with any attributes specified in the configuration. Attributes passed in will override default attributes. Following are the default attributes used by the {{:sitename}} DB class:
<ol>
<li>MYSQL_ATTR_USE_BUFFERED_QUERY = true</li>
<li>MYSQL_ATTR_INIT_COMMAND = "SET NAMES UTF8"</li>
</ol>

</p>
<br />
<h3>
Instantiation
</h3>
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
Note: A connection to the database is not made until a query is run that requires the connection, so there is little overhead in creating database instances. Any initialization that needs to be done upon connection should be configured through the <code>attr</code> MYSQL_ATTR_INIT_COMMAND configuration option.
</p>

<br />
<h3>
SELECT
</h3>
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
$qresult = $basecoat->db->select( $query, $bindings);

// Using ? for data binding
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = ? AND field2 >= ?';
$bindings = array('some text', 4);
$qresult = $basecoat->db->select( $query, $bindings);

// No data bindings
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = "some text" AND field2 >= 4';
$qresult = $basecoat->db->select( $query );
</pre>
</p>

<h3>
Fetching Data
</h3>
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
$qresult = $basecoat->db->select( $query, $bindings );
if ( $qresult>0 ) {
	$result_var	= array();
	while ( $row = $basecoat->db->fetch() ) {
		$result_var[] = $row;
	}
} else if ( $qresult<0 ) {
	echo 'Error: '.$basecoat->db->errorCode.'-'.$basecoat->db->errorMsg;
}
</pre>
</p>

<p>
<strong>fetchAll( &$resultVar, $keyField=null, $grouped=false, $userMaster=false, $method=null )</strong>
<br />
This function can do much more than just return and array of records.
You can specify which field value should be used for the array key.
You can also specify that the results be grouped by the specified field, with the array key being the grouping key.
This will return a hierarchical array.
<pre>
$query	= 'SELECT article_id,comment FROM comments WHERE comment_date&lt;=DATE_SUB(NOW(),INTERVAL 1 DAY)';
$qresult = $basecoat->db->select( $query );
$results_var = array();

// Fetch all records at once
$basecoat->db->fetchAll($results_var);
// Result
array(
	0=>array('article_id'=>'123', 'comment'=>'this is great!'),
	1=>array('article_id'=>'789', 'comment'=>'easier than doing it myself'),
	2=>array('article_id'=>'123', 'comment'=>'very convenient'),
	3=>array('article_id'=>'789', 'comment'=>'great time saver)'
);

// Group records by article_id
$basecoat->db->fetchAll($results_var, 'article_id', true);
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

<p>
<strong>fetchField( $fieldName, $useMaster=false )</strong>
<br />
Sometimes you just need to get a list of values from the database and field names are not necessary.
The <code>fetchField</code> function will retrieve the values of the field specified and return a single dimensional array.
</p>

<h3>
SELECT One
</h3>
<p>
<strong>selectOne( $query, $bindings=null, $useMaster=false )</strong>
<br />
Sometimes you know your query will only find one record, or you only want to retrieve a single record. 
For cases like this, use the selectOne convenience function. 
This works exactly like the <code>select</code> function, except that it retrieves the record if one is found.
This eliminates the need to do a <code>fetch</code> after a select. 

<pre>
$query = 'SELECT field1, field2, field2 FROM table1 WHERE field1 = :value1 LIMIT 1';
$data = $basecoat->db->selectOne( $query, array('value1'=>'X') );
</pre>
</p>

<h3>Special "select"</h3>
<p>
Since the SELECT function does not dictate the structure of the query, any queries that return a list of data can be executed through this function. For example, to retrieve a list of tables or retrieve the list of processes:
<pre>
$query = 'SHOW TABLES';
$qresult = $basecoat->db->select( $query );
$basecoat->db->fetchAll($tables_list);

$query = 'SHOW PROCESSLIST';
$qresult = $basecoat->db->select( $query );
$basecoat->db->fetchAll($process_list);
</pre>
</p>


<br />
<h3>
INSERT
</h3>
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
$basecoat->db->insert('tablename', $data);

//Bulk insert
$data = array(
   0=>array('field1'=>'some text', 'field2'=>'very easy', 'field3'=>4),
   1=>array('field1'=>'text here, 'field2'=>'no worries', 'field3'=>12),
   2=>array('field1'=>'here is some text', 'field2'=>'fast', 'field3'=>9),
);
$basecoat->db->insert('tablename', $data);
</pre>
</p>

<p>
<strong>getLastInsertId($useMaster=true)</strong>
<br />
Returns the auto increment value of the last inserted record.
</p>


<br />
<h3>
UPDATE
</h3>
<p>
<strong>update( $tablename, $data, $filter, $filterBindings=null, $useMaster=true )</strong>
</p>
<p>
The <code>update</code> function is very similar to the <code>insert</code> function, except that it includes 2 extra filter parameters. The <code>$filter</code> parameter is any valid SQL clause. Optionally, a name/value array can also be passed as <code>$filterBindings</code> to be "bound" with the <code>$filter</code>.
</p>

<br />

<h3>
DELETE
</h3>
<p>
<strong>delete( $tablename, $filter, $filterBindings=null, $useMaster=true, $modifiers='' )</strong>
</p>
<p>The <code>delete</code> function is primarily a convenience function for escaping filter variables and profiling. The <code>$modifers</code> parameter will allow complex DELETEs, like deleting by joining tables together.
</p>

<br />
<h3>
PREPARE/EXECUTE Custom Queries
</h3>
<p>
Sometimes there is a need to perform queries more complex than just basic bindings. For example, an insert with formulas and/or special functions (i.e. NOW(), field+1). In cases like these, a basic name/value data pairing will not work. The prepare/execute combination should be used when you need to do special inserts and/or queries like these. This still permits using the PDO bindings for escaping the data. 
Any type of complex queries (i.e. INSERT...SELECT) that do not fit the standard query types should use the prepare/execute combination. Single PREPARE with multiple EXECUTE is also supported for running the same query multiple times where only the data changes.<br />
Note: You must specify to use the master or slave connection for both prepare and execute, both functions default to using the slave connection. Internally {{:sitename}} actually runs all queries through the PREPARE/EXECUTE functions.

<pre>
$query = 'INSERT INTO table1 (field1, field2, field3) VALUES (:value1, NOW(), :value2)';
$presult = $basecoat->db->prepare($query, true);
if ( $presult==1 ) {
  $basecoat->db->execute( array('value1'=>'X', 'value2'=>'Y'), true);
  $insertId = $basecoat->db->getLastInsertId();
}
</pre>
</p>

<h3>
Raw Queries
</h3>
Sometimes there is a need to run "queries" that do not fit in any of the typical INSERT, SELECT, UPDATE, DELETE. For these use cases there is a <code>rawQuery</code> function that gives you the ability to run any SQL command.

<h3>
Error Messages
</h3>
<p>
If an error is encountered during processing, the error code and message with be stored to the respective class variables $errorCode and $errorMsg. Those will always contain the last error encountered. All errors are also stored in a static <code>$errors</code> array, which will contain up to 10 errors. If more than 10 errors are encountered, an 11th error will be added to the <code>$errors</code> array indicating too many errors were encountered.
<p>
<strong>$errorCode</strong>
<br />The error code for the last error encountered.
</p>
<p>
<strong>$errorMsg</strong>
<br />The error message for the last error encountered.
</p>
<p>
<strong>DB::$errors</strong> 
<br />Contains up to the first 10 errors encountered.
</p>

<h3>
Profiling
</h3>
<p>All queries are automatically logged to a static <code>$profiling</code> array to help in debugging and/or performance tuning. Each entry in the array will contain:
<ol>
<li>The connection label to determine which server the query ran against.</li>
<li>The query string, including binding strings.</li>
<li>The binding variables bound to the query statement.</li>
<li>The time the query took to run.</li>
</ol>
</p>

<p>
<strong>getProfiling()</strong>
<br />
Return an array of profiling information. There are two profiling sections: meta and queries. The "meta" section contains summary information including db instances created, number of connections made, number of queries run, total time of queries, and number of errors. <br />
The "queries" section contains all the queries run up the <code>$maxProfiling</code> limit set (default 30). The query, binding values, duration and result code are recorded with each query.
</p>




