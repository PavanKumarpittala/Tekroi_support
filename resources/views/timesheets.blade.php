@extends('layouts.app')
 
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
 -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="{{asset('js/differenceHours.js')}}"></script>

<style>
.line{
  border:2px dashed #ddd;
  margin-top: 5px;
}
.form-control {
    padding: 0.375rem 2.75rem !important;
  }
p.text-primary.ml-3.add {
    font-size: 17px;
}
.fa.fa-plus-circle.text-primary.add{
  font-size: 23px;
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
</style>

@section('content')

<main class="app-content">
  <div class="container-fluid">
     
   <!-- <div class="container-fluid px-1 px-sm-4 py-5 mx-auto">
    <div class="row d-flex justify-content-center"> -->
        <div class="col-md-12 col-lg-10 col-xl-12 mt-3">
            <div class="card">
              <form id="add_timesheet_details" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
                
                <div class="row px-3">
                  <div class="cancels">
                 
                    <div class="col-sm-12 list">
                      <div class="mb-2 row justify-content-between px-3">
                        <div class="col-md-3">  
                            <div class="form-group">
                              <label class="text-grey ">Select Project</label>
                              <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="project_name" name="project_name[]">
                                <option value="">Select Project</option>
                           @foreach($projects as $project)
                              <option value="{{$project->id}}">{{$project->project_name}}</option>
                           @endForeach
                              </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dt"> 
                              <label class="text-grey dt">Date</label>
                               <input class="dt form-control" type="date" name="date[]" id="dt"> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mob"> 
                                <label class="text-grey mr-1">Start Time</label> 
                                <input class="ml-1 form-control" type="time" name="start_time[]" id="start_time_1" onchange="calculateTime(1)"> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mob mb-2">
                                 <label class="text-grey mr-4">End Time</label> 
                                 <input class="ml-1 form-control" type="time" name="end_time[]" id="end_time_1" onchange="calculateTime(1)">
                            </div> 
                        </div>
                        <div class="col-md-2"> 
                            <div class="mob tt"> 
                                  <label class="text-grey total_t">Total Time:</label>
                                  <input type="text" class="form-control" name="total_time[]" id="total_time_1" readonly >
                                 <!--  <strong id="total_time_1" name="total_time_1" class="">00hours 00minutes</strong>  -->
                             </div>
                        </div>
                        <div class="col-md-3"> 
                              <div class="form-group sta">
                                <label class="text-grey ">Status</label>
                                <select class="form-control" id="status" name="status[]">
                                  <option value="">Select Status</option>
                                  <option value="In Progress">In Progress</option>
                                  <option value="Completed">Completed</option>
                                  <option value="Transferred">Transferred</option>
                                </select>
                              </div>
                        </div>
                        <div class="col-md-3"> 
                            <div class="who_assigned"> 
                              <label class="text-grey assign">Who Assigned</label>
                                 <input type="text" class="form-control" name="who_assigned[]" id="who_assigned" >
                            </div>
                        </div>
                                                       
                        <div class="col-md-4"> 
                             <div class="des"> 
                              <label class="text-grey des">Description</label>
                               <textarea class="des form-control"rows="4" cols="50" type="text" name="description[]" id="description"> </textarea>
                            </div>
                        </div>
                            
                      </div>
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
</div>
</main>


<script type="text/javascript">
  var i=1;
  $(document).ready(function(){

$('.add').click(function(){
 i++;
$(".list").append(
'<div class="cancels line">'+
 '<div class="mt-1 cancel fa fa-times" style="float: right;border-radius: 10px;background-color:red;color:#fff;padding: 2px;margin-right:3px;">' +
'</div>' +

'<div class="mb-2 row justify-content-between px-3">' +
   '<div class="col-md-3">'+  
      '<div class="form-group">'+
        '<label class="text-grey ">Select Project</label>'+
        '<select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="project_name" name="project_name[]">'+
            '<option value="">Select Project</option>'+
                @foreach($projects as $project)
            '<option  value="{{$project->id}}">{{$project->project_name}}</option>'+
                 @endForeach
          '</select>'+
        '</div>'+
     '</div>'+

    '<div class="col-md-3">'+
      '<div class="dt">' +
          '<label class="text-grey dt">Start Date</label>' +
          '<input class="dt form-control" type="date" name="date[]">' +
          '</div>' +
    '</div>'+

    '<div class="col-md-3">'+
       '<div class="mob">' +
        '<label class="text-grey mr-1">Start Time</label>' +
        '<input class="ml-1 form-control" type="time" name="start_time[]" id="start_time_'+i+'" onchange="calculateTime('+i+')">' +
        '</div>' +
     '</div>'+

    '<div class="col-md-3">'+
       '<div class="mob mb-2">' +
        '<label class="text-grey mr-4">End Time</label>' +
        '<input class="ml-1 form-control" type="time" name="end_time[]" id="end_time_'+i+'" onchange="calculateTime('+i+')">' +
        '</div>' +
     '</div>'+

    '<div class="col-md-2">'+
      '<div class="mob tt">' +
        '<label class="text-grey total_t">Total Time:</label>' +
        ' <input class="form-control" type="text" name="total_time[]" id="total_time_'+i+'"  readonly>' +
        '</div>' +
    '</div>'+

    '<div class="col-md-3">'+
      '<div class="form-group sta">'+
        '<label class="text-grey ">Status</label>'+
          '<select class="form-control" id="status" name="status[]">'+
                '<option value="">Select Status</option>'+
                '<option value="In Progress">In Progress</option>'+
                '<option value="Completed">Completed</option>'+
                '<option value="Transferred">Transferred</option>'+
          '</select>'+
       '</div>'+
    '</div>'+

    '<div class="col-md-3">'+
     '<div class="who_assigned">'+ 
      '<label class="text-grey assign">Who Assigned</label>'+
      '<input type="text" class="form-control" name="who_assigned[]" id="who_assigned" >'+
      '</div>'+
    '</div>'+

  '<div class="col-md-4">'+
    '<div class="des">' +
        '<label class="text-grey des">Description:</label>' +
        ' <textarea class="des form-control"rows="4" cols="50" type="text" name="description[]"></textarea>' +
        '</div>' +
    '</div>'+
     
  '</div>'+
'</div>');

});

$(".cancels").on('click', '.cancel', function(){
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



<script>
  
$(document).ready(function(){
  $("#add_timesheet_details" ).validate({
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
            url:public_path + '/timesheet_details',
            method:'post',
            data:new FormData($("#add_timesheet_details")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                // alert("Timesheet Details Added Successfully.!")
                  
                     //   
                     Swal.fire({
                            type: 'success',
                            title: result.message,
                            showConfirmButton: true,
                          
                         });
                      location.replace("timesheets_list");                     
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