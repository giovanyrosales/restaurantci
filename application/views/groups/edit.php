

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Administración
        <small>Grupos</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="<?php echo base_url('groups/') ?>">Grupos</a></li>
        <li class="active">Editar</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">
          
          <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('success'); ?>
            </div>
          <?php elseif($this->session->flashdata('error')): ?>
            <div class="alert alert-error alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('error'); ?>
            </div>
          <?php endif; ?>

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Editar grupo</h3>
            </div>
            <form role="form" action="<?php base_url('groups/update') ?>" method="post">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="group_name">Nombre de Grupo</label>
                  <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Ingrese nombre de grupo" value="<?php echo $group_data['group_name']; ?>" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="permission">Permisos</label>

                  <?php $serialize_permission = unserialize($group_data['permission']); ?>
                  
                  <table class="table table-responsive table-hover">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Crear</th>
                        <th>Actualizar</th>
                        <th>Ver</th>
                        <th>Borrar</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Users</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createUser" <?php if($serialize_permission) {
                          if(in_array('createUser', $serialize_permission)) { echo "checked"; } 
                        } ?> ></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateUser" <?php 
                        if($serialize_permission) {
                          if(in_array('updateUser', $serialize_permission)) { echo "checked"; } 
                        }
                        ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewUser" <?php 
                        if($serialize_permission) {
                          if(in_array('viewUser', $serialize_permission)) { echo "checked"; }   
                        }
                        ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteUser" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteUser', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Grupos</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createGroup" <?php 
                        if($serialize_permission) {
                          if(in_array('createGroup', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateGroup" <?php 
                        if($serialize_permission) {
                          if(in_array('updateGroup', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewGroup" <?php 
                        if($serialize_permission) {
                          if(in_array('viewGroup', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteGroup" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteGroup', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Sucursales</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createStore" <?php 
                        if($serialize_permission) {
                          if(in_array('createStore', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateStore" <?php 
                        if($serialize_permission) {
                          if(in_array('updateStore', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewStore" <?php 
                        if($serialize_permission) {
                          if(in_array('viewStore', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteStore" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteStore', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Mesas</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createTable" <?php 
                        if($serialize_permission) {
                          if(in_array('createTable', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateTable" <?php 
                        if($serialize_permission) {
                          if(in_array('updateTable', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewTable" <?php 
                        if($serialize_permission) {
                          if(in_array('viewTable', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteTable" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteTable', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Categorias</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createCategory" <?php 
                        if($serialize_permission) {
                          if(in_array('createCategory', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateCategory" <?php 
                        if($serialize_permission) {
                          if(in_array('updateCategory', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewCategory" <?php 
                        if($serialize_permission) {
                          if(in_array('viewCategory', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteCategory" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteCategory', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Productos</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createProduct" <?php 
                        if($serialize_permission) {
                          if(in_array('createProduct', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateProduct" <?php 
                        if($serialize_permission) {
                          if(in_array('updateProduct', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewProduct" <?php 
                        if($serialize_permission) {
                          if(in_array('viewProduct', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteProduct" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteProduct', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Ordenes</td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="createOrder" <?php 
                        if($serialize_permission) {
                          if(in_array('createOrder', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateOrder" <?php 
                        if($serialize_permission) {
                          if(in_array('updateOrder', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewOrder" <?php 
                        if($serialize_permission) {
                          if(in_array('viewOrder', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="deleteOrder" <?php 
                        if($serialize_permission) {
                          if(in_array('deleteOrder', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                      </tr>
                      <tr>
                        <td>Reportes</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewReport" <?php 
                        if($serialize_permission) {
                          if(in_array('viewReport', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Empresa</td>
                        <td> - </td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateCompany" <?php 
                        if($serialize_permission) {
                          if(in_array('updateCompany', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Perfil</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="viewProfile" <?php 
                        if($serialize_permission) {
                          if(in_array('viewProfile', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Configuraciones</td>
                        <td> - </td>
                        <td><input type="checkbox" name="permission[]" id="permission" value="updateSetting" <?php 
                        if($serialize_permission) {
                          if(in_array('updateSetting', $serialize_permission)) { echo "checked"; }  
                        }
                         ?>></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                    </tbody>
                  </table>
                  
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="<?php echo base_url('groups/') ?>" class="btn btn-danger">Regresar</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!-- col-md-12 -->
      </div>
      <!-- /.row -->
      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script type="text/javascript">
    $(document).ready(function() {
      $('#groupMainNav').addClass('active');
      $('#manageGroupSubMenu').addClass('active');
    });
  </script>  

