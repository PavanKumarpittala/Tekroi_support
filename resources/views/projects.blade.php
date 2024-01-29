@extends('layouts.app')

@section('content')

<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <h4 style="color:#A04000">Projects List</h4>

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
                @if(Auth::user()->role != 3 && Auth::user()->role != 4)
                <div class="col-md-3 margin pull-right ">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#project" type="submit">Add Customer</button>
                </div>
                @endif


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
                        <th>Action</th>
                        <th>Project Name</th>
                        <th>Project Members Count</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Customer Mobile</th>
                        <th>Customer Designation</th>
                        <th>Division</th>
                        <th>Pan Number</th>
                        <th>Gst Number</th>
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
          <h4 class="modal-title">Add Customer Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form id="add_project" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}

         <div class="form-group">
          <label for="project_name" class="required">Project Name:</label>
          <input type="text" class="form-control" id="project_name" name="project_name" placeholder="Enter Project Name">
        </div>
         <div class="form-group">
          <label for="customer_name" class="required">Customer Name:</label>
          <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter Customer Name">
        </div>
        <div class="form-group">
          <label for="customer_email" class="required">Customer Email:</label>
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
          <label for="name" class="required">Primary Contact:</label>
           <input type="text" class="form-control" id="name" name="name[]" placeholder="Enter contact name">
          <input type="email" class="form-control" id="email" name="email[]" placeholder="Enter contact email">
          <input type="text" class="form-control" id="mobile" name="mobile[]" placeholder="Enter contact mobile">
          <input type="text" class="form-control" id="designation" name="designation[]" placeholder="Enter contact designation">
          <div class="fa fa-plus-circle text-primary add"> Add Sub Contacts</div>
        </div>
        <div class="form-group">
          <label for="division" class="required">Main Division:</label>
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
        <div class="form-group">
          <label for="pan_number">Pan Number</label>
          <input type="text" class="form-control" id="pan_number" name="pan_number">
        </div>
        <div class="form-group">
          <label for="gst_number">Gst Number</label>
          <input type="text" class="form-control" id="gst_number" name="gst_number">
        </div>
        <div class="form-group">
          <label for="employees">Assign Employees</label>
          <select name="employees" id="employees" multiple class="form-control selectpicker">
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

 <!-- Edit -->
 <div class="modal fade" id="update_customers">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Edit Customer Details</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form id="update_customer" action="javascript:void(0)" method="POST">
            {{ csrf_field() }}

        <input type="hidden" name="id" id="id">
        <div class="form-group">
          <label for="update_customer_email">Customer Email:</label>
          <input type="email" class="form-control" id="update_customer_email" name="update_customer_email" >
        </div>
        <div class="form-group">
          <label for="update_customer_mobile">Customer Mobile No:</label>
          <input type="text" class="form-control" id="update_customer_mobile" name="update_customer_mobile" >
        </div>
        <div class="form-group">
          <label for="update_customer_designation">Customer Designation:</label>
          <input type="text" class="form-control" id="update_customer_designation" name="update_customer_designation" >
        </div>
        <div class="form-group list">
          <label for="name">Primary Contact:</label>
           <input type="text" class="form-control" id="update_name" name="update_name" ><br>
          <input type="email" class="form-control" id="update_email" name="update_email" ><br>
          <input type="text" class="form-control" id="update_mobile" name="update_mobile" ><br>
          <input type="text" class="form-control" id="update_designation" name="update_designation" >
          <!-- <div class="fa fa-plus-circle text-primary add"> Add Sub Contacts</div> -->
        </div>

        <div class="form-group">
          <label for="update_pan_number">Pan Number</label>
          <input type="text" class="form-control" id="update_pan_number" name="update_pan_number">
        </div>
        <div class="form-group">
          <label for="update_gst_number">Gst Number</label>
          <input type="text" class="form-control" id="update_gst_number" name="update_gst_number">
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
             "data": "",
             "name": "",
             "defaultContent": '-'
         },
         {
             "data": "project_name",
             "name": "project_name",
             "defaultContent": '-'
         },
         {
             "data": "employees",
             "name": "employees",
             "defaultContent": '-'
         },
         {
             "data": "customer_name",
             "name": "customer_name",
             "defaultContent": '-'
         },
         {
             "data": "customer_email",
             "name": "customer_email",
             "defaultContent": '-'
         },
         {
             "data": "customer_mobile",
             "name": "customer_mobile",
             "defaultContent": '-'
         },
         {
             "data": "customer_designation",
             "name": "customer_designation",
             "defaultContent": '-'
         },

         {
             "data": "division",
             "name": "division",
             "defaultContent": '-'
         },
         {
             "data": "pan_number",
             "name": "pan_number",
             "defaultContent": '-'
         },
         {
             "data": "gst_number",
             "name": "gst_number",
             "defaultContent": '-'
         },
         {
             "data": "start_date",
             "name": "start_date",
             "defaultContent": '-'
         },
         {
             "data": "status_display",
             "name": "status_display",
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

            var view_customer = public_path+'/view_customer/'+aData['id'];

            var action1 = '<td class="admin-center">';

            action1 += '<a href='+view_customer+' title="View Customer"><i class="fa fa-eye" aria-hidden="true" style="font-size:22px;text-align:center;color:#c09500;cursor: pointer;"></i></a>&nbsp&nbsp';

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

             action1 += '<a data-toggle="modal" data-target="#delete_customer"  title="'+statusData+'" onclick="deleteCustomer('+aData['id']+','+aData['status']+');"><i class="fa fa-trash" aria-hidden="true" style="font-size:22px;text-align:center;color:#d33;cursor: pointer;"></i></a>&nbsp&nbsp';

             action1 += '</td>';


              $('td:eq(1)', nRow).html(action1);

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
          customer_name: {
              required: true,
          },
          customer_email: {
              required: true,
          },
          division: {
              required: true,
          },

         "name[]": {
             required: true,
          },
          "email[]": {
             required: true,
          },
          "mobile[]": {
             required: true,
          },
          "designation[]": {
             required: true,
          }
        },
      /* @validation error messages
      ---------------------------------------------- */

      messages:{
          project_name: {
              required: 'Please add Project Name'
          },
          customer_name: {
              required: 'Please add Customer Name'
          },
          customer_email: {
              required: 'Please add Customer Email'
          },
          division: {
              required: 'Please add division'
          },
          name: {
              required: 'Please add primary contact Name'
          },
          email: {
              required: 'Please add primary contact Email'
          },
          mobile: {
              required: 'Please add primary contact Mobile'
          },
          designation: {
              required: 'Please add primary contact Designation'
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

  $("#update_customer" ).validate({
      errorClass: "state-error",
      validClass: "state-success",
      errorElement: "em",
      ignore: [],

      /* @validation rules
      ------------------------------------------ */
      rules: {
          update_customer_designation: {
              required: true,
          },
        },
      /* @validation error messages
      ---------------------------------------------- */

      messages:{
          update_customer_designation: {
              required: 'Please add Customer Designation'
          }

      },
      submitHandler: function (form) {


    $.ajax({
            url:public_path + '/update_customer',
            method:'post',
            data:new FormData($("#update_customer")[0]),
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


     function editCustomer(id)
        {
         $('#id').val(id);
            $.ajax({
                    url:public_path + '/get_customer/'+id,
                    method:'get',
                    dataType:'json',
                    cache: false,
                    processData:false,
                    contentType:false,
                    success:function(result){
                       if(result.success==1){

                        $('#update_customer_email').val(result.customer_data.customer_email);
                        $('#update_customer_mobile').val(result.customer_data.customer_mobile);
                        $('#update_customer_designation').val(result.customer_data.customer_designation);
                        $('#update_name').val(result.customer_data.contact_name);
                        $('#update_email').val(result.customer_data.contact_email);
                        $('#update_mobile').val(result.customer_data.contact_mobile);
                        $('#update_designation').val(result.customer_data.contact_designation);
                        $('#update_pan_number').val(result.customer_data.pan_number);
                        $('#update_gst_number').val(result.customer_data.gst_number);


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

  function deleteCustomer(id,status)
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
            url:public_path + '/delete_customer/'+id+'/'+status,
            method:'get',
            data:new FormData($("#delete_customer")[0]),
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

$('#employees').change(function(){
    $('#hidden_id').val($('#employees').val());
    var query = $('#hidden_id').val();
  });
</script>

@endsection
