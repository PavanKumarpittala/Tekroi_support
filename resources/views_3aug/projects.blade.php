@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <span>Projects List</span>

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
                  <!-- <div class="col-md-7"></div> -->
                  <div class="col-md-3">
                 
                </div> 

               
                <div class="col-md-3 margin pull-right no-m-top">
                    <div class="input-group">                      
                     <input type="text" class="form-control no-border-right" id="search_user" placeholder="Search...">
                    <div class="input-group-addon">
                      <i class="fa fa-search sear"></i>
                     </div>
                  </div>
                </div>

                <div class="col-md-3 margin pull-right ">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#project" type="submit">Add Project</button>
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
                 
                    <thead>
                      <tr>
                        <th>S.no</th>
                        <th>Project Name</th>
                        <th>Customer</th>
                        <th>Start Date</th>
                        <th>Status</th>
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

<div class="modal fade" id="project">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Project Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <form id="add_project" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}
            
         <div class="form-group">
          <label for="project_name">Project Name:</label>
          <input type="text" class="form-control" id="project_name" name="project_name" placeholder="Enter Project Name">
        </div>
         <div class="form-group">
          <label for="customer">Customer Name:</label>
          <input type="text" class="form-control" id="customer" name="customer" placeholder="Enter Customer Name">
        </div>
        <div class="form-group">
          <label for="customer_email">Customer Email:</label>
          <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="Enter Customer Email">
        </div>
        <div class="form-group">
          <label for="customer_mobile">Customer Mobile No:</label>
          <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" placeholder="Enter Customer Mobile No">
        </div>
        <div class="form-group">
          <label for="customer_designation">Customer Designation:</label>
          <input type="text" class="form-control" id="customer_designation" name="customer_designation" placeholder="Enter Customer Designation">
        </div>
        <div class="form-group list">
          <label for="name">Primary Contact:</label>
           <input type="text" class="form-control" id="name" name="name[]" placeholder="Enter contact name">
          <input type="email" class="form-control" id="email" name="email[]" placeholder="Enter contact email">
          <input type="text" class="form-control" id="mobile" name="mobile[]" placeholder="Enter contact mobile">
          <input type="text" class="form-control" id="designation" name="designation[]" placeholder="Enter contact designation">
          <div class="fa fa-plus-circle text-primary add"> Add Sub Contacts</div>
        </div>
        <div class="form-group">
          <label for="division">Main Division:</label>
            <select name="division" id="division" class="form-control">
               <option value="" disabled selected>Select Division</option>
               <option value="SAP">SAP</option>
               <option value="ByDesign">ByDesign</option>
               <option value="IE">IE</option>
               <!-- <option value="Web">Web</option> -->
            </select>
            <span class="input-group-addon">-</span>

        <select name="sub_division" id="choices" class="form-control">
          <option value="" disabled selected>Please select Sub Division</option>
        </select>
        <!-- <div class="form-group">
          <label for="division">Main Division:</label>
          <select class="form-control" name="division" id="division">
            <option value="SAP">SAP</option>
            <option value="ByDesign">ByDesign</option>
            <option value="AIML">AIML</option>
            <option value="Web">Web</option>
          </select> -->
        </div>
        <div class="form-group">
          <label for="start_date">Start Date</label>
          <input type="date" class="form-control" id="start_date" name="start_date">
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

<script type="text/javascript">
  var i=1;
  $(document).ready(function(){

$('.add').click(function(){
 i++;
$(".list").append( 
   '<div class="form-group">' + 
        '<input type="text" class="form-control" id="name" name="name[]" placeholder="name">' +
    '</div>',
    '<div class="form-group">' + 
        '<input type="email" class="form-control" id="email" name="email[]" placeholder="email">' +
    '</div>',
    '<div class="form-group">' + 
        '<input type="text" class="form-control" id="mobile" name="mobile[]" placeholder="mobile no">' +
    '</div>',
    '<div class="form-group">' + 
        '<input type="text" class="form-control" id="designation" name="designation[]" placeholder="designation">' +
    '</div>' 
        );
});

$(".list").on('click', '.cancel', function(){
$(this).parent().remove();
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
             "url": public_path+'/project_json',
             "type": "GET",
             "data": function(d) {
                 return $.extend({}, d, {
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
             "data": "project_name",
             "name": "project_name",
             "defaultContent": '-'
         },
         {
             "data": "customer",
             "name": "customer",
             "defaultContent": '-'
         },
         {
             "data": "start_date",
             "name": "start_date",
             "defaultContent": '-'
         },
         {
             "data": "status",
             "name": "status",
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


  $("#add_project" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules 
      ------------------------------------------ */     
      rules: {
          project_name: {
              required: true,
          },
        },
      /* @validation error messages 
      ---------------------------------------------- */
        
      messages:{
          project_name: {
              required: 'Please add Project Name'
          }
          
      },
      submitHandler: function (form) {

          
    $.ajax({
            url:public_path + '/add_project',
            method:'post',
            data:new FormData($("#add_project")[0]), 
            dataType:'json',
            async:false,
            cache: false,
            processData:false,
            contentType:false,
            success:function(result){                
               if(result.success==1){
                // alert("Project Details Added Successfully.!")
                  
                //      location.reload();     
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
     
</script>

<script type="text/javascript">
  // Map your choices to your option value
var lookup = {
   'SAP': ['SAP Technical', 'SAP Functional'],
   'ByDesign': ['ByDesign Technical', 'ByDesign Functional'],
   'IE': ['AIML','Web'],
   // 'Web': ['Web','Ui'],
};

// When an option is changed, search the above for matching choices
$('#division').on('change', function() {
   // Set selected option as variable
   var selectValue = $(this).val();

   // Empty the target field
   $('#choices').empty();
   
   // For each chocie in the selected option
   for (i = 0; i < lookup[selectValue].length; i++) {
      // Output choice in the target field
      $('#choices').append("<option value='" + lookup[selectValue][i] + "'>" + lookup[selectValue][i] + "</option>");
   }
});
</script>
@endsection