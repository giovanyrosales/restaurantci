<?php  

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller 
{	
	public function __construct()
	{
		parent::__construct();
		$this->data['page_title'] = 'Reports';
		$this->load->model('model_reports');
		$this->load->model('model_stores2');
		$this->load->model('model_products');
	}

	/* 
    * It redirects to the report page
    * and based on the year, all the orders data are fetch from the database.
    */
	public function index()
	{
		if(!in_array('viewReport', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
		
		$today_year = date('Y');

		if($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		$order_data = $this->model_reports->getOrderData($today_year);
		$this->data['report_years'] = $this->model_reports->getOrderYear();
		
		//agregue para gastos
		$gasto_data = $this->model_reports->getGastoData($today_year);
		$this->data['report_years'] = $this->model_reports->getGastoYear();
		

		$final_order_data = array();
		foreach ($order_data as $k => $v) {
			
			if(count($v) > 1) {
				$total_amount_earned = array();
				foreach ($v as $k2 => $v2) {
					if($v2) {
						$total_amount_earned[] = $v2['net_amount'];						
					}
				}
				$final_order_data[$k] = array_sum($total_amount_earned);	
			}
			else {
				$final_order_data[$k] = 0;	
			}
			
		}
		$final_gasto_data = array();
		foreach ($gasto_data as $k => $v) {
			
			if(count($v) > 1) {
				$total_amount_earned = array();
				foreach ($v as $k2 => $v2) {
					if($v2) {
						$total_amount_earned[] = $v2['mon_gasto'];						
					}
				}
				$final_gasto_data[$k] = array_sum($total_amount_earned);	
			}
			else {
				$final_gasto_data[$k] = 0;	
			}
			
		}
		
		$this->data['selected_year'] = $today_year;
		$this->data['company_currency'] = $this->company_currency();
		$this->data['results'] = $final_order_data;
		$this->data['results2'] = $final_gasto_data;

		$this->render_template('reports/index', $this->data);
	}

	public function storewise()
	{

		if(!in_array('viewReport', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		$today_year = date('Y');


		$store_data = $this->model_stores2->getStoresData();
		

		$store_id = $store_data[0]['id'];

		if($this->input->post('select_store')) {
			$store_id = $this->input->post('select_store');
		}

		if($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		$order_data = $this->model_reports->getStoreWiseOrderData($today_year, $store_id);
		$this->data['report_years'] = $this->model_reports->getOrderYear();
		

		$final_parking_data = array();
		foreach ($order_data as $k => $v) {
			
			if(count($v) > 1) {
				$total_amount_earned = array();
				foreach ($v as $k2 => $v2) {
					if($v2) {
						$total_amount_earned[] = $v2['net_amount'];						
					}
				}
				$final_parking_data[$k] = array_sum($total_amount_earned);	
			}
			else {
				$final_parking_data[$k] = 0;	
			}
			
		}

		$this->data['selected_store'] = $store_id;
		$this->data['store_data'] = $store_data;
		$this->data['selected_year'] = $today_year;
		$this->data['company_currency'] = $this->company_currency();
		$this->data['results'] = $final_parking_data;
		
		$this->render_template('reports/storewise', $this->data);
	}

	public function printRep($fechas)
	{
		if(!in_array('viewOrder', $this->permission)) {
          	redirect('dashboard', 'refresh');
  		}
        
		if($fechas) {
			$hoy = date("Y-m-d H:i:s");   
			$porciones = explode("%", $fechas);
			$date1 = strtotime($porciones[0]);
			$date2 = strtotime($porciones[1]);

			$date11 = date('Y-m-d',$date1);
			$date22 = date('Y-m-d',$date2);		

			$date111 = date('d-m-Y',$date1);
			$date222 = date('d-m-Y',$date2);	
			
			//agregue para gastos
			$gasto_data = $this->model_reports->getGastosByDate($date11, $date22);

			//ventas
			$order_data = $this->model_reports->getOrderByDate($date1, $date2);

			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Recibo</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
			  <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">
			  <style>
			  @charset "utf-8";
					/* CSS Document */

					@media print {
					
					@page {
						size: 8in 11in;
					
						}
					p {
						font-size: 11pt;
						}
					}
			  </style>
			</head>
			<body onload="window.print();">
			
			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
			          '."".'
			          <small >Fecha: '.$hoy.'</small>
			        </h2>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-xs-12 ">
				  	<b>Restaurante El Gavilán</b><br>
					<b>Reporte de Ingresos y gastos para el periodo:</b><br>
			        <b>Fecha Inicio: </b> '.$date111.'<br>
			        <b>Fecha Fin: </b> '.$date222.'<br>
					<br>
					<br>
					<br>
					<div class="col-xs-4">
					<center><b>Ventas:</b></center>
					</div>
					<div class="col-xs-4" style = "float:right;">
					<center><b>Gastos:</b></center>
					</div>
			      </div>
			      <!-- /.col -->
			    </div>
				

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-6 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
					    <th>Cantidad</th>
			            <th>Descripción</th>
			            <th>Total</th>
			          </tr>
			          </thead>
			          <tbody>'; 
					  $total_ventas = 0.0;
			          foreach ($order_data as $k => $v) {

			          	$product_data = $this->model_products->getProductData($v['product_id']); 
			          	
			          	$html .= '<tr>
						  	<td>'.$v['qty'].'</td>
				            <td>'.$product_data['name'].'</td>
				            <td>$'.$v['total'].'</td>
			          	</tr>';
						  $total_ventas = $total_ventas + floatval($v['total']);
			          }
			          
			          $html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
				  <div class="col-xs-6 table-responsive" style ="float: right;">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Descripción</th>
			            <th>Total</th>
			          </tr>
			          </thead>
			          <tbody>'; 
					  $total_gastos = 0.0;
			          foreach ($gasto_data as $k => $v) {
			          	$html .= '<tr>
				            <td>'.$v['desc_gasto'].'</td>
				            <td>$'.$v['mon_gasto'].'</td>
			          	</tr>';
						  $total_gastos = $total_gastos + floatval($v['mon_gasto']);
			          }
			          
			          $html .= '</tbody>
			        </table>
			      </div>
				  <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <div class="row">
				<br><br><br>
			      <div class="col-xs-12 ">

			        <div class="table-responsive">
			          <table class="table" >
			            <tr>
			              <th>Total Ventas:</th>
			              <td>$'.$total_ventas.'</td>
			            </tr>
						<tr>
			              <th>Total Gastos:</th>
			              <td>$'.$total_gastos.'</td>
			            </tr>
						<tr>
			              <th>Saldo:</th>
			              <td>$'.floatval($total_ventas - $total_gastos).'</td>
			            </tr>
			          </table>
					  <br><br><br><br><br><br>
			        </div>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			  echo $html;
		}
	}
}	