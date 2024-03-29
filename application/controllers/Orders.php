<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller 
{
	var $currency_code = '';

	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Orders';

		$this->load->model('model_orders');
		$this->load->model('model_tables');
		$this->load->model('model_products');
		$this->load->model('model_company');
		$this->load->model('model_stores');

		$this->currency_code = $this->company_currency();
	}

	/* 
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Manage Orders';
		$this->render_template('orders/index', $this->data);		
	}

	/*
	* Fetches the orders data from the orders table 
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$result = array('data' => array());

		$data = $this->model_orders->getOrdersData();
		

		foreach ($data as $key => $value) {

			$store_data = $this->model_stores->getStoresData($value['store_id']);
			$table_data = $this->model_tables->getTableData($value['table_id']);

			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);

			$date_time = $date . ' ' . $time;

			// button
			$buttons = '';

			if(in_array('viewOrder', $this->permission)) {
				$buttons .= ' <a target="__blank" href="'.base_url('orders/printDiv/'.$value['id']).'" class="btn btn-success"><i class="fa fa-credit-card"></i></a>';
			}
			if(in_array('viewOrder', $this->permission)) {
				$buttons .= ' <a target="__blank" href="'.base_url('orders/printDivCoc/'.$value['id']).'" class="btn btn-warning"><i class="fa fa-print"></i></a>';
			}
			if($value['paid_status'] == 1) {
				$buttons .= '';
			}else{
				if(in_array('updateOrder', $this->permission)) {
					$buttons .= ' <a href="'.base_url('orders/update/'.$value['id']).'" class="btn btn-info"><i class="fa fa-pencil"></i></a>';
				}
			}
			if(in_array('deleteOrder', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-danger" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}
			if(in_array('updateOrder', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-success" onclick="updateEstado('.$value['id'].')" data-toggle="modal" data-target="#updateOrderModal"><i class="fa fa-bell"></i></button>';
			}

			if($value['paid_status'] == 1) {
				$paid_status = '<span class="label label-success">Pagada</span>';	
			}
			else {
				$paid_status = '<span class="label label-danger">Sin Pagar</span>';
			}
			//if($value['estado'] == 1) {
			$pendiente  = $this->model_orders->getOrdersItemDataNotCompleted($value['id']);
			if(count($pendiente) != 0) {
				$estado = '<span class="label label-warning">Pendiente</span>';	
			}
			else {
				$estado = '<span class="label label-success">Listo</span>';
			}

			$result['data'][$key] = array(
				//$value['bill_no'],
				$table_data['table_name'],
				$value['notas'],
				$count_total_item,
				$value['net_amount'],
				//$paid_status,
				$estado,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Add Order';

		$this->form_validation->set_rules('product[]', 'Nombre del Producto', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	
        	
        	$order_id = $this->model_orders->create();
        	
        	if($order_id) {
        		$this->session->set_flashdata('success', 'Orden Creada');
        		redirect('orders/update/'.$order_id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error Ocurrio!!');
        		redirect('orders/create/', 'refresh');
        	}
        }
        else {
            // false case
            $this->data['table_data'] = $this->model_tables->getActiveTable();
        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
        	$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

        	$this->data['products'] = $this->model_products->getActiveProductData();      	

            $this->render_template('orders/create', $this->data);
        }	
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the particular product data from the product id 
	* and return the data into the json format.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	/*
	* It gets the all the active product inforamtion from the product table 
	* This function is used in the order page, for the product selection in the table
	* The response is return on the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	/*
	* If the validation is not valid, then it redirects to the edit orders page 
	* If the validation is successfully then it updates the data into the database 
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}



		$this->data['page_title'] = 'Actualizar Orden';

		$this->form_validation->set_rules('product[]', 'Producto', 'trim|required');
		
	
        if ($this->form_validation->run() == TRUE) {        	

        	$update = $this->model_orders->update($id);
        	
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Actualizada');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error Ocurrio!!');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        }
        else {
            // false case
        	$this->data['table_data'] = $this->model_tables->getActiveTable();

        	$company = $this->model_company->getCompanyData(1);
        	$this->data['company_data'] = $company;
        	$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
        	$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

        	$result = array();
        	$orders_data = $this->model_orders->getOrdersData($id);

        	if(empty($orders_data)) {
        		$this->session->set_flashdata('errors', 'No existe');
        		redirect('orders', 'refresh');
        	}

    		$result['order'] = $orders_data;
    		$orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);

    		foreach($orders_item as $k => $v) {
    			$result['order_item'][] = $v;
    		}

    		$table_id = $result['order']['table_id'];
    		$table_data = $this->model_tables->getTableData($table_id);

    		$result['order_table'] = $table_data;

    		$this->data['order_data'] = $result;

        	$this->data['products'] = $this->model_products->getActiveProductData();      	

        	

            $this->render_template('orders/edit', $this->data);
        }
	}
	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function updateorderestatus()
	{

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $updateestado = $this->model_orders->updateEstado($order_id);
            if($updateestado == true) {
                $response['success'] = true;
                $response['messages'] = "Estado Actualizado Exitosamente"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error al cambiar estado";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refresca e intenta de nuevo!!";
        }

        echo json_encode($response); 
	}
	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $delete = $this->model_orders->remove($order_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Borrado Exitosamente"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the product information";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refresca e intenta de nuevo!!";
        }

        echo json_encode($response); 
	}

	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDiv($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
          	redirect('dashboard', 'refresh');
  		}
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);
			$store_data = $this->model_stores->getStoresData($order_data['store_id']);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Pagado" : "Sin Pagar";
			$user_data = $this->model_users->getUserData($order_data['user_id']);
			$table_data = $this->model_tables->getTableData($order_data['table_id']);

			if ($order_data['discount'] > 0) {
				$discount = $this->currency_code . ' ' .$order_data['discount'];
			}
			else {
				$discount = '0';
			}


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
						size: 3in 8in;
					
						}
					p, body , table {
						font-size: 7pt;
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
			        <center><h2 class="page-header">
			          '.$company_info['company_name'].'
					  <small >Tel. +503 7563-3936</small>
			          <small >Fecha: '.$order_date.'</small>
					  <small >Hora: '.date("H:i:s").'</small>
			        </h2></center>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-12 invoice-col">
			        <b>Recibo: </b> '.$order_data['bill_no'].'<br>
					<b> Mesero: </b> '.$user_data['username'].'
			        <b> Cliente: </b> 
					
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped" border="1">
			          <thead>
			          <tr>
					    <th>Qty</th>
			            <th>Descripción</th>
			            <th>Total</th>
			          </tr>
			          </thead>
			          <tbody>'; 

			          foreach ($orders_items as $k => $v) {

			          	$product_data = $this->model_products->getProductData($v['product_id']); 
			          	
			          	$html .= '<tr>
						  	<td>'.$v['qty'].'</td>
				            <td>'.$product_data['name'].'</td>
				            <td>'.$this->currency_code . ' ' .$v['amount'].'</td>
			          	</tr>';
			          }
			          
			          $html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <div class="row">
			      
			      <div class="col-xs-12 ">

			        <div class="table-responsive">
			          <table class="table" border = "1">
			            <tr>
			              <th style="width:50%">Sub Total:</th>
			              <td>'.$this->currency_code . ' ' .$order_data['gross_amount'].'</td>
			            </tr>';

			            if($order_data['service_charge_amount'] > 0) {
			            	$html .= '<tr>
				              <th>Propina ('.$order_data['service_charge_rate'].'%)</th>
				              <td>'.$this->currency_code .' '.$order_data['service_charge_amount'].'</td>
				            </tr>';
			            }

			            if($order_data['vat_charge_amount'] > 0) {
			            	$html .= '<tr>
				              <th>IVA ('.$order_data['vat_charge_rate'].'%)</th>
				              <td>'.$this->currency_code .' '.$order_data['vat_charge_amount'].'</td>
				            </tr>';
			            }
			            
			            
			            $html .=' 
			            <tr>
			              <th>Total:</th>
			              <td>'.$this->currency_code . ' ' .$order_data['net_amount'].'</td>
			            </tr>
			            <tr>
			              <th>Estado:</th>
			              <td>'.$paid_status.'</td>
			            </tr>
			          </table>
					  <br><br>
					  <center>Muchas gracias por su visita!</center>
					  <br><br><br><br>
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

/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDivCoc($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
          	redirect('dashboard', 'refresh');
  		}
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemDataNotCompleted($id);
			$company_info = $this->model_company->getCompanyData(1);
			$store_data = $this->model_stores->getStoresData($order_data['store_id']);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Pagado" : "Sin Pagar";

			$table_data = $this->model_tables->getTableData($order_data['table_id']);

			if ($order_data['discount'] > 0) {
				$discount = $this->currency_code . ' ' .$order_data['discount'];
			}
			else {
				$discount = '0';
			}


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
						size: 3in 8in;
					
						}
					p {
						font-size: 9pt;
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
			          '.$company_info['company_name'].'
			        </h2>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-12 invoice-col">
			        <b>Orden de: </b> '.$table_data['table_name'].'<br>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
					    <th>Cantidad</th>
			            <th>Descripción</th>
			        
			          </tr>
			          </thead>
			          <tbody>'; 

			          foreach ($orders_items as $k => $v) {

			          	$product_data = $this->model_products->getProductData($v['product_id']); 
			          	
			          	$html .= '<tr>
						  	<td>'.$v['qty'].'</td>
				            <td>'.$product_data['name'].'</td>
			          	</tr>';
			          }
			          
			          $html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-12 invoice-col">
			        <b>Notas: </b> '.$order_data['notas'].'<br>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
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