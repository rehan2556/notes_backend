<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Description of Pro_model
*
* @author asimriaz
*/

class Pro_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Get
	* Get list of records.
	* @param string $table
	* @param array|string $where (string= mixed where. i.e"name='Joe' AND status='boss' OR status='active'";)
	*  // Case1:
	//$array = array('name' => $name, 'title' => $title, 'status' => $status);
	//$this->db->where($array);
	// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
	// Case2:
	//$array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);
	//$this->db->where($array);
	// Case3:
	//$where = "name='Joe' AND status='boss' OR status='active'";
	//$this->db->where($where);
	* @param string $select
	* @param string $order_by ('title DESC, name ASC')
	* @param int $limit
	* @param int $offset
	* @return list of arrays
	*/
	function get($table, $where = '', $select = '', $order_by = '', $limit = '', $offset = 0)
	{
		$data = array();

		if (!empty($where))
		{
			$this->db->where($where);
		}

		if(!empty($select))
		{
			$this->db->select($select);
		}

		if(!empty($order_by))
		{
			$this->db->order_by($order_by);
		}

		if(!empty($limit))
		{
			$this->db->limit($limit, $offset);
		}

		$Q = $this->db->get($table);

		if ($Q->num_rows() > 0)
		{
			foreach ($Q->result_array() as $row)
			{
				$data[] = $row;
			}
			$Q->free_result();
		}
		return $data;
	}

	/**
	* Get row
	* Get sigle row as array
	* @param string $table
	* @param array|string $where (string= mixed where. i.e"name='Joe' AND status='boss' OR status='active'";)
	*  // Case1:
	//$array = array('name' => $name, 'title' => $title, 'status' => $status);
	//$this->db->where($array);
	// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
	// Case2:
	//$array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);
	//$this->db->where($array);
	// Case3:
	//$where = "name='Joe' AND status='boss' OR status='active'";
	//$this->db->where($where);
	* @param string $select
	* @return array
	*/
	function get_row($table, $where = '', $select = '')
	{
		$data = array();

		if (!empty($where))
		{
			$this->db->where($where);
		}

		if(!empty($select))
		{
			$this->db->select($select);
		}

		$Q = $this->db->get($table);

		if ($Q->num_rows() > 0)
		{
			$row = $Q->row_array();
			$data = $row;
			$Q->free_result();
		}

		return $data;
	}

	/**
	* Get single column as string
	* Insert into table
	* @param string $table
	* @param array|string $where (string= mixed where. i.e"name='Joe' AND status='boss' OR status='active'";)
	* @param string $column (Must be a single column of the table.)
	* @return int|string
	*/
	function get_single_column_as_string($table, $where, $column)
	{
		$this->db->select($column);
		$this->db->where($where);
		$Q = $this->db->get($table);
		$result = $Q->row();
		$Q->free_result();

		return $result->$column;
	}

	/**
	* Insert
	* Insert into table
	* @param string $table
	* @param array|object $data
	* @return int The insert ID number when performing database inserts.
	*/
	function insert($table, $data)
	{
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	/**
	* Get compiled insert
	* Compiles the insertion query but does not run the query. This method simply returns the SQL query as a string.
	* NOTE: This method doesn’t work for batched inserts.
	* @param string $table
	* @param array|object $data
	* @return string
	*/
	function get_compiled_insert($table, $data)
	{
		$query = $this->db->set($data)->get_compiled_insert($table);
		return $query;
	}

	/**
	* Insert batch
	* Generates an insert string based on the data you supply, and runs the query.
	* NOTE: All values are escaped automatically producing safer queries.
	* @param string $table
	* @param array|object $data
	* @return bool
	*/
	function insert_batch($table, $data)
	{
		return $this->db->insert_batch($table, $data);
	}

	/**
	* Update
	* Generates an update string and runs the query based on the data you supply.
	* NOTE: All values are escaped automatically producing safer queries.
	* @param string $table
	* @param string|array|object $where
	* @param array|object $data
	* @return bool
	*/
	function update($table, $where, $data)
	{
		$this->db->where($where);
		return $this->db->update($table, $data);
	}

	/**
	* Update batch
	* Generates an update string based on the data you supply, and runs the query.
	* NOTE: All values are escaped automatically producing safer queries.
	* @param string $table
	* @param array|object $data
	* @param string $key (key from data array, which use for where clause)
	* @return int returns the number of rows affected.
	*/
	function update_batch($table, $data, $key)
	{
		return $this->db->update_batch($table, $data, $key);
	}

	/**
	* Delete where
	* Generates a delete SQL string and runs the query.
	* @param string|array $table
	* @param array $where
	* @return bool.
	*/
	function delete_where($table, $where)
	{
		$this->db->where($where);
		return ($this->db->delete($table) ? TRUE : FALSE);
	}

	/**
	* Delete or where
	* Generates a delete SQL string and runs the query.
	* @param string|array $table
	* @param array $where
	* @return bool.
	*/
	function delete_or_where($table, $where)
	{
		$this->db->or_where($where);
		return ($this->db->delete($table) ? TRUE : FALSE);
	}

	/**
	* Select max
	* Writes a SELECT MAX(field) portion for your query.
	* @param string $table
	* @param string $column
	* @param string $alias
	* @return int.
	*/
	function select_max($table, $column, $alias = 'max_value')
	{
		$this->db->select_max($column, $alias);
		$Q = $this->db->get($table); // Produces: SELECT MAX(column) as alias FROM table
		$result = $Q->row();
		$Q->free_result();

		return $result->$alias;
	}

	/**
	* Select min
	* Writes a “SELECT MIN(field)” portion for your query.
	* @param string $table
	* @param string $column
	* @param string $alias
	* @return int.
	*/
	function select_min($table, $column, $alias = 'min_value')
	{
		$this->db->select_max($column, $alias);
		$Q = $this->db->get($table); // Produces: SELECT MIN(column) as alias FROM table
		$result = $Q->row();
		$Q->free_result();

		return $result->$alias;
	}

	/**
	* Select min
	* Writes a “SELECT AVG(field)” portion for your query.
	* @param string $table
	* @param string $column
	* @param string $alias
	* @return int.
	*/
	function select_avg($table, $column, $alias = 'avg_value')
	{
		$this->db->select_max($column, $alias);
		$Q = $this->db->get($table); // Produces: SELECT AVG(column) as alias FROM table
		$result = $Q->row();
		$Q->free_result();

		return $result->$alias;
	}

	/**
	* Select sum
	* Writes a “SELECT SUM(field)” portion for your query.
	* @param string $table
	* @param string $column
	* @param string $alias
	* @return int.
	*/
	function select_sum($table, $column, $where = '', $alias = 'sum_value')
	{
		if (!empty($where))
		{
			$this->db->where($where);
		}

		$this->db->select_sum($column, $alias);

		$Q = $this->db->get($table); // Produces: SELECT SUM(column) as alias FROM table
		$result = $Q->row();
		$Q->free_result();

		return $result->$alias;
	}

	/**
	* Truncate
	* Generates a truncate SQL string and runs the query.
	* NOTE: If the TRUNCATE command isn’t available, truncate() will execute as “DELETE FROM table”.
	* @param string|array $table
	* @return bool.
	*/
	function truncate($table)
	{
		return ($this->db->truncate($table) ? TRUE : FALSE);
	}

	/**
	* Count all
	* Permits you to determine the number of rows in a particular table.
	* @param string $table
	* @return int.
	*/
	function count_all($table)
	{
		return $this->db->count_all($table);
	}

	/**
	* Count all results
	* Permits you to determine the number of rows in a particular Active Record query. Queries will accept Query Builder restrictors such as where(), or_where(), like(), or_like(), etc.
	* @param string $table
	* @param array $where
	* @return int.
	*/
	function count_all_results($table, $where)
	{
		$this->db->where($where);
		return $this->db->count_all_results($table);
	}

	/**
	* Join
	* Permits you to write the JOIN portion of your query:
	* @param string $table1
	* @param array|string $where (string= mixed where. i.e"name='Joe' AND status='boss' OR status='active'";)
	*  // Case1:
	//$array = array('name' => $name, 'title' => $title, 'status' => $status);
	//$this->db->where($array);
	// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
	// Case2:
	//$array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);
	//$this->db->where($array);
	// Case3:
	//$where = "name='Joe' AND status='boss' OR status='active'";
	//$this->db->where($where);
	* @param string $select - Select part of the query
	* @param array $joins - keys are 'table', 'condition', 'type' (Type options are: left, right, outer, inner, left outer, and right outer.)
	* @param string $order_by ('title DESC, name ASC')
	* @param int $limit
	* @param int $offset
	* @return list of arrays
	*/
	function join($table1, $where = '', $select = '', $joins = '', $order_by = '', $limit = '', $offset = 0)
	{
		$data = array();

		//$this->db->from($table1);
		if(!empty($select))
		{
			$this->db->select($select);
		}
		if(!empty($joins))
		{
			foreach($joins as $k=>$j)
			{
				$this->db->join($j['table'], $j['condition'], $j['type']);
			}
		}
		if (!empty($where))
		{
			$this->db->where($where);
		}
		if(!empty($order_by))
		{
			$this->db->order_by($order_by);
		}
		if(!empty($limit))
		{
			$this->db->limit($limit, $offset);
		}

		$Q = $this->db->get($table1);
		if ($Q->num_rows() > 0)
		{
			foreach ($Q->result_array() as $row)
			{
				$data[] = $row;
			}
			$Q->free_result();
		}
		return $data;
	}

	function get_from_table_where_wherein_limit_join_orderby($table, $where_array = NULL, $where_in = NULL, $limit = NULL, $join_table = NULL, $join_on = NULL, $order_by = NULL)
	{
		//this function will cover all queries which have join, where_in, order OR limit conditions
	}
}
