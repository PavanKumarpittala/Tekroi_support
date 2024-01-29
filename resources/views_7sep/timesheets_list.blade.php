@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container-fluid">
  <div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading">
      <span>Timesheets List</span>

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
                  <!-- <div class="col-md-3">
                  <div class="form-group">
                     <input type="search" id="user_select" class="form-control" name="user_select" onkeyup="getRecords(1);" placeholder="Serach User">
                    
                  </div>
                </div> 
 -->
                <div class="col-md-6"></div>
                <!-- <div class="col-md-3">  
                  <div class="form-group">
                    <label for="date">From Date:</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" >
                  </div>
                 
                </div>
              
                <div class="col-md-3">  
                  <div class="form-group">
                    <label for="date">To Date:</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" >
                  </div>
                 
                </div> -->
                  
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
                        <th>User Name</th>
                        <th>Project Name</th>
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
</div>
</div>
</main>


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
             "url": public_path+'/timesheets_json',
             "type": "GET",
             "data": function(d) {
                 return $.extend({}, d, {
                    'branch_count' : $('#branch_count').val() || '',
                    "search_user": $('#search_user').val() || '',
                 });
             }
         },
           
        // "columnDefs": [
        //     { "width": "0%", "targets": 0 },
        //     { "width": "0%", "targets": 1 },
        //     { "width": "10%", "targets": 2 },
        //     { "width": "0%", "targets": 3 },
        //     { "width": "0%", "targets": 4 },
        //     { "width": "0%", "targets": 5 },
        //     { "width": "0%", "targets": 6 },
        //     { "width": "15%", "targets": 7 }
        //   ],
    
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
             "data": "project_name",
             "name": "project_name"
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

         "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            var page = this.fnPagingInfo().iPage;
            var length = this.fnPagingInfo().iLength;
            var index  = (page * length + (iDisplayIndex +1));

            $('td:eq(0)', nRow).html(index); 
            var action1 = '<td class="admin-center"><a href="javascript:void(0)" class="edit_branch" data-branch-id="'+aData['id']+'" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp&nbsp';

            action1 += '<a data-toggle="modal" data-target="#commodity" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" >Add Commodity</a>&nbsp&nbsp';
             action1 += '</td>';
             var action2 = '<td class="admin-center"><a href="javascript:void(0)" class="edit_branch" data-branch-id="'+aData['id']+'" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp&nbsp';
               if(aData['is_specific']){
                action2+="Yes";
               }else{
                action2+="No";
               }

              action2 += '&nbsp&nbsp<a data-toggle="modal" data-target="#specific_customer" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" onclick="appendUserId('+aData['id']+');">Update</a>&nbsp&nbsp';
              action2 += '</td>';             

             // $('td:eq(6)', nRow).html(action1);
             // $('td:eq(8)', nRow).html(action2);
          },

    }); 

     
     $('#search_user').on('keyup', function () {
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

<script type="text/javascript">
  var minDate, maxDate;
 
// Custom filtering function which will search data in column four between two values
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = minDate.val();
        var max = maxDate.val();
        var date = new Date( data[4] );
 
        if (
            ( min === null && max === null ) ||
            ( min === null && date <= max ) ||
            ( min <= date  && max === null ) ||
            ( min <= date  && date <= max )
        ) {
            return true;
        }
        return false;
    }
);
 
$(document).ready(function() {
    // Create date inputs
    minDate = new DateTime($('#start_date'), {
        format: 'MMMM Do YYYY'
    });
    maxDate = new DateTime($('#end_date'), {
        format: 'MMMM Do YYYY'
    });
 
    // DataTables initialisation
    var table = $('#branch_table').DataTable();
 
    // Refilter the table
    $('#start_date, #end_date').on('change', function () {
        table.draw();
    });
});
</script>
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
 --><script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>
@endsection