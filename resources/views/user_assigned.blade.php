@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <h4 style="color:#A04000">Users assigned List</h4>

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
                    <button class="btn btn-primary" data-toggle="modal" data-target="#user_assign" type="submit">Add Team Member</button>
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
                        <th>Manager</th>
                        <th>Team Member</th>                                           
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

<div class="modal fade" id="user_assign">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">User Assign</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <form id="user_assigned" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
             <label class="control-label">Users</label>
              <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="user" name="user">
                <option value="">Select Users</option>
                    @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
                    @endForeach
              </select>
            </div>
            <div class="form-group">
             <label class="control-label">Team Member</label>
              <select name="assigned_user" id="assigned_user" multiple class="form-control selectpicker">
                <option value="">Select Users</option>
                @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                @endForeach
              </select>
            </div>
            <input type="hidden" name="hidden_id" id="hidden_id" />
             <div style="clear:both"></div>
         
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
             "url": public_path+'/assigned_user_json',
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
            "data": "user",
            "name": "user",
            "defaultContent": '-'
         },
         {
            "data": "assigned_user",
            "name": "assigned_user",
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

            // action1 += '<a data-toggle="modal" data-target="#update_user" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" onclick="appendUserId('+aData['id']+');">Update</a>&nbsp&nbsp';
            //  action1 += '</td>';
               

              $('td:eq(6)', nRow).html(action1);
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


  $("#user_assigned" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
         user: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          user: {
              required: 'Please Select User'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/add_user_assigned',
            method:'post',
            data:new FormData($("#user_assigned")[0]), 
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


    
});
    
        // $( document ).ready(function() {
        //   $('#user').on('change', function() {
        //     let selelcted_user = this.value;            
        //     $("#assigned_user option[value='"+selelcted_user+"']").each(function() {
        //         $(this).remove();
        //     });          
        //   });
        // });
     
$('#assigned_user').change(function(){
    $('#hidden_id').val($('#assigned_user').val());
    var query = $('#hidden_id').val();
  });      
</script>
@endsection