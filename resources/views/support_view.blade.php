@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <main class="app-content">
        <div class="container-fluid">
            <div class="col-md-12 user-right">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 style="color:#A04000; margin-bottom:20px">Email Support Lists
                            <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#user"
                                type="submit">Add
                                Ticket</button>
                        </h4>

                        <div class="row cust_data_form">
                            <div class="col-md-2">
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
                            {{-- <div class="col-md-2">
                                <select class="form-control" name="status_filter" id="status_filter">
                                    <option value="">Select Status</option>
                                    <option value="0">Open</option>
                                    <option value="1">Initiated</option>
                                    <option value="2">Work in Progress</option>
                                    <option value="3">Waiting for Customer</option>
                                    <option value="4">Confirmation Pending</option>
                                    <option value="5">Closed</option>
                                </select>
                            </div> --}}

                            <div class="col-md-2">
                                <select class="form-control" name="status_filter" id="status_filter" multiple>
                                    <option value="" disabled>Select Status</option>
                                    <option value="0">Open</option>
                                    <option value="1">Initiated</option>
                                    <option value="2">Work in Progress</option>
                                    <option value="3">Waiting for Customer</option>
                                    <option value="4">Confirmation Pending</option>
                                    <option value="5">Closed</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" name="period_filter" id="period_filter">
                                    <option value="">Select Period</option>
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="week">Last 7 Days</option>
                                    <option value="month">Last 30 Days</option>
                                    <option value="beyondmonth">Beyond 30 Days</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" name="user_filter" id="user_filter" data-live-search="true">
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endForeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" name="domain_filter" id="domain_filter">
                                    <option value="">Select Domain</option>
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->domain }}">{{ $domain->domain }}</option>
                                    @endForeach
                                </select>
                            </div>

                            <div class="col-md-2 margin pull-right no-m-top">
                                <div class="input-group">
                                    <input type="text" class="form-control no-border-right" id="search_support"
                                        placeholder="Search...">
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

                                <div class="table_data" style="width: 100%;overflow: hidden;">

                                    <style>

                                    </style>
                                    <!-- <table id="branch_table" class="table table-striped table-bordered dt-responsive " cellspacing="0" width="100%" data-page-length='10'> -->
                                    <table id="branch_table" class="table table-striped table-bordered nowrap"
                                        style="overflow-x: auto;display: block;">

                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Ticket Id</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Domain</th>
                                                <th>Ticket Subject</th>
                                                <th>Assigned User</th>
                                                <th>Email From</th>
                                                <th>Last Updated user</th>
                                                <th>Re Assigned</th>
                                                <th>Total Time Spent</th>
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
                            <label for="ticket_id">Ticket Subject:</label>
                            <input type="text" class="form-control" id="subject" name="subject">
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
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="update_status" action="javascript:void(0)" method="POST">
                        {!! csrf_field() !!}
                        <h4 class="text-left">Update Status</h4>

                        <select class="form-control" name="update_status" id="update_status">
                            <option value="0">Open</option>
                            <option value="1">Initiated</option>
                            <option value="2">Work in Progress</option>
                            <option value="3">Waiting for Customer</option>
                            <option value="4">Confirmation Pending</option>
                            <option value="5">Closed</option>
                        </select>
                        <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea type="text" class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <input type="hidden" name="old_status" id="old_status" value="">
                        <input type="hidden" name="ticket_id" id="ticket_id">
                        <input type="hidden" name="status_id" id="status_id">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"
                                name="button"></i>Update Status</button>
                    </form>
                    <!-- <div id="body_content"> </div> -->
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="status_log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal_lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="" action="javascript:void(0)" method="POST">
                        {!! csrf_field() !!}

                        <input type="hidden" name="ticket_id" id="ticket_id">
                        <!-- <div id ="add_whole_data" name="add_whole_data"></div> -->
                        <div class="row">
                            <div class="col-md-3">
                                <h6><b>From Status</b></h6>
                            </div>
                            <div class="col-md-3">
                                <h6><b>To Status</b></h6>
                            </div>
                            <div class="col-md-3">
                                <h6><b>Comment</b></h6>
                            </div>
                            <div class="col-md-3">
                                <h6><b>Updated At</b></h6>
                            </div>
                        </div>
                        <div id="add_whole_data" name="add_whole_data">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            let dashboardTicketStatus = sessionStorage.getItem("dashboardTicketStatus");
            let dashboardTicketPeriod = sessionStorage.getItem("dashboardTicketPeriod");
            // if (dashboardTicketStatus) {
            //     $('#status_filter').val(dashboardTicketStatus)
            //     sessionStorage.removeItem('dashboardTicketStatus');
            // }

            if (dashboardTicketStatus) {
                // Split the comma-separated string into an array
                var selectedStatusArray = dashboardTicketStatus.split(',');

                // Set the selected values in the dropdown
                $('#status_filter').val(selectedStatusArray);

                // Remove the session storage item
                sessionStorage.removeItem('dashboardTicketStatus');
            }


            if (dashboardTicketPeriod) {
                $('#period_filter').val(dashboardTicketPeriod)
                sessionStorage.removeItem('dashboardTicketPeriod');
            }

            var BranchListTable = $('#branch_table').DataTable({
                "dom": '<"html5buttons"B>tp',
                "bServerSide": true,
                "serverSide": true,
                "processing": true,
                "bRetrieve": true,
                "paging": true,
                "ajax": {
                    "url": public_path + '/support_json',
                    "type": "GET",
                    "data": function(d) {
                        var selectedStatuses = $('#status_filter').val() || '';

                        return $.extend({}, d, {
                            'branch_count': $('#branch_count').val() || '',
                            "search_support": $('#search_support').val() || '',
                            //"status_filter": $('#status_filter').val() || '',
                            "status_filter": selectedStatuses,
                            "period_filter": $('#period_filter').val() || '',
                            "user_filter": $('#user_filter').val() || '',
                            "domain_filter": $('#domain_filter').val() || '',
                        });
                    }
                },
                "columns": [{
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
                        "data": "status",
                        "name": "status",
                        "defaultContent": '-'
                    },
                    {
                        "data": 'created_at',
                        "name": "created_at",
                        "defaultContent": '-',
                    },
                    //  {
                    //     "data": "",
                    //     "name": "",
                    //     "defaultContent": '-'
                    //  },
                    {
                        "data": "domain",
                        "name": "domain",
                        "defaultContent": '-'
                    },
                    {
                        "data": "subject",
                        "name": "subject",
                        "defaultContent": '-'
                    },

                    {
                        "data": "assigned",
                        "name": "assigned",
                        "defaultContent": '-'
                    },
                    {
                        "data": "email_from",
                        "name": "email_from",
                        "defaultContent": '-'
                    },
                    {
                        "data": "last_updated_user",
                        "name": "last_updated_user",
                        "defaultContent": '-'
                    },

                    {
                        "data": "re_assigned",
                        "name": "re_assigned",
                        "defaultContent": '-'
                    },

                    {
                        "data": "total_hours",
                        "name": "total_hours",
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

                    var view_url = public_path + '/view_ticket/' + aData['id'];

                    var view_link = '<td><div><a href=' + view_url + '>' + aData['ticket_id'] +
                        '</a></div></td>';
                    $('td:eq(1)', nRow).html(view_link);

                    // var action1 = '<td><a data-toggle="modal" data-target="#status_update" class="btn btn-primary" data-id="'+aData['id']+'" style="color: #fff;" onclick="addDataValue('+aData['status_id']+','+aData['id']+');">Update</a>&nbsp&nbsp';
                    //    action1 += '</td>';
                    //  $('td:eq(4)', nRow).html(action1);

                    var view_status = public_path + '/support_view';

                    var status_link = '<td><div><a data-toggle="modal" onclick="viewStatus(' + aData[
                            'id'] + ');" data-target="#status_log" href=' + view_status + '>' + aData[
                            'status'] +
                        '</a><a data-toggle="modal" data-target="#status_update" class="update-link" data-id="' +
                        aData['id'] + '" onclick="addDataValue(' + aData['status_id'] + ',' + aData[
                            'id'] + ');">Update</a></div></td>';
                    $('td:eq(3)', nRow).html(status_link);


                    var subject = '<td><a href=' + view_url + '><div class="tooltip-in-table">' + aData[
                            'subject'].slice(0, 35) + '<span class="tooltiptext">' + aData['subject'] +
                        '</span></div></a></td>';

                    $('td:eq(7)', nRow).html(subject);

                     var view_status = public_path+'/support_view/';

                    // var status_view = '<td><div><a href='+view_status+'>'+aData['status']+'</a></div></td>';
                    //   $('td:eq(5)', nRow).html(status_view);
                    // var totalHours = '<td>' + aData['total_hours'] + '</td>';
                    // $('td:eq(13)', nRow).html(totalHours);
                    //-----------------------------------------------------------------
                    // var totalHours = '<td>' + (aData['total_hours'] !== null ? aData['total_hours'] :
                    //     0) + '</td>';
                    // $('td:eq(13)', nRow).html(totalHours);

                    var existingTotalHours = aData['total_hours'];
                    var displayTotalHours = existingTotalHours !== null ? existingTotalHours : '00:00';

                    var totalHours = '<td>' + displayTotalHours + '</td>';
                    $('td:eq(13)', nRow).html(totalHours);
                },

            });


            $('#search_support').on('keyup', function() {
                BranchListTable.draw();
            });

            // $('#status_filter').change(function() {
            //     BranchListTable.page.len($('#branch_count').val()).draw();
            // });

            $('#status_filter').change(function() {
                BranchListTable.draw();
            });

            $('#user_filter').change(function() {
                BranchListTable.page.len($('#branch_count').val()).draw();
            });

            $('#domain_filter').change(function() {
                BranchListTable.page.len($('#branch_count').val()).draw();
            });

            $('#branch_count').change(function() {
                BranchListTable.page.len($('#branch_count').val()).draw();
            });


        });

        function appendUserId(user_id) {

            $.ajax({
                url: public_path + '/view_email_body/' + user_id,
                method: 'get',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == 1) {

                        //alert(result.mail_body);


                        $('#body_content').html(result.mail_body.body);

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

        function addDataValue(status, id) {
            //alert("ah");
            $('#old_status').val(status);
            $('#status_id').val(id);
            $('#ticket_id').val(id);

        }

        function viewStatus(ticket_id) {
            $('#ticket_id').val(ticket_id);

            $.ajax({
                url: public_path + '/get_status_loop/' + ticket_id,
                method: 'get',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == 1) {
                        // console.log(result.status_data);return;
                        //   alert(result.status_data.ticket_id);
                        var data = result.status_data;
                        var add_row = '';
                        Object.values(data).forEach(val => {

                            add_row += '<div class="row">\
                                                                                                        <div class="col-md-3">\
                                                                                                        <p id="old_log">' +
                                val
                                .old_status_display +
                                '</p>\
                                                                                                        </div>\
                                                                                                        <div class="col-md-3">\
                                                                                                          <p id="current_log">' +
                                val
                                .status_display +
                                '</p>\
                                                                                                        </div>\
                                                                                                        <div class="col-md-3">\
                                                                                                           <p id="comment_log">' +
                                val
                                .comment +
                                '</p>\
                                                                                                        </div>\
                                                                                                        <div class="col-md-3">\
                                                                                                          <p id="created_log">' +
                                val
                                .created_at + '</p>\
                                                                                                        </div>\
                                                                                                </div>';
                            // console.log(val.old_status);return ;
                        });
                        $('#add_whole_data').html(add_row);


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

    <script type="text/javascript">
        $(document).ready(function() {

            $("#add_ticket").validate({
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
                    subject: {
                        required: true,
                    },
                },
                /* @validation error messages
                ---------------------------------------------- */

                messages: {
                    ticket_summary: {
                        required: 'Please Enter Ticket Summary'
                    }

                },
                submitHandler: function(form) {

                    $.ajax({
                        url: public_path + '/add_ticket',
                        method: 'post',
                        data: new FormData($("#add_ticket")[0]),
                        dataType: 'json',
                        async: false,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.success == 1) {
                                // alert("Ticket Details Added Successfully.!")

                                // location.reload();
                                Swal.fire({
                                    type: 'success',
                                    title: result.message,
                                    showConfirmButton: true,

                                });
                                $('#user').modal('hide');
                                $('#ticket_summary').val('');
                                $('#subject').val('');
                                $('#branch_table').DataTable().ajax.reload(null, false);
                                // location.reload();
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
            });

            $("#update_status").validate({
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

                messages: {
                    update_status: {
                        required: 'Please Update Status'
                    }
                },
                submitHandler: function(form) {

                    $.ajax({
                        url: public_path + '/update_status',
                        method: 'post',
                        data: new FormData($("#update_status")[0]),
                        dataType: 'json',
                        async: false,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.success == 1) {

                                // alert("Status Updated Successfully.!")
                                Swal.fire({
                                    type: 'success',
                                    title: result.message,
                                    showConfirmButton: true,

                                });
                                $('#status_update').modal('hide');
                                $('#branch_table').DataTable().ajax.reload(null, false);

                                //  location.reload();
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
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#user_filter").select2();
            $("#domain_filter").select2();
            $("#status_filter").select2();
        });
    </script>
@endsection

@push('scripts')
    <script>
        console.log(sessionStorage.getItem('dashboardTicketStatus'));
        console.log(sessionStorage.getItem('dashboardTicketPeriod'));
    </script>
@endpush
