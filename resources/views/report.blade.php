@extends('layouts.app')

@section('content')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> --}}
    <main class="app-content">
        <div class="container-fluid">
            <div class="col-md-12 user-right">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 style="color:#A04000">Timesheet Report</h4>

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
                            <div class="col-md-2">

                            </div>

                            <div class="col-md-2">
                                <?php
                                $months = [
                                    1 => 'JAN',
                                    2 => 'FEB',
                                    3 => 'MAR',
                                    4 => 'APR',
                                    5 => 'MAY',
                                    6 => 'JUN',
                                    7 => 'JUL',
                                    8 => 'AUG',
                                    9 => 'SEP',
                                    10 => 'OCT',
                                    11 => 'NOV',
                                    12 => 'DEC',
                                ];

                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                ?>
                                <select class="form-control" name="month_filter" id="month_filter">
                                    @foreach ($months as $number => $month)
                                        <option @if ($number == $currentMonth) selected @endif
                                            value="{{ $number }}">{{ $month }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="year_filter" id="year_filter">

                                    @for ($i = 2023; $i <= $currentYear; $i++)
                                        <option @if ($number == $currentMonth) selected @endif
                                            value="{{ $i }}">{{ $i }}</option>
                                    @endfor

                                </select>
                            </div>
                            <div class="col-md-1">

                            </div>
                            <div class="col-md-3 margin pull-right no-m-top">
                                <div class="input-group">
                                    <input type="text" class="form-control no-border-right" id="search_report"
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

                                    <table id="report_table" class="table table-striped table-bordered nowrap"
                                        style="overflow-x: auto;display: block;">

                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Emp. Id</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Marked Days</th>
                                                <th>Marked Hours</th>
                                                <th>Missing Dates</th>
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

    <div class="modal fade" id="time_log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog vertical-align-center modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="display_username"></h3>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>

                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <h6><b>Project Name</b></h6>
                            </div>
                            <div class="col-md-2">
                                <h6><b>Date</b></h6>
                            </div>
                            <div class="col-md-2">
                                <h6><b>Start Time</b></h6>
                            </div>
                            <div class="col-md-2">
                                <h6><b>End Time</b></h6>
                            </div>
                            <div class="col-md-2">
                                <h6><b>Total Time</b></h6>
                            </div>
                            <div class="col-md-2">
                                <h6><b>Status</b></h6>
                            </div>
                        </div>
                        <div id="display_timelog"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var BranchListTable = $('#report_table').DataTable({
                "dom": '<"html5buttons"B>tp',
                "bServerSide": true,
                "serverSide": true,
                "processing": true,
                "bRetrieve": true,
                "paging": true,
                "ajax": {
                    "url": public_path + '/report_json',
                    "type": "GET",
                    "data": function(d) {
                        return $.extend({}, d, {
                            'branch_count': $('#branch_count').val() || '',
                            "search_report": $('#search_report').val() || '',
                            "month_filter": $('#month_filter').val() || '',
                            "year_filter": $('#year_filter').val() || '',
                        });
                    }
                },
                "columns": [{
                        "data": "id",
                        "name": "id",
                        "defaultContent": '-'
                    },
                    {
                        "data": "employee_id",
                        "name": "employee_id",
                        "defaultContent": '-'
                    },
                    {
                        "data": "name",
                        "name": "name",
                        "defaultContent": '-'
                    },
                    {
                        "data": 'email',
                        "name": "email",
                        "defaultContent": '-',
                    },

                    {
                        "data": "marked_days",
                        "name": "marked_days",
                        "defaultContent": '-'
                    },
                    {
                        "data": "marked_hours",
                        "name": "marked_hours",
                        "defaultContent": '-',
                        "orderable": false
                    },

                    {
                        "data": "entered_days",
                        "name": "entered_days",
                        "defaultContent": '-',
                        "orderable": false
                    },


                ],

                "order": [
                    [1, "asc"]
                ],

                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                    var page = this.fnPagingInfo().iPage;
                    var length = this.fnPagingInfo().iLength;
                    var index = (page * length + (iDisplayIndex + 1));

                    $('td:eq(0)', nRow).html(index);
                    if (aData['marked_hours'])
                        var timesheet_link = '<td><div><a data-toggle="modal" onclick="viewTimesheet(' +
                            aData['id'] + ', \'' + aData['name'] + '\');" data-target="#time_log" href=#>' + aData[
                            'marked_hours'] + '</a></div></td>';
                    else
                        var timesheet_link = '<td>-</td>';

                    // var timesheet_link = '<td>test</td>';
                    $('td:eq(5)', nRow).html(timesheet_link);


                    /* Display numer of Missing days */
                    let days = aData['entered_days'];
                    let inputMonth = $('#month_filter').val();
                    let inputYear = $('#year_filter').val();
                    var daysArray = [];
                    if (typeof days === 'string') {
                        var daysArray = days.split(',');
                    }
                    const now = new Date();
                    const currentMonth = now.getMonth() + 1;
                    const currentYear = now.getFullYear();
                    const today = now.getDate();

                    var numDaysInMonth = new Date(inputYear, inputMonth, 0).getDate();

                    if (inputMonth == currentMonth && inputYear == currentYear)
                        var numDaysInMonth = today;

                    if (inputYear == currentYear && inputMonth > currentMonth)
                        var numDaysInMonth = 0;

                    var daysList = '';
                    for (let i = 1; i <= numDaysInMonth; i++) {
                        if (!daysArray.includes(i.toString())) {
                            // console.log(i + ', ');
                            daysList += "<span>" + i + ", </span>";
                        }
                    }
                    var daysListDisplay = '<td>' + daysList + '</td>';
                    $('td:eq(7)', nRow).html(daysListDisplay);


                    // var view_status = public_path + '/support_view';




                    // var subject = '<td><a href=' + view_url + '><div class="tooltip-in-table">' + aData[
                    //         'subject'].slice(0, 35) + '<span class="tooltiptext">' + aData['subject'] +
                    //     '</span></div></a></td>';

                    // $('td:eq(7)', nRow).html(subject);
                },

            });


            $('#search_report').on('keyup', function() {
                BranchListTable.draw();
            });

            $('#month_filter').change(function() {
                // BranchListTable.page.len($('#branch_count').val()).draw();
                BranchListTable.draw();
            });

            $('#year_filter').change(function() {
                // BranchListTable.page.len($('#branch_count').val()).draw();
                BranchListTable.draw();
            });

            $('#branch_count').change(function() {
                BranchListTable.page.len($('#branch_count').val()).draw();
            });


        });

        function viewTimesheet(user_id, uname) {
            let inputMonth = $('#month_filter').val();
            let inputYear = $('#year_filter').val();

            $('#display_username').html(uname);

            $.ajax({
                url: public_path + '/get_user_timelog/' + user_id + '/' + inputMonth + '/' + inputYear,
                method: 'get',
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.success == true) {
                        var data = result.timelogs;
                        var add_row = '';
                        Object.values(data).forEach(val => {

                            add_row += '<hr> <div class="row">\
                                                    <div class="col-md-2">\
                                                    <p id="old_log">' + val.project_name + '</p>\
                                                    </div>\
                                                    <div class="col-md-2">\
                                                      <p id="current_log">' + val.date + '</p>\
                                                    </div>\
                                                    <div class="col-md-2">\
                                                       <p id="comment_log">' + val.start_time + '</p>\
                                                    </div>\
                                                    <div class="col-md-2">\
                                                      <p id="created_log">' + val.end_time + '</p>\
                                                    </div>\
                                                    <div class="col-md-2">\
                                                      <p id="created_log">' + val.total_time + '</p>\
                                                    </div>\
                                                    <div class="col-md-2">\
                                                      <p id="created_log">' + val.status + '</p>\
                                                    </div>\
                                            </div> ';
                            // console.log(val.old_status);return ;
                        });
                        $('#display_timelog').html(add_row);


                    } else {
                        swal("Error", result.message, "warning");
                    }
                },
                error: function(error) {
                    console.log('error', error);
                    if (error) {
                        var error_status = error.responseText;
                        alert(error_status.message);
                    }
                }
            });

        }
    </script>


    <script type="text/javascript">
        // $(document).ready(function() {
        //     $("#user_filter").select2();
        // });
    </script>
@endsection
