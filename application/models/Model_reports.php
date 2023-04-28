<?php 

class Model_reports extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/*getting the total months*/
	private function months()
	{
		return array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	}

	/* getting the year of the orders */
	public function getOrderYear()
	{
		$sql = "SELECT * FROM orders WHERE paid_status = ?";
		$query = $this->db->query($sql, array(1));
		$result = $query->result_array();
		
		$return_data = array();
		foreach ($result as $k => $v) {
			$date = date('Y', $v['date_time']);
			$return_data[] = $date;
		}

		$return_data = array_unique($return_data);

		return $return_data;
	}

	/* getting the year of the gastos */
	public function getGastoYear()
	{
		$sql = "SELECT * FROM gastos";
		$query = $this->db->query($sql, array(1));
		$result = $query->result_array();
		
		$return_data = array();
		foreach ($result as $k => $v) {
			$date = date('Y', strtotime($v['fec_gasto']));
			$return_data[] = $date;
		}

		$return_data = array_unique($return_data);

		return $return_data;
	}

	// getting the order reports based on the year and moths
	public function getOrderData($year)
	{	
		if($year) {
			$months = $this->months();
			
			$sql = "SELECT * FROM orders WHERE paid_status = ?";
			$query = $this->db->query($sql, array(1));
			$result = $query->result_array();

			$final_data = array();
			foreach ($months as $month_k => $month_y) {
				$get_mon_year = $year.'-'.$month_y;	

				$final_data[$get_mon_year][] = '';
				foreach ($result as $k => $v) {
					$month_year = date('Y-m', $v['date_time']);

					if($get_mon_year == $month_year) {
						$final_data[$get_mon_year][] = $v;
					}
				}
			}	

			return $final_data;
		}
	}

	// getting los gastos reports based on the year and months
	public function getGastoData($year)
	{	
		if($year) {
			$months = $this->months();
			
			$sql = "SELECT * FROM gastos";
			$query = $this->db->query($sql, array(1));
			$result = $query->result_array();

			$final_data = array();
			foreach ($months as $month_k => $month_y) {
				$get_mon_year = $year.'-'.$month_y;	

				$final_data[$get_mon_year][] = '';
				foreach ($result as $k => $v) {
					$month_year = date('Y-m', strtotime($v['fec_gasto']));

					if($get_mon_year == $month_year) {
						$final_data[$get_mon_year][] = $v;
					}
				}
			}	

			return $final_data;
		}
	}

	public function getStoreWiseOrderData($year, $store)
	{
		if($year && $store) {
			$months = $this->months();
			
			$sql = "SELECT * FROM orders WHERE paid_status = ? AND store_id = ?";
			$query = $this->db->query($sql, array(1, $store));
			$result = $query->result_array();

			$final_data = array();
			foreach ($months as $month_k => $month_y) {
				$get_mon_year = $year.'-'.$month_y;	

				$final_data[$get_mon_year][] = '';
				foreach ($result as $k => $v) {
					$month_year = date('Y-m', $v['date_time']);

					if($get_mon_year == $month_year) {
						$final_data[$get_mon_year][] = $v;
					}
				}
			}	
			
			return $final_data;
		}
	}

		/* ventas en un periodo */
		public function getOrderByDate($fecha1, $fecha2)
		{
			$sql = "SELECT o.bill_no, oi.qty, oi.product_id, sum(oi.amount) as total FROM orders o JOIN order_items oi on o.id = oi.order_id where date_time BETWEEN ".$fecha1." and ".$fecha2." GROUP BY oi.product_id";
			$query = $this->db->query($sql);
			return $query->result_array();
			
		}
		/* gastos en un periodo */
		public function getGastosByDate($fecha1, $fecha2)
		{
			$sql = "SELECT * FROM gastos WHERE fec_gasto between '".$fecha1."' and '".$fecha2."'";
			$query = $this->db->query($sql);
			return $query->result_array();
		}
}