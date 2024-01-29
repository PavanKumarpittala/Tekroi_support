@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <h4 style="color:#A04000">Users List</h4>

         <div class="row cust_data_form">
                <div class="col-md-3">
                  <div class="form-group">
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="branch_count">
                      <option selected="selected">10</option>
                      <option>25</option>
                      <option>50</option>
                      <option>100</option>
                    </select>
                  </div>
                </div>
                  <div class="col-md-3"></div>

                <!-- <div class="col-md-8"></div> -->
                <div class="col-md-3 margin pull-right no-m-top">
                           <div class="input-group">
                     <input type="text" class="form-control no-border-right" id="search_user" placeholder="Search...">
                    <div class="input-group-addon">
                      <i class="fa fa-search sear"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 pull-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#user" type="submit">Add User</button>
                </div>
          </div>
       </div>
  </div>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">

                  <div class="table_data" style="width: 100%;overflow: hidden;">
                  <table id="branch_table" class="table table-striped table-bordered nowrap" style="overflow-x: auto;display: block;">
                    <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Employee Id</th>
                        <th>Employee Name</th>
                        <th>Employee Email</th>
                        <th>Employee Mobile</th>
                        <th>Employee Role</th>
                        <th>Status</th>
                        <th>Update Details</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
   </div>
<!-- sidemenu close divs-->
</div>
</div>
</div>
</main>

<div class="modal fade" id="user">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Employee Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form id="add_employee" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
          <label for="employee_id" class="required">Employee Id:</label>
          <input type="text" class="form-control" id="employee_id" name="employee_id">
        </div>
         <div class="form-group">
          <label for="employee_name" class="required">Employee Name:</label>
          <input type="text" class="form-control" id="employee_name" name="employee_name">
        </div>
         <div class="form-group">
          <label for="employee_email" class="required">Employee Email:</label>
          <input type="email" class="form-control" id="employee_email" name="employee_email">
        </div>
        <div class="form-group">
          <label for="employee_password" class="required">Employee Password:</label>
          <input type="text" class="form-control" id="employee_password" name="employee_password">
        </div>
        <div class="form-group">
          <label for="employee_mobile" class="required">Employee mobile No:</label>
          <input type="text" class="form-control" id="employee_mobile" name="employee_mobile">
        </div>
        <div class="form-group">
          <label for="employee_role" class="required">Employee Role:</label>
          <!-- <input type="text" class="form-control" id="employee_role" name="employee_role"> -->
          <select name="employee_role" id="employee_role" class="form-control">
               <option value="" disabled selected>Select Employee Role</option>
               <option value="1">Admin</option>
               <option value="2">Manager</option>
               <option value="3">Employee</option>
               <option value="4">Client</option>
            </select>
        </div>
        <div class="form-group">
          <label for="support_domain">Support Domain:</label>
          <input type="text" class="form-control" id="support_domain" name="support_domain">
        </div>
        <button type="submit" class="btn btn-primary" name="button">Submit</button>
      </form>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>

<!-- Edit -->
  <div class="modal fade" id="update_user">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Update User Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form id="update_users" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
           <input type="hidden" name="user_hidden" id="user_hidden">
         <div class="form-group">
          <label for="update_name">Employee Name:</label>
          <input type="text" class="form-control" id="update_name" name="update_name">
        </div>
         <div class="form-group">
          <label for="update_email">Employee Email:</label>
          <input type="email" class="form-control" id="update_email" name="update_email">
        </div>
        <div class="form-group">
          <label for="update_password">Employee Password:</label>
          <input type="text" class="form-control" id="update_password" name="update_password" placeholder="*******">
        </div>
        <div class="form-group">
          <label for="update_mobile">Employee mobile No:</label>
          <input type="text" class="form-control" id="update_mobile" name="update_mobile">
        </div>
        <button type="submit" class="btn btn-primary" name="button">Submit</button>
      </form>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>
<script>

$(document).ready(function(){
  var BranchListTable = $('#branch_table').DataTable({
         "dom": '<"html5buttons"B>tp',
         "bServerSide": true,
         "serverSide": true,
         "processing": true,
         "bRetrieve": true,
         "paging": true,
         "ajax": {
             "url": public_path+'/employee_json',
             "type": "GET",
             "data": function(d) {
                 return $.extend({}, d, {
                    'branch_count' : $('#branch_count').val() || '',
                    "search_user": $('#search_user').val() || '',
                 });
             }
         },



         "columns": [
         {
             "data": "id",
             "name": "id",
             "defaultContent": '-'
         },
         {
             "data": "employee_id",
             "name": "employee_id",
             "defaultContent": '-'
         },
         {
             "data": "name",
             "name": "name",
             "defaultContent": '-'
         },
         {
             "data": "email",
             "name": "email",
             "defaultContent": '-'
         },

         {
             "data": "mobile",
             "name": "mobile",
             "defaultContent": '-'
         },
         {
             "data": "role_display",
             "name": "role_display",
            "defaultContent": '-'
         },
         {
            "data": "status_display",
             "name": "status_display",
             "defaultContent": '-'
         },

         {

            "data": "",
            "name": "",
            "defaultContent": '-'
         },

         ],

         "order": [
             [0, "desc"]
         ],

         "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var page = this.fnPagingInfo().iPage;
            var length = this.fnPagingInfo().iLength;
            var index  = (page * length + (iDisplayIndex +1));

            $('td:eq(0)', nRow).html(index);
            var action1 = '<td class="admin-center"><a  data-toggle="modal" data-target="#update_user" title="Edit" onclick="appendUserId('+aData['id']+');"><i class="fa fa-pencil-square-o" aria-hidden="true" style="font-size:22px;text-align:center;color:#c09500;"></i></a>&nbsp&nbsp';

            let statusData = "";

              if(aData['status'] == "0")
              {
                statusData = "Active";
              }
              else if((aData['status'] == "1"))
              {
                statusData = "Inactive";
              }
              else{
                statusData = "Active";
              }

             action1 += '<a data-toggle="modal" data-target="#delete_user"  title="'+statusData+'" onclick="deleteUser('+aData['id']+','+aData['status']+');"><i class="fa fa-trash" aria-hidden="true" style="font-size:22px;text-align:center;color:#d33;cursor: pointer;"></i></a>&nbsp&nbsp';

             action1 += '</td>';


              $('td:eq(7)', nRow).html(action1);
             // $('td:eq(8)', nRow).html(action2);
          },

    });


     $('#search_user').on( 'keyup', function () {
      BranchListTable.draw();
    });

    $('#branch_count').change(function(){
      BranchListTable.page.len( $('#branch_count').val() ).draw();
    });


    $(document).delegate('td>a.edit_branch', 'click', function(){
      var id = $(this).attr('data-branch-id');
      window.location.href = public_path+'/edit_branch/'+id;
    });


  $("#add_employee" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules
      ------------------------------------------ */
      rules: {
          employee_id: { required: true },
          employee_name: { required: true },
          employee_email: { required: true},
          employee_mobile: { required: true},
          employee_password: { required: true},
          employee_role: { required: true},
        },
      /* @validation error messages
      ---------------------------------------------- */

      messages:{
          employee_id: { required: 'Please add Employee Id' },
          employee_name: { required: 'Please add Employee Name' },
          employee_email: { required: 'Please add Employee Email' },
          employee_password: { required: 'Please add Employee Password' },
          employee_mobile: { required: 'Please add Employee Mobile' },
          employee_role: { required: 'Please add Employee Role' },

      },
      submitHandler: function (form) {


    $.ajax({
            url:public_path + '/add_user',
            method:'post',
            data:new FormData($("#add_employee")[0]),
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){
               if(result.success==1){
                // alert("Employee Details Added Successfully.!")

                //      location.reload();
                Swal.fire({
                            type: 'success',
                            title: result.message,
                            showConfirmButton: true,

                         });
                         location.reload();
               }else{
                  swal("Error", result.message, "warning");
               }
            },
            error: function(error){
               if(error){
                    var error_status = error.responseText;
                    alert(error_status.message) ;
               }
            }
      });

        }
   });

  $("#update_users" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules
      ------------------------------------------ */
      rules: {
          // update_password: {
          //     required: true,
          // },
        },
      /* @validation error messages
      ---------------------------------------------- */

      messages:{
          // update_password: {
          //     required: 'Please Enter Password'
          // }

      },
      submitHandler: function (form) {


    $.ajax({
            url:public_path + '/update_users',
            method:'post',
            data:new FormData($("#update_users")[0]),
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){
               if(result.success==1){
                // alert("Your Password Updated Successfully.!")
                  Swal.fire({
                          type: 'success',
                          title: result.message,
                          showConfirmButton: true,
                                // timer: 1500
                        });
                     location.reload();
               }else{
                  swal("Error", result.message, "warning");
               }
            },
            error: function(error){
               if(error){
                    var error_status = error.responseText;
                    alert(error_status.message) ;
               }
            }
      });

        }
   });


});
     function appendUserId(user_id)
        {
         $('#user_hidden').val(user_id);

            $.ajax({
                    url:public_path + '/get_users/'+user_id,
                    method:'get',
                    dataType:'json',
                    cache: false,
                    processData:false,
                    contentType:false,
                    success:function(result){
                       if(result.success==1){

                        // $('#spec_data').html(result.specifications.tolerance);
                        // alert(result.specifications.parameter_id);
                        $('#update_name').val(result.user.name);
                        $('#update_email').val(result.user.email);
                        $('#update_mobile').val(result.user.mobile);



                       }else{
                          swal("Error", result.message, "warning");
                       }
                    },
                    error: function(error){
                       if(error){
                            var error_status = error.responseText;
                            alert(error_status.message) ;
                       }
                    }
              });
        }

   function deleteUser(id,status)
   {
     Swal.fire({
          title: 'Are you sure?',
          text: "You Want to change Status",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#5947B2',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Change it!'
        }).then((result) => {
          if (result.value == true) {

    $.ajax({
            url:public_path + '/delete_user/'+id+'/'+status,
            method:'get',
            data:new FormData($("#delete_user")[0]),
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){
               if(result.success==1){
                // alert(result.message);
                  Swal.fire({
                         type: 'warning',
                         title: result.message,
                         showConfirmButton: true,
                          // timer: 1500
                     });
                   $('#branch_table').DataTable().ajax.reload();

               }else{

                   alert(result.message);
                  //swal("Error", result.message, "warning");
               }
            },
            error: function(error){
               if(error){
                    var error_status = error.responseText;
                    alert(error_status.message) ;
               }
            }
      });
     }
        })

   }
</script>
@endsection
