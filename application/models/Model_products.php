<?php 

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_users');
	}

	/* get the product data */
	public function getProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM products where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}	

		$user_id = $this->session->userdata('id');
		if($user_id == 1) {
			$sql = "SELECT * FROM products ORDER BY id DESC";
			$query = $this->db->query($sql);
			return $query->result_array(); 
		}
		else {
			
			$user_data = $this->model_users->getUserData($user_id);
			$sql = "SELECT * FROM products ORDER BY id DESC";
			$query = $this->db->query($sql);

			$data = array();
			foreach ($query->result_array() as $k => $v) {
				$store_ids = json_decode($v['store_id']);
				if(in_array($user_data['store_id'], $store_ids)) {
					$data[] = $v;
				}
			}

			return $data;		
		}
	}

	/* get the product data */
	public function getProductDataByCat($cat_id = null)
	{
		if($cat_id) {

			$user_id = $this->session->userdata('id');
			if($user_id == 1) {
				$sql = "SELECT * FROM products ORDER BY id DESC";
				$query = $this->db->query($sql);
				$result = array();
				foreach($query->result_array() as $key => $value) {
					$category_ids = json_decode($value['category_id']);
					if(in_array($cat_id, $category_ids)) {
						$result[] = $value;
					}
				} 

				return $result;
			}
			else {

				// for store users 
				$user_data = $this->model_users->getUserData($user_id);

				$sql = "SELECT * FROM products ORDER BY id DESC";
				$query = $this->db->query($sql);

				$data = array();
				foreach ($query->result_array() as $k => $v) {
					$store_ids = json_decode($v['store_id']);
					$category_ids = json_decode($v['category_id']);
					if(in_array($cat_id, $category_ids) && in_array($user_data['store_id'], $store_ids)) {
						$data[] = $v;
					}
				}

				return $data;		


			}
		}	
	}

	public function getActiveProductData()
	{
		$user_id = $this->session->userdata('id');

		if($user_id == 1) {
			$sql = "SELECT * FROM products WHERE active = ? ORDER BY id DESC";
			$query = $this->db->query($sql, array(1));
			return $query->result_array();
		}
		else {
			$this->load->model('model_users');
			$user_data = $this->model_users->getUserData($user_id);
			$sql = "SELECT * FROM products WHERE active = ? ORDER BY id DESC";
			$query = $this->db->query($sql, array(1));

			$data = array();
			foreach ($query->result_array() as $k => $v) {
				$store_ids = json_decode($v['store_id']);
				if(in_array($user_data['store_id'], $store_ids)) {
					$data[] = $v;
				}
			}

			return $data;			
		}

		
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('products', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
	}

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

}