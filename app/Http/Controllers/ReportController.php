<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('report');
    }

    public function reportJson(Request $request)
    {
        if (Auth::user()->role != 1) return;

        $data = $request->all();

        if (isset($data['draw'])) {

            $monthFilter = $data['month_filter'];
            $yearFilter = $data['year_filter'];

            $columnArray
                = array(
                    'id',
                    'employee_id',
                    'name',
                    'email',
                    'marked_days',
                    'marked_hours',
                    'entered_days',
                );

            try {

                DB::enableQueryLog();

                /**
                 * Database query object selection
                 */

                $query = DB::table('users as u')
                            ->where('u.role', '!=', 4)
                            ->where('status', 1);

                $getHoursSql = "SUBSTRING_INDEX(LOWER(total_time), 'h', 1)";
                $getMinsSql = "FLOOR(SUBSTRING_INDEX(REPLACE(LOWER(total_time), 'm', '') , 'h', -1) / 60)";
                $getMinsRemainderSql = "LPAD(MOD(SUM(SUBSTRING_INDEX(REPLACE(LOWER(total_time), 'm', '') , 'h', -1)), 60), 2, '0')";

                $query->select(
                    'u.id',
                    'u.employee_id',
                    'u.name',
                    'u.email',
                    DB::raw("(SELECT COUNT(DISTINCT DATE(date)) AS num_days
                    FROM timesheets as t WHERE MONTH(date) = $monthFilter AND YEAR(date) = $yearFilter
                    AND t.user_id=u.id) as marked_days"),

                    DB::raw("(SELECT CONCAT(SUM($getHoursSql+$getMinsSql), 'h',  $getMinsRemainderSql , 'm' ) AS TotalTime FROM timesheets as t WHERE t.user_id=u.id AND MONTH(date) = $monthFilter AND YEAR(date) = $yearFilter)
                    as marked_hours"),

                    DB::raw("(SELECT GROUP_CONCAT(DISTINCT DAY(date)) AS num_days FROM timesheets as t WHERE MONTH(date) = $monthFilter AND YEAR(date) = $yearFilter AND t.user_id=u.id)
                    as entered_days"),
                );


                if ($data['search_report'] != '') {
                    $query->whereRaw(
                        "u.employee_id like '%" . $data['search_report'] . "%' || u.name like '%" . $data['search_report'] . "%' || u.email like '%" . $data['search_report'] . "%'"
                    );
                }

                // if ($data['status_filter'] != '') {

                //     $query->whereRaw(
                //         "s.status = '" . $data['status_filter'] . "'"
                //     );
                // }

                // if ($data['user_filter'] != '') {

                //     $query->whereRaw(
                //         "assigned = '" . $data['user_filter'] . "'"
                //     );
                // }

                if (isset($data['branch_count'])) {
                    $count = $data['branch_count'];
                } else {
                    $count = '10';
                }

                $userCount = count($query->get());
                /**
                 * Order by
                 */


                if (isset($data['order'])) {
                    $query->orderBy(
                        $columnArray[$data['order'][0]['column']],
                        $data['order'][0]['dir']
                    );
                }

                /**
                 * Apply limit
                 */
                if ($data['length'] != -1) {
                    $query->skip($data['start'])->take($count);
                }
                // echo "<pre>"; print_r($query->toSql());
                /**
                 * Get
                 */
                $feedbacks = $query->get();
            } catch (\Exception $e) {
                $feedbacks = [];
                $userCount = 0;
            }

            $response['draw'] = $data['draw'];
            $response['recordsTotal'] = $userCount;
            $response['recordsFiltered'] = $userCount;

            $response['data'] = $feedbacks;

            // print_r(DB::getQueryLog());

            return response()->json($response);
        }
    }

    public function getUserTimelog($user_id, $month, $year)
    {
        $timelogs = DB::table('timesheets as t')
                    ->select('p.project_name',
                     (DB::raw("DATE_FORMAT(t.date, '%d-%m-%Y') as date")),
                     't.status', 't.start_time', 't.end_time', 't.total_time')
                    ->leftJoin('users as u', 'u.id', '=', 't.user_id')
                    ->leftJoin('projects as p', 'p.id', '=', 't.project_name')
                    ->where('t.user_id', $user_id)
                    ->whereRaw("MONTH(t.date) = $month AND YEAR(t.date) = $year")
                    ->orderBy('t.date', 'desc')
                    ->get();

        return response()->json([
            'timelogs' => $timelogs ,
            'success' => true,
            'count' => count($timelogs)
        ]);
    }
}
