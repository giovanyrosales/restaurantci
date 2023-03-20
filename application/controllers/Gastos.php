<?php 

class Gastos extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();
		
		$this->data['page_title'] = 'Users';
		$this->load->model('model_gastos');
	}

	public function index()
	{
		if(!in_array('viewUser', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		$this->render_template('gastos/index', $this->data);
	}

	public function fetchCategoryData()
	{
		if(!in_array('viewUser', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$result = array('data' => array());

		$data = $this->model_gastos->getGastosData();

		foreach ($data as $key => $value) {
			// button
			$buttons = '';

			if(in_array('updateUser', $this->permission)) {
				$buttons = '<button type="button" class="btn btn-info" onclick="editFunc('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button>';
			}

			if(in_array('deleteUser', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-danger" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}


			$result['data'][$key] = array(
				$value['desc_gasto'],
				"$ ".$value['mon_gasto'],
				$value['fec_gasto'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function create()
	{
		if(!in_array('createUser', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('desc_gasto', 'Descripcion del Gasto', 'required');
		$this->form_validation->set_rules('fec_gasto', 'Fecha', 'required');
		$this->form_validation->set_rules('mon_gasto', 'Fecha', 'required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'desc_gasto' => $this->input->post('desc_gasto'),
        		'fec_gasto' => $this->input->post('fec_gasto'),	
				'mon_gasto' => $this->input->post('mon_gasto')
        	);
		
        	$create = $this->model_gastos->create($data);
        	if($create == true) {
        		$response['success'] = true;
        		$response['messages'] = 'Succesfully created';
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Error registrando el gasto';			
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);
	}

	public function fetchGastosDataById($id = null)
	{
		if($id) {
			$data = $this->model_gastos->getGastosData($id);
			echo json_encode($data);
		}
		
	}

	public function update($id)
	{
		if(!in_array('updateUser', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_desc_gasto', 'Descripcion del Gasto', 'required');
			$this->form_validation->set_rules('edit_fec_gasto', 'Fecha', 'required');
			$this->form_validation->set_rules('edit_mon_gasto', 'Monto del gasto', 'required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'desc_gasto' => $this->input->post('edit_desc_gasto'),
        			'fec_gasto' => $this->input->post('edit_fec_gasto'),	
					'mon_gasto' => $this->input->post('edit_mon_gasto')
	        	);

	        	$update = $this->model_gastos->update($id, $data);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Actualizado correctamente';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error al intentar actualizar la inoformacion';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Error, por favor refresca la pagina y vuelve a intentar!!';
		}

		echo json_encode($response);
	}

	public function remove()
	{
		if(!in_array('deleteUser', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		
		$gasto_id = $this->input->post('gasto_id');

		$response = array();
		if($gasto_id) {
			$delete = $this->model_gastos->remove($gasto_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Borrado Correctamente";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the brand information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
	}

}