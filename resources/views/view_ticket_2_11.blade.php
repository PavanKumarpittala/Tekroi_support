@extends('layouts.app')

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="{{asset('js/differenceHours.js')}}"></script>

<style>
  .form-control {
    padding: 0.375rem 2.75rem !important;
  }
  
  p.text-primary.ml-3.add {
    font-size: 17px;
}
.fa.fa-plus-circle.text-primary.add{
  font-size: 23px;
}
.card-header {
  background-color:#c09500 !important;
}

.text-grey {
    color: #000
}

.fa {
    font-size: 30px;
    cursor: pointer
}

input,
select {
    /*padding: 2px 6px;*/
    border: none;
    border-bottom: 1px solid #000;
    border-radius: none;
    box-sizing: border-box;
    color: #000;
    background-color: transparent;
    font-size: 14px;
    letter-spacing: 1px;
    text-align: center !important
}

input:focus,
select:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border-bottom: 1px solid #00C853;
    outline-width: 0
}


select option:focus {
    background-color: #00C853 !important
}

button:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    outline-width: 0
}

.btn {
    border-radius: 50px;
    width: 120px
}

.exit {
    border: 1px solid #9E9E9E;
    color: #9E9E9E;
    background-color: transparent
}

.exit:hover {
    border: 1px solid #9E9E9E;
    color: #000 !important;
    background-color: #9E9E9E
}

@media screen and (max-width: 768px) {
    .mob {
        width: 70%
    }

    select.mob {
        width: 50%
    }
}
.summary pre{
  font-size: 100%;
  font-family: "Mulish", sans-serif;
}
a:visited{
  /*color:#fff !important;*/
  text-decoration: none !important;
}
a:link {
  /*color:#fff !important;*/
  text-decoration: none !important;
}
</style>

@section('content')
<!-- Select2 CSS --> 
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> 
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
@php
function displayFileIcon($fileName)
{
  $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
  $filePath = asset('attachment').'/'.$fileName;
  $shortFileName = substr($fileName, -20);

  if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'gif') {
    $file = "<a target='_blank' href='$filePath'>
                <img src='$filePath' width='50px'>
                <span>$shortFileName</span>
              </a>";
  }
  elseif ($fileExtension == 'doc' ||  $fileExtension == 'docx')
  {
    $iconPath = asset('icon/doc.png');
    $file = "<a target='_blank' href='$filePath'>
                <img src='$iconPath' width='50px'>
                <span>$shortFileName</span>
              </a>";
  }
  elseif ($fileExtension == 'xls' ||  $fileExtension == 'xlsx')
  {
    $iconPath = asset('icon/xls.png');
    $file = "<a target='_blank' href='$filePath'>
                <img src='$iconPath' width='50px'>
                <span>$shortFileName</span>
              </a>";
  }
  elseif ($fileExtension == 'pdf')
  {
    $iconPath = asset('icon/pdf.png');
    $file = "<a target='_blank' href='$filePath'>
                <img src='$iconPath' width='50px'>
                <span>$shortFileName</span>
              </a>";
  }
  elseif ($fileExtension == 'txt')
  {
    $iconPath = asset('icon/txt.png');
    $file = "<a target='_blank' href='$filePath'>
                <img src='$iconPath' width='50px'>
                <span>$shortFileName</span>
              </a>";
  }
  else {
    $file = "<a target='_blank' href='$filePath'>
                <span>$shortFileName</span>
              </a>";
  }

  return $file;

}
@endphp
<main class="app-content">
  <div class="container-fluid">
      <div class="col-md-12 user-right">
         
            <div class="card">
                <div class="card-header">
                   <div class="row">
                      <div class="col-md-7">
                       <h3 class="tile-title text-white">{{$ticket_data->subject_ticketid}}</h3>
                       </div>
                      <div class="col-md-3 ">
                       <h5 class="tile-title text-white"><b>On: </b>{{ date('d-m-Y H:i', strtotime($ticket_data->created_at))}}</h5>
                      </div>
                      <div class="col-md-2 ">
                        <a href="#add_timesheet">
                          <button type="button" class="btn btn-light" style="width:150px">Add Timesheet</button>
                        </a>
                      </div>
                    </div>
                 </div>
                <div class="card-body">
                  <div class="row g-0">
                     <div class="col-md-4">
                         <p>{{$ticket_data->email_name}}</p>
                         <div class="attachments" >
                        @foreach($attachments as $attachment)
                        <!-- <a  target="_blank" href="{{ URL::to('/') }}/attachment/{{ $attachment->filename}}">{{ $attachment->filename}}</a> -->
                          @php
                          echo displayFileIcon($attachment->filename);
                          echo "<br>";
                          echo "<br>";
                          @endphp
                         @endForeach
                      </div>
                      </div>

                      <div class="col-md-8 summary">
                         {!! $ticket_data->ticket_summary !!}
              
                      </div>

                    </div>
                </div>
                
            </div>
            
                     

            <div class="card mb-3 latest_ticket mt-3">
              <div class="row g-0 ticket-replies">
                  <div class="col-md-4 p-4">

                    @foreach ($last_ticket_replies as $ticket_reply)
                      <p>{{$ticket_reply->email_from}}</p>
                    @endforeach
                    <!-- <h5 class="card-title"><b>Received At/ Replied</b></h5> -->

                      @php 
                       if(count($last_ticket_replies) > 0) { 
                       @endphp
                          <p class="card-text">                            
                           {{ date('d-m-Y H:i', strtotime(isset( $ticket_reply->created_at)?$ticket_reply->created_at:""))}}
                          </p>
                         <h6><b>Attachments</b></h6>
                        @php 
                        }
                       @endphp
                      @php
                      if (isset($ticket_reply->attachments)) {
                          $attachments = explode(',', $ticket_reply->attachments);

                          foreach ($attachments as $attachment) {
                         
                           echo displayFileIcon($attachment);
                           echo "<br>";
                           echo "<br>";
                          }
                      }
                      @endphp

                  </div>
                  <div class="col-md-8 ">
                    @forelse ($last_ticket_replies as $ticket_reply)
                      <div class="card-body summary">
                          <h5 class="card-title"><b>Summary</b></h5>
                          <p class="card-text ">{!! $ticket_reply->summary !!}</p>                                                
                      </div>
                      <button type="button" class="btn btn-primary ml-5 view_more_btn mb-3 ml-5" name="submit" style="width:300px;">
                        View Previous Messages
                      </button>
                       @empty
                       No replies.
                      @endforelse
                  </div>
              </div>
          </div>
          <?php
          foreach($ticket_replies as $ticket_reply){?>
            <div class="card mb-3 d-none all_tickets">
              <div class="row g-0 ticket-replies">
                  <div class="col-md-4 p-4">                   
                      <p>{{$ticket_reply->email_from}}</p> 
                      <!-- <h5 class="card-title"><b>Received At/ Replied</b></h5> -->
                          <p class="card-text">{{ date('d-m-Y H:i', strtotime($ticket_reply->created_at))}}</p>
                          <h6><b>Attachments</b></h6>
                        
                         <?php
                          if (isset($ticket_reply->attachments)) {
                          $attachments = explode(',', $ticket_reply->attachments);

                          foreach ($attachments as $attachment) {
                          // echo "<a target='_blank' href='/tekroi_support/public/attachment/$attachment'>$attachment</a><br>";
                            echo displayFileIcon($attachment);
                            echo "<br>";
                            echo "<br>";
                          }
                      }
                         ?>                   
                  </div>
                    <div class="col-md-8 ">                  
                      <div class="card-body">
                          <h5 class="card-title"><b>Summary</b></h5>
                          <p class="card-text summary">{!! $ticket_reply->summary !!}</></p>
                                 
                       
                      </div>                     
                  </div>

              </div>

          </div>
          <?php
        }
          ?>          

    <div class="row jumbotron reply-ticket" >
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="reply-form" action="{{ route('replyticket')}}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <h5 class="text-dark"> Reply Message </h5>
                <p><input type="hidden" name="ticket_id" value="{{ $ticket_data->id }}"></p>                
              
                <p><textarea name="summary" class="des form-control"rows="4" cols="80" type="text"> </textarea></p>
                <p>File Attachments : <input type="file" name="attachments[]" multiple></p>
                <p><button type="submit" class="btn btn-primary ml-2" name="submit">Submit</button></p>               
            </form>
        </div>
        <div id="add_timesheet"></div>
    </div>
    
   <!-- <div class="container-fluid px-1 px-sm-4 py-5 mx-auto">
    <div class="row d-flex justify-content-center"> -->
       @if(Auth::user()->role != 4)

        <div class="col-md-12 col-lg-10 col-xl-12 mt-3" >
           <h4 class="text-danger" >Add Timesheet</h4>
            <div class="card mt-3">
              <form id="add_ticket_details" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}

                <div class="row px-3">
                     <!-- <div class="col-sm-2">
                     <label class="text-grey mt-1 mb-3">Open Hours</label>
                    </div> -->
                    <div class="col-sm-12 list">
                        <div class="mb-2 row justify-content-between px-3">
                        <input type="hidden" value="<?php echo $ticket_data->id;?>" name="ticket_id" id="ticket_id" > 

                            <div class="dt"> 
                              <label class="text-grey dt">Date</label>
                               <input class="dt form-control" type="date" name="date[]" id="dt"> 
                            </div>
                            <div class="mob"> 
                                <label class="text-grey mr-1">Start Time</label> 
                                <input class="ml-1 form-control" type="time" name="start_time[]" id="start_time_1" onchange="calculateTime(1)"> 
                            </div>
                            <div class="mob mb-2">
                                 <label class="text-grey mr-4">End Time</label> 
                                 <input class="ml-1 form-control" type="time" name="end_time[]" id="end_time_1" onchange="calculateTime(1)">
                            </div>  
                            <div class="mob tt"> 
                                  <label class="text-grey total_t">Total Time:</label>
                                  <input type="text" class="form-control" name="total_time[]" id="total_time_1" readonly >
                                 <!--  <strong id="total_time_1" name="total_time_1" class="">00hours 00minutes</strong>  -->

                            </div> 
                             <div class="des"> 
                              <label class="text-grey des">Description</label>
                               <textarea class="des form-control"rows="4" cols="80" type="text" name="description[]" id="description"></textarea> 
                            </div>
                            
                            <!-- <input type="hidden" name="total_time" id="total_time">  -->
                             <div class="mt-1 cancel fa fa-times text-danger"style=" margin-top: 2.25rem !important;"></div>
                        </div>
                    </div>
                </div>
                <div class="row px-3 mt-3">
                    <div class="col-sm-8"  ></div>
                    <div class="col-sm-4">
                        <div class="row px-3">
                            <div class="fa fa-plus-circle text-primary add"></div>
                            <p class="text-primary ml-3 add">Add More</p>
                            <button type="submit" class="btn btn-primary ml-2" name="button">Submit</button> 
                        </div>
                    </div>
                </div>
                <!-- <div class="row px-3 mt-3 justify-content-center"> 
                  <button type="submit" class="btn btn-primary ml-2" name="button">Submit</button> 
                  
                </div> -->
              </form>
            </div>
        </div>
        
    <!-- </div>
</div> -->

        <div class="col-md-12 col-lg-10 col-xl-12 mt-3">
            <div class="card" style="border:0;">
              <form id="update_details" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}

                <div class="row px-3">
                  
                    <div class="col-sm-12 ">
                        <div class="mb-2 row justify-content-between px-3">
                          <div class="col-sm-4">
                             <input type="hidden" value="<?php echo $ticket_data->id;?>" name="ticket_id" id="ticket_id" > 
                              <div class="form-group">
                                <label class="text-grey ">Assigned To</label>
                                <select class="form-control" id="re_assigned" name="re_assigned">
                                 <option value="">Select User</option>
                                   @foreach($users as $user)
                                   <option value="{{$user->id}}">{{$user->name}}</option>
                                  @endForeach
                                </select>
                              </div>
                          </div>
                          <div class="col-sm-4">
                              <div class="form-group">
                                <label class="text-grey ">Issue Summary</label>
                                <input type="text" class="form-control" name="issue_summary" id="issue_summary">
                              </div>
                          </div>
                          <div class="col-sm-4 mt-4">
                             
                                  <button type="submit" class="btn btn-primary ml-2" name="button">Submit</button> 
                              
                          </div>
                        </div>
                    </div>
                </div>
                
              </form>
            </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="mr-4 pull-right">               

                      <b><h4 class="text-danger">
                        <?php if(isset($total_hours[0]->total_time)){ ?>
                        Total Hours: {{date ('H:i:s',strtotime($total_hours[0]->total_time));}}

                      <?php } ?>

                      </h4></b>
                    </div>
                  <div>
                
                 <table id="branch_table" class="table table-striped table-bordered dt-responsive nowrap branch_table" cellspacing="0" width="100%" data-page-length='10'>
                 
                    <thead>
                    <tr>
                        <th>S.no</th>
                        <th>User Name</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Total Time</th>
                        <th>Description</th>
                        
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
  @endif
  </div>         
</div>
</main>


<script type="text/javascript">
  var i=1;
  $(document).ready(function(){

$('.add').click(function(){
 i++;
$(".list").append(

'<div class="mb-2 row justify-content-between px-3">' +   
    '<div class="dt">' +
        '<label class="text-grey dt">Date</label>' +
        '<input class="dt form-control" type="date" id="dt" name="date[]">' +
        '</div>' +
    '<div class="mob">' +
        '<label class="text-grey mr-1">Start Time</label>' +
        '<input class="ml-1 form-control" type="time" name="start_time[]" id="start_time_'+i+'" onchange="calculateTime('+i+')">' +
        '</div>' +
    '<div class="mob mb-2">' +
        '<label class="text-grey mr-4">End Time</label>' +
        '<input class="ml-1 form-control" type="time" name="end_time[]" id="end_time_'+i+'" onchange="calculateTime('+i+')">' +
        '</div>' +
    '<div class="mob tt">' +
        '<label class="text-grey total_t">Total Time:</label>' +
        ' <input type="text" class=" form-control" name="total_time[]" id="total_time_'+i+'"  readonly>' +
        '</div>' +
    '<div class="des">' +
        '<label class="text-grey des">Description:</label>' +
        ' <textarea class="des form-control"rows="4" cols="80" type="text" name="description[]"></textarea>' +
        '</div>' +
    '<div class="mt-1 cancel fa fa-times text-danger" style=" margin-top: 2.25rem !important;">' +
        '</div>' +
    '</div>');

});

$(".list").on('click', '.cancel', function(){
$(this).parent().remove();
});

});
</script>

<script type="text/javascript">

  function calculateTime(my_id) {
    // console.log(my_id);
   differenceHours.diff_hours('start_time_'+my_id,'end_time_'+my_id,'total_time_'+my_id) 
    
  }


</script>

<script type="text/javascript">
  $(document).ready(function(){
     $("#re_assigned").select2(); 
});
</script>

<script>
  
$(document).ready(function(){
   

  $("#add_ticket_details" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          date: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          date: {
              required: 'Please Enter Date'
          }
          
      },
      submitHandler: function (form) {



          
    $.ajax({
            url:public_path + '/ticket_details',
            method:'post',
            data:new FormData($("#add_ticket_details")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                // alert("Ticket Details Added Successfully.!")
                  
                     
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
</script>

<script>
  
$(document).ready(function(){
   

  $("#update_details" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          issue_summary: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          issue_summary: {
              required: 'Please Enter summary'
          }
          
      },
      submitHandler: function (form) {



$.ajax({
            url:public_path + '/update_details',
            method:'post',
            data:new FormData($("#update_details")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                // alert("Details Updated Successfully.!")
                  Swal.fire({
                            type: 'success',
                            title: result.message,
                            showConfirmButton: true,
                          
                         });  
                     // location.reload();                       
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

$(".view_more_btn").click(function(){  
  $('.all_tickets').removeClass('d-none');
  // $('.latest_ticket').addClass('d-none');
});


});
</script>

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
             "url": public_path+'/view_ticket_json',
             "type": "GET",
             "data": function(d) {
                 return $.extend({}, d, {
                    'tecket_id'  : "{{$ticket_data->id}}",
                    'branch_count' : $('#user_count').val() || '',
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
             "data": "date",
             "name": "date"
         },
         {
             "data": "start_time",
             "name": "start_time"
         },
         {
             "data": "end_time",
             "name": "end_time",
             "defaultContent": '-'
         },
         {
             "data": "total_time",
             "name": "total_time",
             "defaultContent": '-'
         }, 
         {
             "data": "description",
             "name": "description",
             "defaultContent": '-'
         },
         ],         

         "order": [
             [0, "desc"]
         ],

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



});
     
</script>
 <!-- disable future date -->
 <script type="text/javascript">
   $(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var maxDate = year + '-' + month + '-' + day;
    // alert(maxDate);
    $('#dt').attr('max', maxDate);
});
 </script>

@endsection