@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <span>Email Support List</span>
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
                     <input type="text" class="form-control no-border-right" id="search_support" placeholder="Search...">
                    <div class="input-group-addon">
                      <i class="fa fa-search sear"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 pull-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#user" type="submit">Add Ticket</button>
                </div>
          </div>
       </div>
  </div>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    
                  <div class="">
                
                 <table id="branch_table" class="table table-striped table-bordered dt-responsive " cellspacing="0" width="100%" data-page-length='10'>

                    <thead>
                       <tr>
                       <th>S.no</th>
                       <th>Ticket Id</th>
                       <th>Ticket Subject</th>
                       <th>Email From</th>
                       <th>Domain</th>
                       <th>Assigned User</th>
                       <th>Created At</th> 
                       <th>Status</th>
                       <!-- <th>Total Hours Spent</th> -->
                       <!-- <th>Ticket Summary</th>
                       <th>Last Reply</th> -->
                        
                        <th>Status Update</th>                                              
                       
                    </tr>
                    </thead>
                    <tbody>
                     <!-- @php $i=1;
                       @endphp
                      @foreach ($support_emails as $support)
                      <tr>
                      <td>{{$i}}</td>
                      
                       <td>
                       <a href="" class="valign">{{$support->ticket_id}}</a>
                         <div class="row-options">
                          <a href="{{ url('view_ticket/'.$support->id) }}">View</a>
                           <span class="text-dark"> | </span>
                           <a href="">Edit </a>
                            <span class="text-dark"> | </span>
                            
                             <a href="" class="text-danger _delete">Delete </a>
                          </div>
                       </td>
                       <td>{{$support->subject}}</td>
                       <td></td>
                        <td></td>
                       <td>{{$support->created_at}}</td>
                     </tr>                                           
                       

                @php 
                $i++;
                 @endphp
                     @endforeach -->
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
          <h4 class="modal-title">Ticket Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <form id="add_ticket" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
          <label for="ticket_id">Ticket Id:</label>
          <input type="text" class="form-control" id="ticket_id" name="ticket_id">
        </div>
         <div class="form-group">
          <label for="ticket_summary">Ticket Summary:</label>
          <textarea type="text" class="form-control" id="ticket_summary" name="ticket_summary"></textarea>
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
                        
                        <option value="0">Open</option>
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
                    "search_support": $('#search_support').val() || '',
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
             "data": "ticket_id",
             "name": "ticket_id",
             "defaultContent": '-'
         },         
         {
             "data": "subject",
             "name": "subject",
             "defaultContent": '-'
         }, 
         {
             "data": "email_from",
             "name": "email_from",
             "defaultContent": '-'
         }, 
         {
             "data": "domain",
             "name": "domain",
             "defaultContent": '-'
         }, 
         {
             "data": "assigned",
             "name": "assigned",
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
         // {
         //     "data": "total_hours",
         //     "name": "total_hours",
         //     "defaultContent": '-'
         // },         
        
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
             
           var view_url = public_path+'/view_ticket/'+aData['id'];

           var view_link = '<td><div>'+aData['ticket_id']+'</div><a href='+view_url+'>View</a></td>';
             $('td:eq(1)', nRow).html(view_link);

              var action1 = '<td><a data-toggle="modal" data-target="#status_update" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" onclick="addDataValue('+aData['id']+');">Update</a>&nbsp&nbsp';
               action1 += '</td>';
             $('td:eq(9)', nRow).html(action1);


          },

    }); 

     
     $('#search_support').on( 'keyup', function () {
      BranchListTable.draw();
    });

    $('#branch_count').change(function(){
      BranchListTable.page.len( $('#branch_count').val() ).draw();
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

<script type="text/javascript">
    
$(document).ready(function(){

  $("#add_ticket" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          ticket_summary: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          ticket_summary: {
              required: 'Please Enter Ticket Summary'
          }
          
      },
      submitHandler: function (form) {



          
    $.ajax({
            url:public_path + '/add_ticket',
            method:'post',
            data:new FormData($("#add_ticket")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                // alert("Ticket Details Added Successfully.!")
                  
                     // location.reload();  
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
            url:public_path + '/update_status',
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

function addDataValue(id)
    {
      $('#status_id').val(id);

    }
</script>


@endsection