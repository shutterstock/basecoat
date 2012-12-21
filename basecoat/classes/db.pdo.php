<?php
namespace Basecoat;

class DB {
	/**
	 * Database Connection parameters for instance
	 * @var array
	 */
	private $connectParams		= array();
	/**
	 * Master Database Connection parameters for class
	 * @var array
	 */
	private static $connectParamsMaster= array();
	/**
	 * Default Database Connection parameters
	 * @var array
	 */
	private static $connectDefaults	= array('host'=>'localhost',
											'label'=>'default', 
											'type'=>'mysql', 
											'attr'=>array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true, \PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8")
											);
	/**
	 * List of server and connection parameters to use for each on
	 * An associatve array in the following format:
	 * id => array('host' => 'host', 'username' => 'username', 'password' => 'password', 'db' => 'database', 'label'=>'master','attr'=>array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true))
	 *
	 * @var array
	 */
	private static $servers		= null;
	/**
	 * Database handle for instance
	 * @var object
	 */
	private $dbh				= null;
	/**
	 * Statment handle for instance
	 * @var object
	 */
	private $sth				= null;
	/**
	 * Master Database handle for class
	 * @var object
	 */
	private static $mdbh		= null;
	/**
	 * Error codes to attempt a reconnect on
	 *
	 */
	private static $reconnect_on_error	= array(2003, 2004, 2005, 2006, 2011, 2012);
	/**
	 * Master Statement handle for class
	 * @var object
	 */
	private static $msth		= null;
	/**
	* mysql wait_timeout value
	*/
	private static $mtimeout	= 90;
	private $timeout			= 90;
	/**
	 * A "human" name to assign to the database connection
	 * @var string
	 */
	private $connectionLabel	= 'none';
	/**
	 * A "human" name to assign to the master database connection
	 * @var string
	 */
	private static $mConnectionLabel	= 'none';
	/**
	 * The last query that was run
	 * @var string
	 */
	public $lastQuery			= null;
	/**
	 * The last error message that was generated
	 * @var string
	 */
	public $errorMsg			= null;
	/**
	 * The last error code that was generated
	 * @var string
	 */
	public $errorCode			= null;
	/**
	 * List of errors that have been generated
	 * @var array
	 */
	public static $errors		= array();
	/**
	 * Number of errors that have been generated
	 * @var int
	 */
	public static $errorCntr	= 0;
	/**
	 * Also log errors via error_log
	 * @var int
	 */
	public $useErrorLog			= 1;
	/**
	 * Default storage variable for select result if no variable is provided
	 * @var array
	 */
	public $selectResult		= array();
	/**
	 * Last insert ID
	 * @var int
	 */
	public $insertId			= 0;
	/**
	 * Maximum number of records that can be inserted at one time. 
	 * Large inserts are broken up into multiple insert of this size
	 * @var int
	 */
	public $bulkInsertSize		= 100;
	/**
	 * Debugging turned on(1) or off(0)
	 * @var int
	 */
	public $debug				= 0;
	/**
	 * List of all queries that have been run, along with profiling information (i.e. time taken)
	 * @var array
	 */
	private static $profiling	= array();
	/**
	 * Maximum number of items to retain in profiling list
	 * @var int
	 */
	public static $maxProfiling	= 30;
	/**
	 * Number of queries that have been run
	 * @var int
	 */
	private static $queryCntr	= 0;
	/**
	 * Total time all queries have taken
	 * @var float
	 */
	private static $queryTime	= 0;
	/**
	 * List of references to instances of the class that have been created
	 * @var array
	 */
	private static $instances	= array();
	/**
	 * Number of database connections that have been made across all instances
	 * @var int
	 */
	private static $connections	= 0;
	/**
	 * Number of instances that have been created
	 * @var int
	 */
	public static $instanceCntr	= 0;

	/**
	* Set Server Connection Parameters
	* The server list can have all the fields listed in $connectDefaults. At a minimum, it should have a lablel
	*
	* @param array $servers list of servers to connect to. The array key should be the "id" of the server.
	* @param mixed $masterKey	the array key in $servers that acts as the Master server
	*/
	public static function setServerConfig( array $servers, $masterKey=null ) {
		self::$servers	= $servers;
		if ( is_null($masterKey) ) {
			$masterKey	= 0;
		}
		self::setConnectParamsMaster($servers[$masterKey]);
		if ( isset( $servers[$masterKey]['label'] ) ) {
			self::$mConnectionLabel	= $servers[$masterKey]['label'];
		}
		return 1;
	}
	
	/**
	* Return an instance of the class connected to the specified server
	* If any instance of the request server already exist, that instance will be returned
	*
	* @param mixed $serverKey the $servers key to use for connecting
	* @param boolean $new force the creation of a new instance
	*/
	public static function getServerInstance( $serverKey, $new=false ) {
		if ( !isset(self::$servers[$serverKey]) ) {
			self::$errors[]		= array('connection'=>'config', 'code'=>'n/a', 'msg'=>'Invalid server configuration key ('.$serverKey.')');
			return -1;
		}
		if ( !isset(self::$instances[$serverKey]) ) {
			// No instances, create one
			self::$instances[$serverKey][]	= new DB(self::$servers[$serverKey]);
		} else if ( $new ) {
			// Instance exists, but force creation of a new one
			self::$instances[$serverKey][]	= new DB(self::$servers[$serverKey]);
			// check if timeout value needs to be set
			if ( isset(self::$servers[$serverKey]['timeout']) ) {
				self::$instances[$serverKey][ count(self::$instances[$serverKey])-1 ]->setConnectTimeout( self::$servers[$serverKey]['timeout'] );
			}
			return self::$instances[$serverKey][ count(self::$instances[$serverKey])-1 ];
		} else if ( is_object(self::$instances[$serverKey][0]) ) {
			// return first existing instance, providing it still exists
		} else {
			// Instance exists, but is no longer an object
			self::$instances[$serverKey][0]	= new DB(self::$servers[$serverKey]);
		}
		// check if timeout value needs to be set
		if ( isset(self::$servers[$serverKey]['timeout']) ) {
			self::$instances[$serverKey][0]->setConnectTimeout( self::$servers[$serverKey]['timeout'] );
		}
		return self::$instances[$serverKey][0];
	}

	/**
	 * Initialize an instance of the database class.
	 * Connections are not made at this time, only the parameters are set.
	 *
	 * @param mixed $host		string (hostname) or associative array with parameter to use
	 * @param string $username	user name to use for login
	 * @param string $password	password to use for login
	 * @param string $db	optional database to select
	 */
	public function __construct($host, $username=null, $password=null, $db=null, $label='n/a') {
		$this->setConnectParams($host, $username=null, $password=null, $db=null, $label='n/a');
		if ( is_array($host) && isset($host['label']) ) {
			$this->connectionLabel	= $host['label'];
		} else {
			$this->connectionLabel	= $label;
		}
		self::$instanceCntr++;
	}

	/**
	 * Actions to perform when class is destructed
	 */
	public function __destruct() {
		$this->disconnect();
		$this->disconnect(true);
	}
	
	/**
	 * Set database connection parameters. This does not establish a connection,
	 * only sets the parameters for a connection.
	 *
	 * @param mixed $host		string (hostname) or associative array with parameter to use
	 * @param string $username	user name to use for login
	 * @param string $password	password to use for login
	 * @param string $db	optional database to select
	 * @param string $label	a "name" reference for the connection, used for profiling
	 * @param array $attr	connection attribute list (i.e. PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	 */
	public function setConnectParams( $host, $username=null, $password=null, $db=null, $label='n/a', $attr=null ) {
		if ( is_array($host) ) {
			$this->connectParams	= array_merge( self::$connectDefaults, $host );
			$label					= $this->connectParams['label'];
		} else {
			$this->connectParams	= array('host'=>$host,
											'username'=>$username,
											'password'=>$password,
											'db'=>$db,
											'label'=>$label,
											'type'=>'mysql',
											'attr'=>$attr
											);
		}
		$this->connectionLabel		= $label;
	}

	/**
	 * Set MASTER database connection parameters. This does not establish a connection,
	 * only sets the parameters for a connection.
	 *
	 * @param mixed $connectParams		associative array with parameters to use, see setConnectParams function
	 */
	public static function setConnectParamsMaster( $connectParams ) {
		self::$connectParamsMaster	= array_merge( self::$connectDefaults, $connectParams );
	}
	
	/**
	 * Establish a connection to the database. Note: Connections are "on demand", you shouldn't need to call this function yourself
	 * Called automatically by execQuery if no connection.
	 * Connection parameters must have already been set with setConnectParams
	 * Use $persistantConnections variable to toggle persistant connections 
	 * (DON'T USE PERSISTANT CONNECTIONS UNLESS YOU KNOW WHY YOU SHOULDN'T)
	 *
	 * @return mixed		returns 1 on success or -1 on failure
	 */
	public function connect($useMaster=false, $reconnect=false) {
		if ( $reconnect ) {
			$this->disconnect($useMaster);
		}
		if ( $useMaster ) {
			return $this->connectMaster($reconnect);
		}
		if ( is_object($this->dbh) && !$reconnect ) {
			// Already connected
			return 1;
		}

		static $retries		= 0;
		$connect_result	= 1;

		if ( $this->debug>1 ) {
			echo 'DB CONNECT PARAMS: '.print_r($this->connectParams, true)."\n";
		}
		$dsn		= $this->connectParams['type'].':dbname='.$this->connectParams['db'].';host='.$this->connectParams['host'];
		if ( isset($this->connectParams['sock']) ) {
			$dsn	.= ';unix_socket='.$this->connectParams['sock'];
		}
		try {
			$this->dbh	= new \PDO( $dsn, $this->connectParams['username'], $this->connectParams['password'], $this->connectParams['attr'] );
		} catch (Exception $e) {
			$this->errorMsg		= $e->getMessage();
			$this->errorCode	= $e->getCode();
			error_log('DB CLASS ERROR: '.$this->errorMsg, 0);
			if ( $retries<2 ) {
				$retries++;
				error_log('DB CLASS ERROR: Attempting to connect again - '.$retries.' '.$this->connectParams['host'], 0);
				usleep(100000);
				$connect_result	= $this->connect($useMaster, $reconnect);
			} else {
				$connect_result	= -1;
				$retries	= 0;
			}
			return $connect_result;
		}
		//Update timeout setting
		if ( $this->timeout>0 ) {
			$this->rawQuery('SET SESSION wait_timeout='.$this->timeout, false);
		}
		if ( $reconnect ) {
			error_log('DB CLASS RECONNECTED (S)', 0);
		}
		self::$connections++;
		if ( $retries>0 ) {
			error_log('DB CLASS: Connected after '.$retries.' attempts '.$this->connectParams['host']);
		}
		$retries	= 0;
		return $connect_result;
	}
	
	/**
	* Establish a connection to the Master server
	*
	* @return mixed		returns 1 on success or -1 on failure
	*/
	private function connectMaster($reconnect=false) {
		if ( is_object(self::$mdbh) && !$reconnect ) {
			// Already connected
			return 1;
		}
		if ( $this->debug>1 ) {
			echo 'MASTER DB CONNECT PARAMS: '.print_r(self::$connectParamsMaster, true)."\n";
		}
		static $retries		= 0;
		$connect_result	= 1;

		$c			=& self::$connectParamsMaster;
		$dsn		= $c['type'].':dbname='.$c['db'].';host='.$c['host'];
		if ( isset($c['sock']) ) {
			$dsn	.= ';unix_socket='.$c['sock'];
		}
		try {
			self::$mdbh	= new \PDO( $dsn, $c['username'], $c['password'], $c['attr'] );
		} catch (Exception $e) {
			$this->errorMsg		= $e->getMessage();
			$this->errorCode	= $e->getCode();
			error_log('DB CLASS ERROR: '.$this->errorMsg, 0);
			if ( $retries<2 ) {
				$retries++;
				error_log('DB CLASS ERROR: Attempting to connect again - '.$retries.' '.$c['host'], 0);
				usleep(100000);
				$connect_result	= $this->connectMaster($reconnect);
			} else {
				$connect_result	= -1;
				$retries	= 0;
			}
			return $connect_result;
		}
		if ( $reconnect ) {
			error_log('DB CLASS RECONNECTED (M)', 0);
		}
		self::$connections++;
		//Update timeout setting
		if ( self::$mtimeout>0 ) {
			$this->rawQuery('SET SESSION wait_timeout='.self::$mtimeout, true);
		}
		if ( $retries>0 ) {
			error_log('DB CLASS: Connected after '.$retries.' attempts '.$c['host']);
		}
		$retries	= 0;
		return $connect_result;
	}
	
	/**
	 * Set how long a db connection will be left open
	 * before disconnecting
	 *
	 * @param Integer $value seconds before timeout
	 * @param Boolean $useMaster set timeout value on master/slave connection
	 */
	public function setConnectTimeout($value, $useMaster=false) {
		$value	= (int)$value;
		if ( $useMaster ) {
			self::$mtimeout	= $value;
		} else {
			$this->timeout	= $value;
		}
		if ( is_object($this->dbh) ) {
			// Already connected, update timeout value
			$this->rawQuery('SET SESSION wait_timeout='.$value, $useMaster);
		}
	}

	/**
	 * Disconnect from the database
	 *
	 * @param Boolean $useMaster disconnect from master/slave
	 */
	public function disconnect($useMaster=false) {
		if ( $useMaster ) {
			self::$mdbh	= null;
		} else {
			$this->dbh			= null;
		}
	}

	/**
	 * Execute query. Can be called directly for complex query.
	 * Used by various other functions (i.e. select, update, insert) to run a query/
	 *
	 * @param string $query	SQL query to run
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int			result of running the query (not data)
	 */
	public function rawQuery( $query, $useMaster=false ) {
		static $retries		= 0;
		if ( $this->connect($useMaster) == -1 ) {
			return -2;
		}
		$debug				= null;
		if ( $useMaster ) {
			$dbh			=& self::$mdbh;
			$connectionLabel= self::$mConnectionLabel;
		} else {
			$dbh			=& $this->dbh;
			$connectionLabel= $this->connectionLabel;
		}
		// Save query to last query variable
		$this->lastQuery	= $query;
		// Clear error variables
		$this->errorCode	= $this->errorMsg	= null;
		// Run query and record how long it took
		$qStartTime			= round(microtime(true),3);
		$result				= $dbh->exec( $query );
		$qTime				= (round(microtime(true),3)-$qStartTime);
		if ( $result===false ) {
			// Log error
			$this->logErrorInfo($dbh, $connectionLabel);
			$debug			= $this->errorMsg;
			// Check for dropped connection
			if ( in_array($this->errorCode, self::$reconnect_on_error) && $retries==0 ) {
				$retries++;
				$this->connect($useMaster, true);
				$result		= $this->rawQuery( $query, $useMaster);
			} else {
				$result		= -1;
			}
		} else if ( $retries>0 ) {
			$retries	= 0;
		}
		$this->updateProfiling( $query, null, $qTime, $result, $connectionLabel, $debug );
		return $result;
	}

	/**
	 * Prepare a query for execution by the execPrepared function
	 *
	 * @param string $query	the query string, may contain data binding placeholder for use by execPrepared
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @param array $attr	custom PDO attributes to set
	 * $return int	whether statement was successfully prepared (1), or not (-1)
	 */
	public function prepare( $query, $useMaster=false, $attr=null ) {
		if ( $this->connect($useMaster) == -1 ) {
			return -2;
		}
		if ( is_null($attr) ) {
			$attr			= array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY);
		}
		$this->lastQuery	= $query;
		if ( $useMaster ) {
			$sth			=& self::$msth;
			$dbh			=& self::$mdbh;
			self::$msth 	= self::$mdbh->prepare($query, $attr );
			$connectionLabel= self::$mConnectionLabel;
		} else {
			$sth			=& $this->sth;
			$dbh			=& $this->dbh;
			$this->sth 		= $this->dbh->prepare($query, $attr );
			$connectionLabel= $this->connectionLabel;
		}
		if ( $sth===false ) {
			$this->logErrorInfo($dbh, $connectionLabel);
			// Check for dropped connection
			if ( in_array($this->errorCode, self::$reconnect_on_error) && $retries==0 ) {
				$retries++;
				$this->connect($useMaster, true);
				$result	= $this->prepare( $query, $useMaster, $attr);
			} else {
				$result	= -1;
			}
		} else {
			$result	= 1;
		}
		$sth->setFetchMode(\PDO::FETCH_ASSOC);
		return $result;
	}
	
	/**
	 * Execute a prepare statement to data binding values. Can be called
	 * repeatedly for the same prepared query, with different data bindings.
	 *
	 * @param array $values	data binding values. Can be an associative array to bind by name
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int returns number of rows that were affected, or -1 if query failed
	 */
	public function execute( $bindings = array(), $useMaster=false ) {
		static $retries		= 0;
		$debug				= null;
		if ( $useMaster ) {
			$sth			=& self::$msth;
			$connectionLabel= self::$mConnectionLabel;
		} else {
			$sth			=& $this->sth;
			$connectionLabel= $this->connectionLabel;
		}
		$qStartTime			= round(microtime(true),3);
		if ( $sth->execute( $bindings ) ) {
			$result			= $sth->rowCount();
			// Check if we need to run an explain for debugging
			if ( $this->debug>0 ) {
				$dbh		= ( $useMaster ? self::$mdbh : $this->dbh );
				$debug		= $this->explainQuery($this->lastQuery, $bindings, $dbh);
			}
			if ( $retries>0 ) {
				$retries	= 0;
			}
		} else {
			$this->logErrorInfo($sth, $connectionLabel);
			// Check for dropped connection
			if ( in_array($this->errorCode, self::$reconnect_on_error) && $retries==0 ) {
				$retries++;
				$this->connect($useMaster, true);
				$result		= $this->execute( $bindings, $useMaster);
			} else {
				$debug		= $this->errorMsg;
				$result		= -1;
			}
		}
		$qTime				= (round(microtime(true),3)-$qStartTime);
		$this->updateProfiling( $this->lastQuery, $bindings, $qTime, $result, $connectionLabel, $debug );
		return $result;
	}

	/**
	 * Run a SELECT query with optional data binding values
	 *
	 * @param string $query	SQL Select query to run
	 * @param array $bindings	array of values to bind to the query. Can be an associative array to bind by name
	 * @param boolean $fetchAll		1 to fetch data into an associated array ($this->selectResult), 0 to just run the query (use on of the fetch/fetchAll functions to retrieve data)
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int 	returns number of rows selected. Use SQL_CALC_FOUND_ROWS with qFoundRows if using LIMIT, to get total rows found.
	 */
	public function select( $query, $bindings=null, $useMaster=false, $fetchAll=false ) {
		static $retries		= 0;
		if ( $this->connect($useMaster) == -1 ) {
			return -2;
		}
		$debug		= null;
		$sth		= null;
		if ( $useMaster ) {
			$dbh	=& self::$mdbh;
			if ( is_object(self::$msth) ) {
				self::$msth->closeCursor();
			}
			self::$msth	=& $sth;
			$connectionLabel	= self::$mConnectionLabel;
		} else {
			$dbh	=& $this->dbh;
			if ( is_object($this->sth) ) {
				$this->sth->closeCursor();
			}
			$this->sth	=& $sth;
			$connectionLabel	= $this->connectionLabel;
		}
		// Clear result holder
		$this->selectResult		= array();
		$this->lastQuery		= $query;
		if ( is_null($bindings) ) {
			$qStartTime			= round(microtime(true),3);
			$sth				= $dbh->query($query);
			$qTime				= (round(microtime(true),3)-$qStartTime);
			if ( $sth===false  ) {
				$this->logErrorInfo($dbh, $connectionLabel);
				$debug			= $this->errorMsg;
				// Check for dropped connection
				if ( in_array($this->errorCode, self::$reconnect_on_error) && $retries==0 ) {
					$retries++;
					$this->connect($useMaster, true);
					$result		= $this->select( $query, $bindings, $useMaster, $fetchAll);
				} else {
					$result		= -1;
				}
			} else {
				$result 			= $sth->rowCount();
				if ( $retries>0 ) {
					$retries	= 0;
				}
				// Check if we need to run an explain for debugging
				if ( $this->debug>0 ) {
					$debug		= $this->explainQuery($query, null, $dbh);
				}
			}
			$this->updateProfiling( $query, $bindings, $qTime, $result, $connectionLabel, $debug );
		} else {
			$presult			= $this->prepare( $query, $useMaster );
			if ( $presult>0 ) {
				$result				= $this->execute( $bindings, $useMaster );
				// Check if we need to run an explain for debugging
				if ( $this->debug>0 ) {
					$debug			= $this->explainQuery($query, $bindings, $dbh);
				}
			}
		}
		if ( $result>-1 ) {
			if ( $fetchAll ) {
				if ( $this->fetchAll($this->selectResult, null, false, $useMaster) == -1 ) {
					return -1;
				}
			}
			return $result;
		} else {
			return $result;
		}
	}
	
	/**
	 * Run a SELECT query with optional data binding values, and retrieves ALL rows
	 * This will use a separate statement handle so other outstanding queries
	 * do not get overwritten
	 *
	 * @param string $query	SQL Select query to run
	 * @param array $bindings		array of values to bind to the query. Can be an associative array to bind by name
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return mixed 	returns associative array of data on success, negative int on failure
	 */
	public function selectAll($query, $bindings=null, $useMaster=false) {
		// Save current statement handle
		if ( $useMaster ) {
			$sth	= self::$msth;
			self::$msth	= null;
		} else {
			$sth	= $this->sth;
			$this->sth	= null;
		}
		$qresult	= $this->select( $query, $bindings, $useMaster, true);
		// Restore previous statement handle
		if ( $useMaster ) {
			self::$msth	= $sth;
		} else {
			$this->sth	= $sth;
		}
		
		if ( $qresult>0 ) {
			return $this->selectResult;
		} else {
			return $qresult;
		}
	}
	
	/**
	 * Run a SELECT query with optional data binding values, and retrieve only 1 row
	 *
	 * @param string $query	SQL Select query to run
	 * @param array $bindings		array of values to bind to the query. Can be an associative array to bind by name
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return mixed 	returns associative array of data on success, negative int on failure
	 */
	public function selectOne($query, $bindings=null, $useMaster=false ) {
		$result		= $this->select( $query, $bindings, $useMaster, false );
		if ( $result>0 ) {
			return $this->fetch($useMaster);
		}
		return $result;
	}

	/**
	 * Run and EXPLAIN on a query for debugging puroposes.
	 * Run automatically on SELECTs if debug>0
	 *
	 * @param string $query  SQL Select query to run
	 * @param array $bindings		array of values to bind to the query. Can be an associative array to bind by name
	 * @param object $dbh	database handle to use
	 */
	private function explainQuery($query, $bindings=null, &$dbh) {
		// Check if query is a SELECT
		if ( substr($query, 0, 6)!='SELECT' ) {
			return '';
		}
		if ( is_null($bindings) ) {
			$explain_sth	= $dbh->query('EXPLAIN '.$query);
		} else {
			$explain_sth	= $dbh->prepare( 'EXPLAIN '.$query );
			if ( $explain_sth!=false ) {
				$result		= $explain_sth->execute( $bindings );
				if ( !$result ) {
					return '';
				}
			}
		}
		if ( $explain_sth===false ) {
			$this->logErrorInfo($dbh, 'debug');
			return 'statement error';
		} else {
			$explanation	= $explain_sth->fetchAll(\PDO::FETCH_ASSOC);
			return $explanation;
		}
	}
	

	/**
	 * Retrieve total rows found for last SELECT SQL_CALC_FOUND_ROWS query with LIMIT clause
	 *
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int	number of total rows found
	 */
	public function foundRows($useMaster=false) {
		$result	= $this->select('SELECT FOUND_ROWS() fr', null, $useMaster, true);
		if ( $result!=-1 ) {
			return $this->selectResult[0]['fr'];
		}
		return $result;
	}

	/**
	 * Update record(s) in database
	 *
	 * @param string $tablename	name of table to update
	 * @param array $data	associative array of field/value pairs to update. Note only raw data is supported, not formulas
	 * @param string $filter SQL filter to apply to update, should be properly escaped
	 * @param string $filterBindings associative array name/value pairs to use for binding. CAUTION: may override $data bindings if same names are used
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int	number of rows updated, or negative if an error occurred
	 */
	public function update($tablename, $data, $filter, $filterBindings=null, $useMaster=true) {
		// Check for filter bindings
		if ( is_array($filterBindings) ) {
			// Check if there are any binding conflicts between $data and $filterBindings
			$conflicts	= array_intersect_key($data, $filterBindings);
			if ( count($conflicts)>0 ) {
				$this->errorCode	= 'n/a';
				$this->errorMsg		= 'Duplicate bindings for data and filter ('.implode(', ',array_keys($conflicts)).').';
				return -2;
			}
		} else if ( !is_null($filterBindings) ) {
			// Invalid filter binding specification, must be an array or null
			$this->errorCode	= 'n/a';
			$this->errorMsg		= 'Invalid filter binding passed, must be an array or null.';
			return -3;
		}
		// Add backticks to table name
		$tablename	= '`'.str_replace('.', '`.`', trim($tablename, '"\'`') ).'`';
		$query		= 'UPDATE '.$tablename.' SET ';
		$setList	= array();
		// Build "set" update string and Add proper name binding prefix to binding array
		$setList	= array();
		foreach( $data as $field=>$val ) {
			$setList[]			= '`'.$field.'`= :'.$field;
		}
		$query		.= implode(', ', $setList).' WHERE ' . $filter;
		// Check if we need to add filter bindings
		if ( is_array($filterBindings) ) {
			$data	= array_merge($data, $filterBindings);
		}
		$this->prepare( $query, $useMaster );
		return $this->execute( $data, $useMaster );
	}

	
	/**
	 * Insert record(s) in database. The last insert ID is stored in the $insertId class variable
	 *
	 * @param string $tablename	name of table to insert into
	 * @param array $data	associative array of field/value pairs to insert, can be multi-dimensional for bulk inserts. All fields must be the same for each record for bulk inserts
	 * @param string $modifiers modifier to use in query (i.e. IGNORE). Default is no modifier
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @param string $action	type of insert to perform (INSERT, REPLACE). Default is INSERT
	 * @return int number of rows that were inserted
	 */
	public function insert($tablename, &$data, $modifiers='', $useMaster=true, $action='INSERT') {
		if(!in_array($action, array('INSERT', 'REPLACE'))){ $action = 'INSERT'; }
		$tablename	= '`'.str_replace('.', '`.`', trim($tablename, '"\'`') ).'`';

		$query		= $action . ' ' . $modifiers . ' INTO '.$tablename;
		if ( $useMaster ) {
			$dbh		=& self::$mdbh;
		} else {
			$dbh		=& $this->dbh;
		}
		$insertCnt		= 0;
		// Check for bulk insert
		if ( is_array($data[ key($data) ]) ) {
			$recCount		= count($data);
			// Get Field List
			$fields			= array_keys($data[key($data)]);
			// Create field string
			$ifields		= '`'.implode('`,`',$fields).'`';
			$batches		= ceil($recCount/$this->bulkInsertSize)."\n";
			$unprepared		= true;
			// Loop through inserts in batches
			for( $b=1; $b<=$batches; $b++ ) {
				$bindings	= array();
				$istart		= ($b-1)*$this->bulkInsertSize;
				$iend		= min($this->bulkInsertSize, $recCount-$istart);
				$idata		= array();
				$bindings	= array();
				// Create bindings
				$data_block	= array_slice($data, $istart, $this->bulkInsertSize);
				$i			= 0;
				$bcnt		= 0;
				foreach ( $data_block as $rec ) {
					foreach( $rec as $field=>$val ) {
						$idata[$field.$i]	= $val;
					}
					$bindings[]			= '(:'.implode($i.',:', $fields).$i.')';
					$bcnt +=count($fields);
					$i++;
				}
				// Check if a prepare statement needs to be run (first and last inserts)
				if ( $unprepared || $b==$batches ) {
					$pquery		= $query.' ('.$ifields.') VALUES '.implode(',',$bindings );
					$this->prepare($pquery, $useMaster);
					$unprepared	= false;
				}
				$iresult	= $this->execute($idata, $useMaster);
				if ( $iresult>0 ) {
					$insertCnt	+= $iresult;
				} else {
					return $insertCnt;
				}
			}
			$this->insertId		= $dbh->lastInsertId();

		} else {
			$fields		= array_keys($data);
			$fieldCnt	= count($data);
			$query		.= ' (`' . implode('`,`', $fields) . '`) VALUES ( :'. implode(', :',$fields) . ' )';
			$this->prepare( $query, $useMaster );
			if( $this->execute( $data, $useMaster ) == 1) {
				$insertCnt++;
				$this->insertId		= $dbh->lastInsertId();
			}
		}
		return $insertCnt;
	}
	
	/**
	 * Get the last insert if for prepare/execute statements
	 *
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int		lsat insert id
	 */
	public function getLastInsertId($useMaster=true) {
		$dbh = $useMaster ? self::$mdbh : $this->dbh;
		return $dbh->lastInsertId();
	}
	
	/**
	 * Delete records from the database
	 *
	 * @param string $query		query string to execute.
	 * @param array $bindings	values to use for binding to query
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return int		number of rows deleted
	 */
	public function delete( $tablename, $filter, $filterBindings=null, $useMaster=true, $modifiers='' ) {
		$tablename	= '`'.str_replace('.', '`.`', trim($tablename, '"\'`') ).'`';
		$query		= 'DELETE ' . $modifiers . ' FROM '.$tablename.' WHERE '.$filter;
		$this->prepare($query, $useMaster);
		return $this->execute($filterBindings, $useMaster);
	}

	/**
	 * Fetch one row of data from the database after SELECT query is run
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @param constant $method	PDO method to use for fetch records (default is PDO::FETCH_ASSOC)
	 * @return array	associative array of database record
	 */
	public function fetch( $useMaster=false, $method=null ) {
		if ( $useMaster ) {
			$sth		=& self::$msth;
		} else {
			$sth		=& $this->sth;
		}
		if ( !is_object($sth) ) {
			$this->errorCode	= 'n/a';
			$this->errorMsg		= 'Statement handle is not an object.';
			return -1;
		}
		if ( is_null($method) ) {
			$method		= \PDO::FETCH_ASSOC;
		}
		return $sth->fetch($method);
	}

	/**
	 * Fetch all rows from the database after SELECT query is run
	 * Rows can be fetched with a field as the index instead of sequential index
	 * By specifying a $keyField and $grouped, record will be returned grouped by the key field
	 *
	 * @param variable	$resultVar	variable reference to store the result in
	 * @param string $keyField	field to use as the array key
	 * @param boolean $grouped if $keyField specified, group by the key field
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @param constant $method	PDO method to use for fetch records (default is PDO::FETCH_ASSOC)
	 * @return int	1 if successful, -1 if error. Fetched data is stored in $selectResult class variable by default
	 */
	public function fetchAll( &$resultVar, $keyField=null, $grouped=false, $useMaster=false, $method=null )
	{
		if ( $useMaster ) {
			$sth		=& self::$msth;
		} else {
			$sth		=& $this->sth;
		}
		if ( !is_object($sth) ) {
			$this->errorCode	= 'n/a';
			$this->errorMsg		= 'Statement handle is not an object.';
			return -1;
		}
		if ( is_null($method) ) {
			$method		= \PDO::FETCH_ASSOC;
		}
		$resultVar		= array();
		if ( is_null($keyField) ) {
			$st			= round(microtime(true),3);
			$resultVar	= $sth->fetchAll($method);
		} else if ($grouped) {
			while ( $row = $sth->fetch($method) ) {
				$resultVar[ $row[$keyField] ][] = $row;
			}
		} else {
			while( $row = $sth->fetch($method) ) {
				$resultVar[ $row[$keyField] ]	= $row;
			}
		}
		return 1;
	}

	/**
	 * Fetch a single field from a select query. Use for when you just want
	 * a list of values, like IDs, for processing.
	 *
	 * @param string  $fieldName	field name to get
	 * @param boolean $useMaster	set to TRUE to use the master server connection
	 * @return array 	list of values fetched
	 */
	public function fetchField($fieldName, $useMaster=false) {
		if ( $useMaster ) {
			$sth		=& self::$msth;
		} else {
			$sth		=& $this->sth;
		}
		if ( !is_object($sth) ) {
			$this->errorCode	= 'n/a';
			$this->errorMsg		= 'Statement handle is not an object.';
			return -1;
		}
		while( $row = $sth->fetch(\PDO::FETCH_ASSOC) ) {
			$this->selectResult[]	= $row[ $fieldName ];
		}
		return $this->selectResult;
	}
	
	/**
	 * Retrieve the contents of the $selectResult class variable and clear it
	 * @param variable	$resultVar	variable reference to store the result in
	 * @return array	contents of $selectResult variable
	 */
	public function getResult( &$resultVar ) {
		$resultVar 	= $this->selectResult;
		$this->clearResult();
	}

	/**
	 * Clear $selectResult variable
	 */
	public function clearResult() {
		$this->selectResult	= array();
	}
		
	/**
	 * Update profiling array with query information
	 *
	 * @param string $query		query that was run
	 * @param array $bindings	data bindings used in the query
	 * @param float $qTime		time in seconds.fraction it took to run the query.
	 * @param string $result	the result of the query run (general an int)
	 * @param string $label		the label of the connection used
	 * @param mixed $debug		debugging information (i.e. EXPLAIN for a SELECT)
	 */
	private function updateProfiling( $query, $bindings, $qTime, $result, $label, $debug=null ) {
		self::$queryCntr++;
		self::$queryTime	+= $qTime;
		if ( self::$queryCntr < self::$maxProfiling ) {
			self::$profiling[]	= array('connection'=>$label, 'query'=>$query, 'bindings'=>$bindings, 'time'=>$qTime, 'result'=>$result, 'debug'=>$debug);
		} else if (self::$queryCntr == self::$maxProfiling) {
			self::$profiling[]	= 'MAX NUMBER OF PROFILING QUERIES EXCEEDED';
		}
	}
	
	/**
	 * Resets the profiling array
	 */
	public function resetProfiling() {
		self::$queryCntr = 0;
		self::$queryTime = 0;
		self::$profiling = array();
	}

	/**
	 * Retrieve profiling information
	 * @return array
	 */
	public function getProfiling() {
		$profData	= array('meta'=>
								array(
								'instances'=>self::$instanceCntr,
								'connections'=>self::$connections,
								'queries'=>self::$queryCntr,
								'time'=>self::$queryTime,
								'errors'=>self::$errors,
								),
							'queries'=>self::$profiling
							);
		return $profData;
	}

	/**
	 * Log an error that occurred on a handle (database or statement)
	 *
	 * @param object $handle	PDO handle
	 */
	private function logErrorInfo( $handle, $label='' ) {
		$errorInfo			= $handle->errorInfo();
		$this->errorCode	= $errorInfo[1];
		$this->errorMsg		= $errorInfo[2];
		if ( $this->useErrorLog ) {
			error_log('DB CLASS ERROR:('.$this->errorCode.') '.$this->errorMsg .' :: ' . $this->lastQuery . ' :: URL '.$_SERVER['REQUEST_URI']. ' M/S:'.self::$connectParamsMaster['host'].'/'.$this->connectParams['host'], 0);
		}
		self::$errorCntr++;
		// Check for too many errors
		if ( self::$errorCntr<10 ) {
			self::$errors[]		= array('connection'=>$label, 'code'=>$errorInfo[1], 'msg'=>$errorInfo[2]);
		} else if ( self::$errorCntr==10 ) {
			self::$errors[]		= array('connection'=>'n/a', 'code'=>0, 'msg'=>'Too many errors logged');			
		}
		if ( $this->debug>0 ) {
			echo 'DB ERROR: '.$errorInfo[1].'-'.$errorInfo[2]."\n";
		}
		return 1;
	}
	

}
