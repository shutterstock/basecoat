<?php

class Tasks {
	protected static $status_opts	= null;
	protected static $category_opts	= null;
	
	public $default_status_id	= 1;
	public $default_category_id	= 1;
	public $todo_status_id		= 1;
	public $basecoat = null;

	protected $base_tasks_q	= 'SELECT tasks.*,status.name status,categories.category
			FROM tasks 
			LEFT JOIN status ON tasks.status_id=status.sid
			LEFT JOIN categories ON tasks.category_id=categories.sid ';
	
	public function __construct() {
		$this->basecoat = $GLOBALS['basecoat'];
	}
	
	public function get($id) {
		$q			= $this->base_tasks_q . ' WHERE tasks.sid= :id';
		$qresult	= $this->basecoat->db->selectOne($q, array('id'=>$id), true);
		if ( !is_array($qresult) && $qresult<0 ) {
			$this->logError($qresult.' '.\Basecoat\Core::$db->errorMsg. ' :: '.$q);
		}
		return $qresult;
	}

	public function save($task_data) {
		$iresult	= $this->basecoat->db->insert('tasks', $task_data);
		return $iresult;
	}
	
	public function update($id, $task_data) {
		$uresult	= $this->basecoat->db->update('tasks', $task_data, 'sid=:id', array('id'=>$id));
		return $uresult;
	}
	
	public function getStatusOpts() {
		// Check if we loaded data already
		if ( !is_array(self::$status_opts) ) {
			// Load options
			$q	= 'SELECT * FROM status ORDER BY order_by';
			// Run query and retrieve records (query, bindings, useMaster, fetchAll)
			$qresult	= $this->basecoat->db->select($q, null, false, true);
			if ( $qresult>=0 ) {
				self::$status_opts	= $this->basecoat->db->selectResult;
			} else {
				// Something went wrong
				$this->logError($qresult.' '.$this->basecoat->db->errorMsg. ' :: '.$q);
				return $qresult;
			}
		}
		return self::$status_opts;
	}
	
	public function getCategoryOpts() {
		// Check if we loaded data already
		if ( !is_array(self::$category_opts) ) {
			$q	= 'SELECT * FROM categories ORDER BY category';
			// Run query and retrieve records (query, bindings, useMaster, fetchAll)
			$qresult	= $this->basecoat->db->select($q, null, false, true);
			if ( $qresult>=0 ) {
				self::$category_opts	= $this->basecoat->db->selectResult;
			} else {
				// Something went wrong
				$this->logError($qresult.' '.$this->basecoat->db->errorMsg. ' :: '.$q);
				return $qresult;
			}
		}
		return self::$category_opts;
	}
	
	public function getToDo($order_by=null, $from_date=null) {
		if ( is_null($order_by) ) {
			$order_by	= ' due_date ASC, due_time DESC ';
		}
		if ( is_null($from_date) ) {
			$from_date	= time();
		} else {
			$from_date	= strtotime($from_date);
		}
		$from_date		= date('Y-m-d', $from_date );
		$q				= $this->base_tasks_q . 'WHERE due_date >= :from AND status_id= :status_id ORDER BY '.$order_by;
		$qresult		= $this->basecoat->db->select($q, array('from'=>$from_date, 'status_id'=>$this->todo_status_id));
		if ( $qresult<0 ) {
			// Something went wrong
			$this->logError($qresult.' '.$this->basecoat->db->errorMsg. ' :: '.$q);
			return $qresult;
		}
		$this->basecoat->db->fetchAll($tasks);
		return $tasks;
	}
	
	public function getPastDue($order_by=null, $from_date=null) {
		if ( is_null($order_by) ) {
			$order_by	= ' due_date ASC, due_time DESC ';
		}
		if ( is_null($from_date) ) {
			$from_date	= time();
		} else {
			$from_date	= strtotime($from_date);
		}
		$from_date		= date('Y-m-d', $from_date );
		$q				= $this->base_tasks_q . 'WHERE due_date <= :from AND status_id= :status_id ORDER BY '.$order_by;
		$qresult		= $this->basecoat->db->select($q, array('from'=>$from_date, 'status_id'=>$this->todo_status_id));
		if ( $qresult<0 ) {
			// Something went wrong
			$this->logError($qresult.' '.$this->basecoat->db->errorMsg. ' :: '.$q);
			return $qresult;
		}
		$this->basecoat->db->fetchAll($tasks);
		return $tasks;
		
	}
	
	public function getByCategory() {
		
	}
	
	public function logError($msg) {
		error_log($msg);
		echo $msg;
	}
}