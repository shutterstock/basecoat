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
<li>On-demand connection - connections can be configured, but a connection is only established once a query is run</li>
<li>Master/Multiple Slave support - multiple connections are supported, queries that modify data are automatically routed through the Master connection. All other queries default to running through the salve connection. </li>
<li>Connection Management - By default, connections are singleton (1 connection per server) to prevent creating too many connections. A new connection can be forced to a slave.</li>
<li>Automatic Escaping - data binding is used to prevent SQL injection.</li>
<li>Bulk Inserts - Inserts are normally performed by passing an associative array. 
Multi-dimensional arrays are automatically and bulk inserts performed. 
Very large arrays are automatically broken up and multiple bulk inserts performed based on a max insert size setting.</li>
<li>Auto-reconnect - a connection disconnect with automatically be detected, a new connection established and the last failed query rerun.</li>
<li>Profiling - when profiling is enabled, all queries are timed and recorded. Number of connections established is also available.</li>
</ul>

</p>




<div class="section_title">
Loading and Configuration
</div>
<p>
The class requires that it be configured for use prior to creating instances of the class. 
Each instance of the class will have 2 connection handles, one to the database instance specified on instantiation and one to the master. 
There is a single connection to the master database shared across all instances of the database class. 
Profiling also aggregates across all instances.

Once included, the setServerConfig() function must be called to load the available database server configurations. 
This function accepts an array, where each item contains parameter for connecting to each database server. 
An optional "label" parameter can be included with each configuration which will be used to "name" the connection. 
The setServerConfig() function accepts a second parameter, which specifies which configuration record is the master.

<pre>
$servers = array(
    0 => array('host' => IpAddress, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Master'),
    1 => array('host' => IpAddress, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 1'),
    2 => array('host' => IpAddress, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 2'),
    3 => array('host' => IpAddress, 'username' => UserName, 'db' => DBName, 'password' => Password, 'label'=>'Slave 3'),
    ...
    );
// Set configuration and specify 0 as the master connection
DB::setServerConfig($servers, 0);
</pre>

Finer control over connections can be specified with an additional 'attr' parameter option in the $server list. 
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
To create an instance of the database class, call the getServerInstance() function. Specify which connection setting to use by pass the "key" of the item in the $server configuration list to use. If any instance already exists, the existing instance will be returned. You can force a new instance to be created by passing "true" as the second parameter.
<pre>
// Create an instance with a connection to Slave 2, forcing a new instance to be created
$db2 = DB::getServerInstance(2, true);
</pre>
Note: A connection to the database is not made until a query is run that requires the connection.
</p>


<div class="section_title">
Running a SELECT query
</div>
<p>
Select queries are run against the default connection of the instance being used. This behavior can be overridden by specifying and extra "user master" parameter when call a select function. When select queries are run, the data is not retrieve until a "fetch" function is run, or the "fetch all" parameter is used in the select function.

The select function supports an optional "bindings" parameter which should be used so proper escaping is used when using external values. To use "bindings" insert a placeholder in your query that will be replaced by the appropriate binding values. While you can use "?" as placeholder, it is preferred that named placeholders be used to better document which values are being inserted where.

<pre>
// Using named bindings
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = :value1 AND field2 >= :value2';
$bindings = array('value1'=>'some text', 'value2'=>4);
$qresult = BSP::$db->select( $query, $bindings);

// Using ? as binding, not preferred
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = ? AND field2 >= ?';
$bindings = array('some text', 4);
$qresult = BSP::$db->select( $query, $bindings);

// Straight select, no bindings
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = "some text" AND field2 >= 4';
$qresult = BSP::$db->select( $query );
</pre>
</p>

<div class="subsection_title">
Selecting One
</div>
<p>
Many times you know your query will only find one record, or only want to retrieve a single record. 
For cases like this, use the selectOne function. This will return an associative array of the record found, if any. 
This bypasses the need to do a fetch after the select. 
If no matching records are found or there is an error, then a 0 or negative number will be returned rather than an array.

<pre>
$query = 'SELECT field1, field2, field2 FROM table1 WHERE field1 = :value1 LIMIT 1';
$data = BSP::$db->selectOne( $query, array('value1'=>'X') );
</pre>
</p>

<div class="subsection_title">
Retrieving SELECTed Data
</div>
<p>
The select function will always return the number of records found by the select. A 0 will be returned if no records are found. If a negative value is returned, this means an error occurred and the 'errorMsg' and/or 'errorCode' class variables should be checked for the specific error.
Once a select is run, the result set, if any, would need to be retrieved. This can be done using the fetchAll or fetch functions. The fetchAll function will retrieve the entire result set, the fetch function will retrieve the next record. The fetchAll requires a variable to be passed where the result set will be stored.
The fetchAll function will retrieve the records from the last select used for the connection (master or slave).

<pre>
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = :value1 AND field2 >= :value2';
$bindings = array('value1'=>'some text', 'value2'=>4);
$qresult = BSP::$db->select( $query, $bindings);
if ( $qresult>0 ) {
    $result_set = array();
    BSP::$db->fetchAll($result_set);
} else if ( $qresult<0 ) {
    echo 'Error: '.BSP::$db->errorCode.'-'.BSP::$db->errorMsg;
}
</pre>
</p>

<div class="subsection_title">
Retrieving with index key/grouped
</div>
<p>
The fetchAll function supports optional parameters to retrieve the data with keys based on a field value, or grouped by a field. 
This can be convenient if you need to "index" the data, or group it for display.

<pre>
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = :value1 AND field2 >= :value2';
$bindings = array('value1'=>'some text', 'value2'=>4);
$qresult = BSP::$db->select( $query, $bindings);
$result_set = array();

// Retrieve indexed off of the value of 'field1'
BSP::$db->fetchAll($result_set, 'field1');
// Result
array('field1_val1'=>
    array('field1'=>'value', 'field2'=>'value, ...),
 'field1_val2'=>
    array('field1'=>'value', 'field2'=>'value, ...)
);

// Retrieve indexed off of, and grouped by the value of 'field1'
BSP::$db->fetchAll($result_set, 'field1', true);
// Result
array('field1_val1'=>
    array(0=>array('field1'=>'value', 'field2'=>'value, ...),
          1=>array('field1'=>'value', 'field2'=>'value, ...),
         ),
 'field1_val2'=>
    array(0=>array('field1'=>'value', 'field2'=>'value, ...),
          1=>array('field1'=>'value', 'field2'=>'value, ...),
         ),
);
</pre>

</p>

<div class="subsection_title">
SELECTing from the Master
</div>
<p>
Occasionally you may need to run a SELECT against the master connection instead of the default instance connection. This can be done by simply specifying "true" for the "use master" parameter. 
Note: When specifying "use master", all subsequent related functions calls (i.e. fetchAll) related to the query must also specify "use master".

<pre>
$query = 'SELECT field1, field2, field3 FROM table1 WHERE field1 = :value1 AND field2 >= :value2';
$bindings = array('value1'=>'some text', 'value2'=>4);
// Add "true" parameter to run against master
$qresult = BSP::$db->select( $query, $bindings, true);
$result_set = array();
// Add "true" parameter to fetch data from last query run against the master
BSP::$db->fetchAll($result_set, $bindings, true);
</pre>
</p>



<div class="section_title">
INSERTs and Bulk Inserts
</div>
<p>
The insert functional is fairly flexible in that it can handle INSERTs or REPLACEs, modifiers (i.e. IGNORE, DELAYED), along with bulk inserts. Inserts default to running against the master, but can be overridden to perform the insert on the instance's default connection. At the most basic level, the insert function accepts two parameters: a table name, and an associative array. The array keys must match the field names in the database table being inserted into. The data should not be escaped, the function will handle any needed escaping.

To perform a bulk insert, simply pass an array containing multiple associative arrays. Note that each array entry must have the same set of keys. The function automatically determines if a bulk insert should be performed. Bulk inserts are broken up into multiple insert queries with a maximum of 100 records per insert query. This can be changed/tuned by modifying the $bulkInsertSize public class variable. Bulk inserts have been successfully run on a 30,000 entry array.

The function will return the number of records that were inserted. To get the last insert ID, call the getLastInsertId() function. Remember to pass a "false" parameter if the insert did not run against the master.


<pre>
// Single record insert
$data = array('field1'=>'some text', 'field2'=>'other words', 'field3'=>4);
BSP::$db->insert('tablename', $data);

//Bulk insert
$data = array(
   0=>array('field1'=>'some text', 'field2'=>'other words', 'field3'=>4),
   1=>array('field1'=>'text here, 'field2'=>'other words', 'field3'=>12),
   2=>array('field1'=>'here is some text', 'field2'=>'other words', 'field3'=>9),
);
BSP::$db->insert('tablename', $data);

//Insert with an IGNORE modify
$data = array('field1'=>'some text', 'field2'=>'other words', 'field3'=>4);
BSP::$db->insert('tablename', $data, 'IGNORE');

//Insert on the slave/default instance connection instead of the master
$data = array('field1'=>'some text', 'field2'=>'other words', 'field3'=>4);
BSP::$db->insert('tablename', $data, null, false);
</pre>
</p>

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