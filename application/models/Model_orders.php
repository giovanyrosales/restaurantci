<?php 

class Model_orders extends CI_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tables');
		$this->load->model('model_users');
		$this->load->model('model_category');
	}

	/* get the orders data */
	public function getOrdersData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM orders WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$user_id = $this->session->userdata('id');
		if($user_id == 1) {
			$sql = "SELECT * FROM orders ORDER BY id DESC";
			$query = $this->db->query($sql);
			return $query->result_array();
		}
		else {
			$user_data = $this->model_users->getUserData($user_id);
			$sql = "SELECT * FROM orders WHERE store_id = ? ORDER BY id DESC";
			$query = $this->db->query($sql, array($user_data['store_id']));
			return $query->result_array();	
		}
	}

	// get the orders item data
	public function getOrdersItemData($order_id = null)
	{
		if(!$order_id) {
			return false;
		}

		$sql = "SELECT * FROM order_items WHERE order_id = ?";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}
	// get the orders item data
	public function getOrdersItemDataNotCompleted($order_id = null)
	{
		if(!$order_id) {
			return false;
		}

		$sql = "SELECT * FROM order_items WHERE order_id = ? and completo = '0'";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}

	public function create()
	{
		$user_id = $this->session->userdata('id');
		// get store id from user id 
		$user_data = $this->model_users->getUserData($user_id);
		$store_id = $user_data['store_id'];

		$bill_no = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    	$data = array(
    		'bill_no' => $bill_no,
    		'date_time' => strtotime(date('Y-m-d h:i:s a')),
    		'gross_amount' => $this->input->post('gross_amount_value'),
    		'service_charge_rate' => $this->input->post('service_charge_rate'),
    		'service_charge_amount' => ($this->input->post('service_charge_value') > 0) ?$this->input->post('service_charge_value'):0,
    		'vat_charge_rate' => $this->input->post('vat_charge_rate'),
    		'vat_charge_amount' => ($this->input->post('vat_charge_value') > 0) ? $this->input->post('vat_charge_value') : 0,
    		'net_amount' => $this->input->post('net_amount_value'),
    		'discount' => $this->input->post('discount'),
			'notas' => $this->input->post('notas'),
			'estado' => 1,
    		'paid_status' => 2,
    		'user_id' => $user_id,
    		'table_id' => $this->input->post('table_name'),
    		'store_id' => $store_id
    	);

		$insert = $this->db->insert('orders', $data);
		$order_id = $this->db->insert_id();

		$count_product = count($this->input->post('product'));
    	for($x = 0; $x < $count_product; $x++) {
    		$items = array(
    			'order_id' => $order_id,
    			'product_id' => $this->input->post('product')[$x],
    			'qty' => $this->input->post('qty')[$x],
    			'rate' => $this->input->post('rate_value')[$x],
    			'amount' => $this->input->post('amount_value')[$x]
    		);

    		$this->db->insert('order_items', $items);
    	}

    	// update the table status
    	$this->load->model('model_tables');
    	$this->model_tables->update($this->input->post('table_name'), array('available' => 2));

		return ($order_id) ? $order_id : false;
	}

	public function countOrderItem($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM order_items WHERE order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}

	public function update($id)
	{
		if($id) {
			$user_id = $this->session->userdata('id');
			$user_data = $this->model_users->getUserData($user_id);
			$store_id = $user_data['store_id'];
			// update the table info

			$order_data = $this->getOrdersData($id);
			$data = $this->model_tables->update($order_data['table_id'], array('available' => 1));

			if($this->input->post('paid_status') == 1) {
	    		$this->model_tables->update($this->input->post('table_name'), array('available' => 1));	
	    	}
	    	else {
	    		$this->model_tables->update($this->input->post('table_name'), array('available' => 2));	
	    	}

			$data = array(
	    		'gross_amount' => $this->input->post('gross_amount_value'),
	    		'service_charge_rate' => $this->input->post('service_charge_rate'),
	    		'service_charge_amount' => ($this->input->post('service_charge_value') > 0) ?$this->input->post('service_charge_value'):0,
	    		'vat_charge_rate' => $this->input->post('vat_charge_rate'),
	    		'vat_charge_amount' => ($this->input->post('vat_charge_value') > 0) ? $this->input->post('vat_charge_value') : 0,
	    		'net_amount' => $this->input->post('net_amount_value'),
	    		'discount' => $this->input->post('discount'),
				'notas' => $this->input->post('notas'),
	    		'paid_status' => $this->input->post('paid_status'),
	    		'user_id' => $user_id,
	    		'table_id' => $this->input->post('table_name'),
	    		'store_id' => $store_id
	    	);

			$this->db->where('id', $id);
			$update = $this->db->update('orders', $data);

			// now remove the order item data 
			$this->db->where('order_id', $id);
			$this->db->delete('order_items');
			$newqty = 0;
			$count_product = count($this->input->post('product'));
	    	for($x = 0; $x < $count_product; $x++) {
	    		$items = array(
	    			'order_id' => $id,
	    			'product_id' => $this->input->post('product')[$x],
	    			'qty' => $this->input->post('qty')[$x],
	    			'rate' => $this->input->post('rate_value')[$x],
	    			'amount' => $this->input->post('amount_value')[$x],
					'completo' => $this->input->post('completo_value')[$x],
	    		);
				//Restar cantidad de los productos si es cerveza
				if($data['paid_status'] == 1){
					$product_data = $this->model_products->getProductDataAll($this->input->post('product')[$x]);
							//decode porq en la db esta guardado como arreglo
							$category_ids = json_decode($product_data['category_id']);
							$store_name = array();
							//recorro el arreglo de la base
							foreach ($category_ids as $k => $v) {
								//capturo el nombre de la categoria
								$store_data = $this->model_category->getCategoryData($v);
								$store_name[] = $store_data['name'];
							}
							//Extraigo el nombre de la categoria mejor
							$store_name = implode(', ', $store_name);
								//si es cerveza
						if($store_name == 'Cerveza' ){
								//calculo la nueva cantidad
							$newqty = intval($product_data['cantidad']) - intval($this->input->post('qty')[$x]);
							//ACTUALIZO LA CANTIDAD
							$updatedata = array('cantidad' => $newqty);
							$this->model_products->updateQty($updatedata, $this->input->post('product')[$x]);
						}
				}

	    		$this->db->insert('order_items', $items);
	    	}

	    	
	    	

			return true;
		}
	}

	public function updateEstado($id)
	{
		if($id) {
			// update the table info

			$order_data = $this->getOrdersData($id);
			$data = $this->model_tables->update($order_data['table_id'], array('available' => 1));

				$data = array(
	    		'estado' => 2
	    	);

			$this->db->where('id', $id);
			$update = $this->db->update('orders', $data);
			$this->db->where('order_id', $id);
			$update = $this->db->update('order_items', array('completo' => 1));

			

			return true;
		}
	}


	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('orders');

			$this->db->where('order_id', $id);
			$delete_item = $this->db->delete('order_items');
			return ($delete == true && $delete_item) ? true : false;
		}
	}

	public function countTotalPaidOrders()
	{
		$sql = "SELECT * FROM orders WHERE paid_status = ?";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}

	public function countTotalOrders()
	{
		$sql = "SELECT * FROM orders";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}

	public function countTotalUnPaidOrders()
	{
		$sql = "SELECT * FROM orders WHERE paid_status = 2";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}

}