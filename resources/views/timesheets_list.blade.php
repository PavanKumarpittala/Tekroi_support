@extends('layouts.app')

@section('content')
    <main class="app-content">
        <div class="container-fluid">
            <div class="col-md-12 user-right">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 style="color:#A04000">Timesheets List</h4>

                        <div class="row cust_data_form">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true"
                                        id="branch_count">
                                        <option selected="selected">10</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    {{-- <input type="text" id="user_select" class="form-control" name="user_select"  placeholder="Serach User..."> --}}
                                    <select class="form-control" name="user_select" id="user_select">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endForeach
                                    </select>
                                </div>
                            </div>



                            {{-- <div class="col-md-3">  
                  <div class="form-group">
                    <input type="date" data-date="" data-date-format="DD/MM/YYYY" class="form-control" id="select_date" name="select_date" >
                  </div>
                 
                </div> --}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" data-date="" data-date-format="MM/DD/YYYY" class="form-control"
                                        id="min" name="min">
                                </div>

                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" data-date="" data-date-format="MM/DD/YYYY" class="form-control"
                                        id="max" name="max">
                                </div>

                            </div>

                            @if (Auth::user()->role != 3)
                                <div class="col-md-3 margin pull-right no-m-top">
                                    <div class="input-group">
                                        <input type="text" class="form-control no-border-right" id="search_user"
                                            placeholder="Search Project...">
                                        <div class="input-group-addon">
                                            <i class="fa fa-search sear"></i>
                                        </div>
                                    </div>
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

                                    <!--  <table id="branch_table" class="table table-striped table-bordered dt-responsive nowrap branch_table" cellspacing="0" width="100%" data-page-length='10'> -->

                                    <table id="branch_table" class="table table-striped table-bordered nowrap"
                                        style="overflow-x: auto;display: block;">
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
                                                <th>Status</th>
                                                <th>Assigned By</th>
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

    <div class="modal fade" id="view_description" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal_lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Descriptions</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="" action="javascript:void(0)" method="POST">
                        {!! csrf_field() !!}

                        <input type="hidden" name="id" id="id">

                        <pre style="white-space: normal;"><div id ="add_whole_data" name="add_whole_data"></div></pre>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var BranchListTable = $('#branch_table').DataTable({
                "dom": '<"html5buttons"B>tp',
                "bServerSide": true,
                "serverSide": true,
                "processing": true,
                "bRetrieve": true,
                "paging": true,
                "ajax": {
                    "url": public_path + '/timesheets_json',
                    "type": "GET",
                    "data": function(d) {
                        return $.extend({}, d, {
                            'branch_count': $('#branch_count').val() || '',
                            "search_user": $('#search_user').val() || '',
                            "user_select": $('#user_select').val() || '',
                            // "select_date": $('#select_date').val() || '',
                            "min": $('#min').val() || '',
                            "max": $('#max').val() || '',
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

                "columns": [{
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
                        "data": "",
                        "name": "",
                        "defaultContent": '-'
                    },
                    {
                        "data": "status",
                        "name": "status",
                        "defaultContent": '-'
                    },
                    {
                        "data": "who_assigned",
                        "name": "who_assigned",
                        "defaultContent": '-'
                    },
                ],

                "order": [
                    [0, "desc"]
                ],

                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    var page = this.fnPagingInfo().iPage;
                    var length = this.fnPagingInfo().iLength;
                    var index = (page * length + (iDisplayIndex + 1));

                    $('td:eq(0)', nRow).html(index);
                    var action1 =
                        '<td><a data-toggle="modal" data-target="#view_description" class="btn btn-primary" data-id="' +
                        aData['id'] + '" style="color: #fff;" onclick="viewDescription(' + aData['id'] +
                        ');">View Description</a>&nbsp&nbsp';
                    action1 += '</td>';
                    $('td:eq(7)', nRow).html(action1);

                },

            });


            $('#search_user').on('keyup', function() {
                BranchListTable.draw();
            });

            // $('#user_select').on('keyup', function() {
            //     BranchListTable.draw();
            // });
//------------This i s My NEW Code--------------------------------->
            $('#user_select').on('change', function() {
                BranchListTable.draw();
            });
//-----------------end--------------------------------

            $('#min').on('change', function() {
                BranchListTable.draw();
            });

            // Event listener for the "max" input field
            $('#max').on('change', function() {
                BranchListTable.draw();
            });
            $('#branch_count').on('change', function() {
                BranchListTable.page.len($(this).val()).draw();
            });

            // $('#select_date').change(function(){
            //   BranchListTable.page.len( $('#branch_count').val() ).draw();
            // });

            $('#branch_count').change(function() {
                BranchListTable.page.len($('#branch_count').val()).draw();
            });


            $(document).delegate('td>a.edit_branch', 'click', function() {
                var id = $(this).attr('data-branch-id');
                window.location.href = public_path + '/edit_branch/' + id;
            });



        });
    </script>

    <script type="text/javascript">
        function viewDescription(id) {
            $('#id').val(id);

            $.ajax({
                url: public_path + '/get_timesheet_description/' + id,
                method: 'get',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == 1) {
                        // console.log(result.status_data);return;
                        //   alert(result.status_data.ticket_id);
                        var data = result.description_data.description;

                        // Object.values(data).forEach(val => {
                        // console.log(data);return ;


                        //  console.log(data.description);
                        // });
                        $('#add_whole_data').html(data);


                    } else {
                        swal("Error", result.message, "warning");
                    }
                },
                error: function(error) {
                    if (error) {
                        var error_status = error.responseText;
                        alert(error_status.message);
                    }
                }
            });




        }
    </script>
    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
            <script type="text/javascript" src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
             -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>
@endsection
