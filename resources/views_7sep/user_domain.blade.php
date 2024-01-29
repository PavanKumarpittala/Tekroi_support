@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <span>Users Domain List</span>

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
                    <button class="btn btn-primary" data-toggle="modal" data-target="#user_domain" type="submit">Add User Domain</button>
                </div>
          </div>
       </div>
  </div>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    
                  <div>
                    <table id="branch_table" class="table table-striped table-bordered dt-responsive nowrap branch_table" cellspacing="0" width="100%" data-page-length='10'>
                <!-- <table id="branch_table" class="table table-striped table-bordered nowrap"> -->
                    <thead>
                    <tr>
                        <th>S.no</th>
                        <th>User Name</th>
                        <th>Domain</th>
                        <th>Created At</th>
                        <th>Status</th> 
                        <th>Action</th>                       
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

<div class="modal fade" id="user_domain">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Domain Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <form id="add_domain" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
              <label for="user_id">Select User:</label>
                 <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="user_id" name="user_id">
                        <option value="">Select User</option>
                     @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                     @endForeach
                        </select>
            </div>
         
          <div class="form-group">
            <label for="domain">Support Domain:</label>
            <input type="text" class="form-control" id="domain" name="domain">
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
  <div class="modal fade" id="update_users_domain">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Update User Domain</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <form id="update_user_domain" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
           <input type="hidden" name="user_hidden" id="user_hidden"> 
           <div class="form-group">
              <label for="user_id_update">Select User:</label>
                 <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="user_id_update" name="user_id_update">
                        <option value="">Select User</option>
                     @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                     @endForeach
                        </select>
            </div>
             <div class="form-group">
            <label for="domain_update">Support Domain:</label>
            <input type="text" class="form-control" id="domain_update" name="domain_update">
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
             "url": public_path+'/domain_json',
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
             "data": "user_id",
             "name": "user_id"
         },
         {
             "data": "domain",
             "name": "domain",
             "defaultContent": '-'
         },
         {
             "data": "created_at",
             "name": "created_at",
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
            var action1 = '<td class="admin-center"><a  data-toggle="modal" data-target="#update_users_domain" title="Edit" onclick="editUserDomain('+aData['id']+');"><i class="fa fa-pencil-square-o" aria-hidden="true" style="font-size:22px;text-align:center;color:#c09500;cursor: pointer;"></i></a>&nbsp&nbsp';

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
           
             action1 += '<a data-toggle="modal" data-target="#delete_domain"  title="'+statusData+'" onclick="deleteDomain('+aData['id']+','+aData['status']+');"><i class="fa fa-trash" aria-hidden="true" style="font-size:22px;text-align:center;color:#d33;cursor: pointer;"></i></a>&nbsp&nbsp';

             action1 += '</td>';
               

              $('td:eq(5)', nRow).html(action1);
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


  $("#add_domain" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          domain: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          domain: {
              required: 'Please add domain'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/add_domain',
            method:'post',
            data:new FormData($("#add_domain")[0]), 
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

   $("#update_user_domain" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          domain_update: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          domain_update: {
              required: 'Please Add Domain'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/update_user_domain',
            method:'post',
            data:new FormData($("#update_user_domain")[0]), 
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

   
   function editUserDomain(user_id)
        {
         $('#user_hidden').val(user_id);

            $.ajax({
                    url:public_path + '/get_domain/'+user_id,
                    method:'get',
                    dataType:'json',
                    cache: false,
                    processData:false,
                    contentType:false,
                    success:function(result){                
                       if(result.success==1){

                        // $('#spec_data').html(result.specifications.tolerance);
                        // alert(result.specifications.parameter_id);
                        $('#user_id_update').val(result.user.user_id);
                        $('#domain_update').val(result.user.domain);                       

                       
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

    function deleteDomain(id,status)
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
            url:public_path + '/delete_domain/'+id+'/'+status,
            method:'get',
            data:new FormData($("#delete_domain")[0]), 
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