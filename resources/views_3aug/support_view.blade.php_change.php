@extends('layouts.app')

@section('content')
<style>
  .table_commodity{
  width: 100%;
  overflow: hidden;
}
.table {
  overflow-x: auto;
  display: block;
}
</style>
<main class="app-content">
<div class="container-fluid">
<div class="col-md-12 user-right">
  <!-- <div class="panel panel-default">
      <div class="panel-heading"> -->
      <span>Email Feedback List</span>

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
                  <div class="col-md-6"></div>
                  
                <!-- <div class="col-md-8"></div> -->
                <div class="col-md-3 margin pull-right no-m-top">
                           <div class="input-group">
                     <input type="text" class="form-control no-border-right" id="search_user" placeholder="Search...">
                    <div class="input-group-addon">
                      <i class="fa fa-search sear"></i>
                    </div>
                  </div>
                </div>
            </div>
          <!-- </div>
        </div> -->

        <div class="row">
            <!-- <div class="panel panel-default">
                <div class="panel-heading"> -->
                  <!-- <span>Grain Capture List</span> -->
                  <!-- <div>
                
                <table id="branch_table" class="table table-striped table-bordered dt-responsive nowrap branch_table" cellspacing="0" width="100%" data-page-length='10'> -->  
                <div class="table_commodity">
              <table id="branch_table" class="table table-striped table-bordered nowrap">  
                    <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>received At</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>View Body Content</th>
                        <th>Update Status</th>
                        <th>Comments</th>

                    </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
       <!--  </div>
    </div> -->

<!-- sidemenu close divs-->
</div>
</div>
</div>

</main>


<div class="modal fade" id="CustomerView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal_lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">

                    <h4 class="text-left">View Body</h4>

                    <div id="body_content"> </div>
                 </div>
            </div>
        </div>
</div>

<div class="modal fade" id="status_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal_lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
            <form id="update_status" action="javascript:void(0)" method="POST">
                {!! csrf_field() !!}
                <h4 class="text-left">Update Status</h4>

                    <select class="form-control" name="update_status" id="update_status">
                        
                        <option value="0">Pending</option>
                        <option value="1">Closed</option>
                    </select>

                    <input type="hidden" name="status_id" id="status_id">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle" name="button"></i>Update Status</button>
            </form>
                    <!-- <div id="body_content"> </div> -->
                 </div>
            </div>
        </div>
</div>

<div class="modal fade" id="comments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal_lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
            <form id="update_comments" action="javascript:void(0)" method="POST">
                {!! csrf_field() !!}
                <h4 class="text-left">Comments</h4>

                    
                    <input type="text" class="form-control" name="update_comments" id="update_comments">

                    <input type="hidden" name="comments_id" id="comments_id">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle" name="button"></i>Update Comments</button>
            </form>
                    <!-- <div id="body_content"> </div> -->
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
             "url": public_path+'/support_json',
             "type": "GET",
             "data": function(d) {
                 return $.extend({}, d, {
                    'search_count' : $('#branch_count').val() || '',
                    "search_support": $('#search_user').val() || '',
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
             "data": "name",
             "name": "name"
         },{
             "data": "email",
             "name": "email",
             "defaultContent": '-'
         }, {
             "data": "subject",
             "name": "subject",
             "defaultContent": '-'
         },
         {
             "data": "received_at",
             "name": "received_at",
             "defaultContent": '-'
         },
         {
            "data": "created_at",
            "name": "created_at",
            "defaultContent": '-'
         },
         {
            "data": "status",
            "name": "status",
            "defaultContent": '-'
         },
          {
            "data": "comments",
            "name": "comments",
            "defaultContent": '-'
         },         
         {
            "data": "",
            "name": "",
            "defaultContent": '-'
         },
        {
            "data": "",
            "name": "",
            "defaultContent": '-'
         },
         {
            "data": "",
            "name": "",
            "defaultContent": '-'
         }
         ],         

         "order": [
             [0, "desc"]
         ],

         "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var page = this.fnPagingInfo().iPage;
            var length = this.fnPagingInfo().iLength;
            var index  = (page * length + (iDisplayIndex +1));

            $('td:eq(0)', nRow).html(index); 
            var action1 = '<a data-toggle="modal" data-target="#CustomerView" class="btn btn-primary content" data-id="'+aData['id']+'" style="color: #fff;" onclick="appendUserId('+aData['id']+');">View Content</a>&nbsp&nbsp';
            /*action1 += '<a data-toggle="modal" data-target="#CustomerView" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" onclick="appendUserId('+aData['id']+');">Update</a>';*/
            /*&nbsp&nbsp <a data-toggle="modal" data-target="#exampleModal" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" >View</a>*/

             action1 += '</td>';
             $('td:eq(8)', nRow).html(action1);
            var action2 = '<a data-toggle="modal" data-target="#status_update" class="btn btn-primary content" data-id="'+aData['id']+'" style="color: #fff;" onclick="addDataValue('+aData['id']+');">Update Status</a>&nbsp&nbsp';
            action2 += '</td>';
             $('td:eq(9)', nRow).html(action2);

             var action3 = '<a data-toggle="modal" data-target="#comments" class="btn btn-primary content" data-id="'+aData['id']+'" style="color: #fff;" onclick="addDataValue('+aData['id']+');">Comments</a>&nbsp&nbsp';
            action3 += '</td>';
             $('td:eq(10)', nRow).html(action3);


          },

    }); 

     
     $('#search_user').on( 'keyup', function () {
      BranchListTable.draw();
    });

    $('#branch_count').change(function(){
      BranchListTable.page.len( $('#branch_count').val() ).draw();
    });
 $("#update_status" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          update_status: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          update_status: {
              required: 'Please Update Status'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/update_support_status',
            method:'post',
            data:new FormData($("#update_status")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                alert("Status Updated Successfully.!")
                  // swal({
                  //       title: "Success",
                  //       text: result.message,
                  //       type: "success",
                  //       });  
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


 $("#update_comments" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          update_comments: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          update_comments: {
              required: 'Please Update Comment'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/update_support_comments',
            method:'post',
            data:new FormData($("#update_comments")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                alert("Comment Updated Successfully.!")
                  // swal({
                  //       title: "Success",
                  //       text: result.message,
                  //       type: "success",
                  //       });  
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
         
            $.ajax({
                    url:public_path + '/view_email_body/'+user_id,
                    method:'get',
                    dataType:'json',
                    cache: false,
                    processData:false,
                    contentType:false,
                    success:function(result){                
                       if(result.success==1){

                        //alert(result.mail_body);


                        $('#body_content').html(result.mail_body.body);
                       
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
    function addDataValue(id)
    {
      $('#status_id').val(id);
      $('#comments_id').val(id);

    }
    



</script>
@endsection