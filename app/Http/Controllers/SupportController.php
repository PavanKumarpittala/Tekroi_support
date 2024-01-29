<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use DB;
use Illuminate\Support\Facades\DB;
use Response;
use Mail;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function supportView()
    {

        $support_data = DB::table('support_tickets')->select('*')->get();
        $users = DB::table('users')->select('name', 'id')->whereNotIn('role', [4])->where('status', 1)->orderBy('name')->get();
        $domains = DB::table('user_domains')->select('*')->orderBy('domain')->groupBy('domain')->get();
       // $total_hours = DB::table('ticket_details')->sum('total_hours');
        //dd( $total_hours);

        // $support_status = DB::table('support_tickets')->select('status')
        // ->get();

        // echo "<pre>"; print_r($support_status);exit;
        $support_emails = DB::table('support_tickets as st')
            ->select('st.*', 'e.subject')
            ->leftJoin('support_email as e', 'st.id', '=', 'e.id')
            //->leftJoin('ticket_details as td', 'st.ticket_id', '=', 'td.id') // Assuming ticket_id is the foreign key in support_tickets referencing ticket_details

            ->get();
        // echo "<pre>"; print_r($support_emails);exit;

        // Fetch total_hours from ticket_details based on ticket_id
        $total_hours = DB::table('support_tickets as st')
            ->select('st.id', 'td.total_hours')
            ->leftJoin('ticket_details as td', 'st.ticket_id', '=', 'td.ticket_id')
            ->get();
        // echo "<pre>"; print_r($total_hours);exit;
        return view(
            'support_view',
            [
                'support_data' => $support_data,
                'support_emails' => $support_emails,
                'users' => $users,
                'domains' => $domains,
                'total_hours' => $total_hours, // Pass the total_hours to the view
                // 'support_status'=>$support_status,
            ]
        );
    }

    public function supportEmails()
    {

        return view('support_emails');
    }

    // public function viewTicket($id)
    // {
    //   $ticket_data = DB::table('support_tickets')->select('*')
    //                  ->where("id",$id)
    //                 ->first();

    //   $users = DB::table('users')->select('name','id')->get();

    //   $attachments = DB::table('support_ticket_attachments')->select('*')->where('ticket_id', $id)->get();

    //   return view('view_ticket',['ticket_data'=>$ticket_data,
    //                               'users'=>$users,
    //                               'attachments'=>$attachments,
    //                              ]);
    // }

    public function viewTicket($id)
    {
        // Need to Check permission
        $ticket_data = DB::table('support_tickets')->select('*')
            ->where("id", $id)
            ->first();

        $total_hours = DB::select(DB::raw("SELECT t.user_id,
                    SEC_TO_TIME(SUM(TIME_TO_SEC(t.end_time) - TIME_TO_SEC(t.start_time))) AS total_time FROM ticket_details t WHERE t.ticket_id = $id GROUP BY t.user_id"));
        // print_r($total_hours);exit;

        $last_ticket_replies = DB::select(DB::raw("SELECT sr.*, sta.type, GROUP_CONCAT(sta.filename) AS attachments FROM `support_ticket_replies` as sr LEFT JOIN support_ticket_attachments as sta ON sr.id = sta.ticket_id AND sta.type='reply' WHERE sr.ticket_id = $id GROUP BY sr.id ORDER BY sr.id DESC Limit 1"));

        $ticket_replies = DB::select(DB::raw("SELECT sr.*, sta.type, GROUP_CONCAT(sta.filename) AS attachments FROM `support_ticket_replies` as sr LEFT JOIN support_ticket_attachments as sta ON sr.id = sta.ticket_id AND sta.type='reply' WHERE sr.ticket_id = $id GROUP BY sr.id ORDER BY sr.id DESC"));


        $users = DB::table('users')->select('name', 'id')->whereNotIn('role', [4])->where('status', 1)->orderBy('name')->get();

        $attachments = DB::table('support_ticket_attachments')->select('*')->where('ticket_id', $id)->get();
        $domains = DB::table('user_domains')->select('*')->orderBy('domain')->groupBy('domain')->get();


        return view('view_ticket', ['ticket_data' => $ticket_data, 'total_hours' => $total_hours, 'users' => $users, 'ticket_replies' => $ticket_replies, 'last_ticket_replies' => $last_ticket_replies, 'attachments' => $attachments, 'domains' => $domains]);
    }


    public function storeReplyTicket(Request $request)
    {
        $ticket_info = DB::table('support_tickets')->where('id', $request->ticket_id)->first();

        if (!isset($ticket_info->id))
            return redirect()->back();

        $maildata["email"][] = $ticket_info->email_from;
        $maildata["email"][] = "support@tekroi.com";

        $assigned_user_info = DB::table('users')->where('id', $ticket_info->assigned)->first();

        if ($assigned_user_info)
            $maildata["email"][] = $assigned_user_info->email;

        $validatedData = $request->validate([
            'summary' => 'required',
            'ticket_id' => 'required',
        ]);
        // $data = array(
        //     'summary'  => $request->summary,
        //     'ticket_id'   => $request->ticket_id,
        //     'user_id'     => Auth::id()
        //   );
        //  $reply_id = DB::table('support_ticket_replies')->insertGetId($data);
        $fileUploaded = [];
        if ($request->hasfile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $name = $file->getClientOriginalName();
                $name = $request->ticket_id . "_" . time() . '_' . $name;
                $file->move(public_path() . '/attachment/', $name);
                $fileUploaded[] = public_path('attachment/' . $name);
                // $data_attachment = array(
                //     'ticket_id'  => $reply_id,
                //     'type'   => 'reply',
                //     'filename'   => $name,
                //     'type'   => 'reply',
                //     'user_id'     => Auth::id()
                //   );
                //  DB::table('support_ticket_attachments')->insert($data_attachment);
            }
        }

        $ticket_replies = DB::select(DB::raw("SELECT sr.*, sta.type, GROUP_CONCAT(sta.filename) AS attachments FROM `support_ticket_replies` as sr LEFT JOIN support_ticket_attachments as sta ON sr.id = sta.ticket_id AND sta.type='reply' WHERE sr.ticket_id = $ticket_info->id  GROUP BY sr.id ORDER BY sr.id DESC"));

        $mail_from = " ";
        $prevEmailContent = $ticket_info->ticket_summary;
        foreach ($ticket_replies as $ticket_replie) {
            $prevEmailContent .= $ticket_replie->summary;
            $mail_from = $ticket_replie->created_at . " " . $ticket_replie->email_from . ":";
        }


        $email_chain = '<blockquote>' . $prevEmailContent . " " . $mail_from . '</blockquote>';
        $summary = $request->summary;
        // echo "<pre>";print_r($email_chain);exit;
        // echo "<pre>";print_r($summary);exit;


        // Send a Email to Client with attachments If any
        $maildata["subject"] = "RE: " . $ticket_info->subject_ticketid;
        $maildata["body"] =  $request->summary . $email_chain;

        // echo"<pre>";print_r($maildata["body"]);exit;

        Mail::send('emails.myTestMail', $maildata, function ($message) use ($maildata, $fileUploaded) {
            $message->to($maildata["email"])
                ->subject($maildata["subject"]);

            if (count($fileUploaded) > 0)
                foreach ($fileUploaded as $file) {
                    $message->attach($file);
                }
        });

        // $totalHours = $request->input('total_hours');

        // $totalHours = DB::table('support_tickets')
        //     ->where('id', $request->ticket_id)
        //     ->update(['total_hours' => $totalHours]);

        return redirect()->route('support_view')->with('success', 'Successfully replied');
    }

    public function emailJson(Request $request)
    {
        $data = $request->all();
        if (isset($data['draw'])) {

            $columnArray
                = array(
                    'id',
                    'name',
                    'subject',
                    'email',
                    'received_at',
                    'created_at',
                    'status',
                    'comments'
                );

            try {
                DB::enableQueryLog();

                /**
                 * Database query object selection
                 */
                $query = DB::table('support_email as s');
                /**
                 * field selection
                 */
                // date('Y-m-d', strtotime()

                $query->select(
                    's.id',
                    's.name',
                    's.subject',
                    's.email',
                    's.received_at',
                    (DB::raw("DATE_FORMAT(s.received_at, '%d-%m-%Y %H:%i') as received_at")),
                    's.created_at',
                    (DB::raw("DATE_FORMAT(s.created_at, '%d-%m-%Y %H:%i') as created_at")),
                    's.comments',
                    (DB::raw("(CASE
                                  WHEN s.status = '0' THEN 'Pending'
                                  WHEN s.status = '1' THEN 'Completed'
                                   END) status"))
                );


                if ($data['search_user'] != '') {

                    $query->whereRaw(

                        "s.name like '%" . $data['search_user'] . "%' || s.email like '%" . $data['search_user'] . "%' || s.subject like '%" . $data['search_user'] . "%'"

                    );
                }



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
                //echo "<pre>"; print_r($query->toSql());
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

            return response()->json($response);
        }
    }


    public function supportJson(Request $request)
    {
        $data = $request->all();
        $user_id = Auth::id();
        $logged_user_id = Auth::user()->id;
        $role = Auth::user()->role;
        if (isset($data['draw'])) {

            $columnArray
                = array(
                    'id',
                    'ticket_id',
                    'subject',
                    'email_from',
                    'domain',
                    'assigned',
                    're_assigned',
                    'status',
                    'last_updated_user',
                    'created_at'

                );

            try {

                DB::enableQueryLog();

                /**
                 * Database query object selection
                 */
                if ($role == 1) {
                    $query = DB::table('support_tickets as s')
                        ->leftJoin('users as us', 'us.id', '=', 's.assigned')
                        ->leftJoin('users as u', 'u.id', '=', 's.re_assigned')
                        ->leftJoin('users as ui', 'ui.id', '=', 's.last_updated_user');
                } elseif ($role == 2) {

                    $query = DB::table('support_tickets as s')
                        ->leftJoin('users as us', 'us.id', '=', 's.assigned')
                        ->leftJoin('users as u', 'u.id', '=', 's.re_assigned')
                        ->leftJoin('users as ui', 'ui.id', '=', 's.last_updated_user')
                        ->where(function ($query) use ($logged_user_id) {
                            $query->where('s.assigned', $logged_user_id)
                                ->oRwhere('s.re_assigned', $logged_user_id);
                        });
                } elseif ($role == 4) {

                    $role_email = Auth::user()->email;
                    list($username, $domain) = explode('@', $role_email);

                    $query = DB::table('support_tickets as s')
                        ->leftJoin('users as us', 'us.id', '=', 's.assigned')
                        ->leftJoin('users as u', 'u.id', '=', 's.re_assigned')
                        ->leftJoin('users as ui', 'ui.id', '=', 's.last_updated_user')
                        ->where('s.domain', $domain);
                } else {

                    $query = DB::table('support_tickets as s')
                        ->leftJoin('users as us', 'us.id', '=', 's.assigned')
                        ->leftJoin('users as u', 'u.id', '=', 's.re_assigned')
                        ->leftJoin('users as ui', 'ui.id', '=', 's.last_updated_user')
                        ->where(function ($query) use ($logged_user_id) {
                            $query->where('s.assigned', $logged_user_id)
                                ->oRwhere('s.re_assigned', $logged_user_id);
                        });

                    // ->oRwhere('s.re_assigned', $logged_user_id)
                    // ->oRwhereIn('s.assigned',function($data) use ($logged_user_id) {
                    //       $data->select('assigned_user')->from('user_assigned')
                    //         ->where("user",$logged_user_id);

                    //    })
                    // ->oRwhereIn('s.re_assigned',function($data) use ($logged_user_id) {
                    //       $data->select('assigned_user')->from('user_assigned')
                    //         ->where("user",$logged_user_id);

                    //    });
                }

                // Add left join with ticket_details
                $query->leftJoin('ticket_details as td', 'td.ticket_id', '=', 's.ticket_id');

                $query->select(
                    's.id',
                    's.ticket_id',
                    's.subject',
                    's.email_from',
                    's.domain',
                    'us.name as assigned',
                    'u.name as re_assigned',
                    'ui.name as last_updated_user',
                    's.created_at',
                    's.total_hours', // Include the 'total_hours' column
                    (DB::raw("DATE_FORMAT(s.created_at, '%d-%m-%Y %H:%i') as created_at")),
                    's.status as status_id',
                    (DB::raw("(CASE
                                  WHEN s.status = '0' THEN '<span style=" . 'color:white;background-color:green;padding:5px;border-radius:15px;' . ">Open</span>'
                                  WHEN s.status = '1' THEN '<span style=" . 'color:white;background-color:#5DADE2;padding:5px;border-radius:15px;' . ">Initiated</span>'
                                  WHEN s.status = '2' THEN '<span style=" . 'color:white;background-color:#A569BD;padding:5px;border-radius:15px;' . ">Work in Progress</span>'
                                  WHEN s.status = '3' THEN '<span style=" . 'color:white;background-color:#34495E;padding:5px;border-radius:15px;' . ">Waiting for Customer</span>'
                                  WHEN s.status = '4' THEN '<span style=" . 'color:white;background-color:#A04000;padding:5px;border-radius:15px;' . ">Confirmation Pending</span>'
                                  WHEN s.status = '5' THEN '<span style=" . 'color:white;background-color:red;padding:5px;border-radius:15px;' . ">Closed</span>'
                                   END) status"))
                );

                if ($data['search_support'] != '') {

                    $query->whereRaw(

                        "s.ticket_id like '%" . $data['search_support'] . "%' || s.subject like '%" . $data['search_support'] . "%' || s.email_from like '%" . $data['search_support'] . "%' || s.domain like '%" . $data['search_support'] . "%' || u.name like '%" . $data['search_support'] . "%'
                            "

                    );
                }

                // if ($data['status_filter'] != '') {

                //     $query->whereRaw(
                //         "s.status = '" . $data['status_filter'] . "'"
                //     );
                // }

                if (!empty($data['status_filter'])) {
                    $query->whereIn('s.status', $data['status_filter']);
                }

                if ($data['period_filter'] != '') {

                    switch ($data['period_filter']) {
                        case 'today':
                            $query->whereDate('s.created_at', now()->today());
                            break;
                        case 'yesterday':
                            $query->whereDate('s.created_at', now()->subDays(1));
                            break;
                        case 'week':
                            $query->whereDate('s.created_at', '>=', now()->subDays(7));
                            break;
                        case 'month':
                            $query->whereDate('s.created_at', '>=', now()->subDays(30));
                            break;
                        case 'beyondmonth':
                            $query->whereDate('s.created_at', '<', now()->subMonth(1));
                            break;
                    }
                }

                // if ($data['user_filter'] != '') {

                //     $query->whereRaw(
                //         "assigned = '" . $data['user_filter'] . "'"
                //     );
                // }

                if ($data['user_filter'] != '') {
                    $query->whereRaw("s.assigned = " . $data['user_filter'] . " OR s.re_assigned = " . $data['user_filter']);
                }
                
                

               

                if ($data['domain_filter'] != '') {

                    $query->whereRaw(
                        "domain = '" . $data['domain_filter'] . "'"
                    );
                }

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
                //echo "<pre>"; print_r($query->toSql());
                /**
                 * Get
                 */
                $feedbacks = $query->get();
                // foreach ($feedbacks as &$feedback) {
                //     $feedback->total_hours = $feedback->total_hours; // Include the 'total_hours' field
                // }
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
    public function viewEmailBody($id)
    {
        $view_body = DB::table('support_email')
            ->select('id', 'body')
            ->where("id", $id)
            ->first();
        return Response::json(array(
            'success' => 1,
            'mail_body'    => $view_body,
            'message' => 'OK'
        ));
    }


    public function updateSupportStatus(Request $request)
    {
        $data = $request->all();
        $parms = array(
            'status' => $request['update_status'],
            'updated_at' => date('Y-m-d H:i:s')
        );

        $password = DB::table('support_email')
            ->where('id', $request['status_id'])
            ->update($parms);
        if ($password) {
            return Response::json(array('success' => 1, 'message' => 'Status Updated.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Status Not Updated.'));
        }
    }
    public function updateSupportComments(Request $request)
    {
        $data = $request->all();
        $parms = array(
            'comments' => $request['update_comments'],
            'updated_at' => date('Y-m-d H:i:s')
        );

        $password = DB::table('support_email')
            ->where('id', $request['comments_id'])
            ->update($parms);
        if ($password) {
            return Response::json(array('success' => 1, 'message' => 'Comments Updated.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Comments Not Updated.'));
        }
    }

    public function addTicketDetails(Request $request)
    {
        $data = $request->all();
        $user_id = Auth::id();

        for ($i = 0; $i < count($data['date']); $i++) {
            // echo "<pre>";print_r($data['date']);exit;
            $params = array(
                'date'        => $data['date'][$i],
                'start_time'  => $data['start_time'][$i],
                'end_time'    => $data['end_time'][$i],
                'total_time'  => $data['total_time'][$i],
                'description' => $data['description'][$i],
                'ticket_id'   => $request['ticket_id'],
                //  'total_hours' => $data['total_hours'],
                'user_id'     => $user_id = Auth::id(),
                'created_at'  => date('Y-m-d H:i:s')
            );
            //echo"<pre>"; print_r($params);exit;
            $details = DB::table('ticket_details')
                ->insert($params);
        }


        if ($details) {
            return Response::json(array('success' => 1, 'message' => 'Ticket Details Added Successfully.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Ticket Details Not Added.'));
        }

        // if (Auth::check()) {

        //    echo "hello";
        //    echo "user".$user_id =  Auth::id();

        //    exit;
        // }else{
        //   echo "teststst";
        // }
        // exit;

        // $params = array(
        //                 'ticket_id'   => $data['ticket_id'],
        //                 'date'        => $request['date'],
        //                 'start_time'  => $request['start_time'],
        //                 'end_time'    => $request['end_time'],
        //                 'total_time' =>  $request['total_time'],
        //                 'user_id'     => $user_id = Auth::id(),
        //                 'created_at'  => date('Y-m-d H:i:s')
        //               );
        //  $details = DB::table('ticket_details')
        //              ->insert($params);
        // ->update($parms);
    }


    public function changeTicketDomain(Request $request)
    {

        $parms = array(
            'domain'  => $request['newdomain'],
        );

        $details = DB::table('support_tickets')
            ->where('id', $request['ticket_id'])
            ->update($parms);
        // print_r($details);exit;
        if ($details) {
            return Response::json(array('success' => 1, 'message' => 'Domain Updated.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Domain Not Updated.'));
        }
    }

    public function updateDetails(Request $request)
    {
        $data = $request->all();

        $parms = array(
            're_assigned'  => $request['re_assigned'],
            'issue_summary' => $request['issue_summary'],
            // 'total_hours'   => $request['total_hours'], // Add this line to update total_hours
            'updated_at'   => date('Y-m-d H:i:s')
        );

        $details = DB::table('support_tickets')
            ->where('id', $request['ticket_id'])
            ->update($parms);
        // print_r($details);exit;
        if ($details) {
            return Response::json(array('success' => 1, 'message' => 'detials Updated.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'details Not Updated.'));
        }
    }

    public function addTicket(Request $request)
    {
        $email = Auth::user()->email;

        $apiEmailController = new ApiEmailController();
        $ticketId = $apiEmailController->generateTicketId();

        list($name, $domain) = explode('@', $email);
        $assign_user = $apiEmailController->findUser($domain);

        $assigned_user_id = 0;
        if (isset($assign_user[0]->id)) {
            $assigned_user_id = $assign_user[0]->id;
        }

        $params = array(
            'subject'        => $request['subject'],
            'ticket_summary' => $request['ticket_summary'],
            'ticket_id' => $ticketId,
            'subject_ticketid' => "[Ticket # $ticketId] : " . $request['subject'],
            'email_from' => $email,
            'email_name' => $name,
            'domain' => $domain,
            'assigned' => $assigned_user_id,
            'created_at'     => date('Y-m-d H:i:s')
        );

        $details = DB::table('support_tickets')->insert($params);
        if ($details) {
            return Response::json(array('success' => 1, 'message' => 'Ticket is Added Successfully.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Ticket Adding is Failed, Try again.'));
        }
    }

    public function viewTicketJson(Request $request)
    {
        $data = $request->all();
        // $lastWord = substr($url, strrpos($url, '/') + 1);

        // echo "<pre>"; print_r($data); exit;

        if (isset($data['draw'])) {

            $columnArray
                = array(
                    'id',
                    'user_id',
                    'date',
                    'start_time',
                    'end_time',
                    'total_time',
                    'description'

                );

            try {
                DB::enableQueryLog();

                /**
                 * Database query object selection
                 */
                $query = DB::table('ticket_details as t')
                    ->leftJoin('users as u', 'u.id', '=', 't.user_id');

                /**
                 * field selection
                 */
                // date('Y-m-d', strtotime()
                $query->select(
                    't.id',
                    'u.name as user_id',
                    't.date',
                    't.start_time',
                    't.end_time',
                    't.total_time',
                    't.description'

                );

                if ($data['tecket_id'] != '') {

                    $query->whereRaw(
                        "ticket_id = " . $data['tecket_id'] . ""
                    );
                }

                if ($data['search_user'] != '') {

                    $query->whereRaw(

                        "t.date like '%" . $data['search_user'] . "%'"

                    );
                }


                if (isset($data['user_count'])) {

                    $count = $data['user_count'];
                } else {
                    $count = '10';
                }


                $userCount = count($query->get());
                /**
                 * Order by
                 */
                // echo "<pre>"; print_r($query->toSql()); exit();

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
                // Additional code to calculate and store total_hours
                $users = $query->get();

                $cumulativeTotalSeconds = 0;

                foreach ($users as $user) {
                    $startDateTime = new \DateTime($user->date . ' ' . $user->start_time);
                    $endDateTime = new \DateTime($user->date . ' ' . $user->end_time);

                    // Calculate the total seconds
                    $totalSeconds = $startDateTime->diff($endDateTime)->s
                        + $startDateTime->diff($endDateTime)->i * 60
                        + $startDateTime->diff($endDateTime)->h * 3600;

                    // Update the total_hours in the response
                    $user->total_hours = gmdate('H:i', $totalSeconds);

                    //dd($user);
                    // Update the total_hours in the response or set it to 0 if not existing
                    $user->total_hours = isset($user->total_hours) ? $user->total_hours : '00:00';
                    // Accumulate total_seconds
                    $cumulativeTotalSeconds += $totalSeconds;
                    // dd( $cumulativeTotalSeconds);
                }

                // Convert accumulated total seconds back to HH:MM format
                $cumulativeTotalHoursFormatted = gmdate('H:i', $cumulativeTotalSeconds);
                //dd($cumulativeTotalHoursFormatted);
          //-------------- Update total_hours in the support_tickets table based on the id--------------------------//
                DB::table('support_tickets')->where('id', $data['tecket_id'])
                    ->update(['total_hours' => $cumulativeTotalHoursFormatted]);

                $response['total_time_spent'] = $cumulativeTotalHoursFormatted;
            } catch (\Exception $e) {

                $users = [];
                $userCount = 0;
            }

            $response['draw'] = $data['draw'];
            $response['recordsTotal'] = $userCount;
            $response['recordsFiltered'] = $userCount;

            $response['data'] = $users;
            //$response['total_hours'] = isset($users[0]->total_hours) ? date('H:i', strtotime($users[0]->total_hours)) : '00:00';


            return response()->json($response);
        }
    }

    public function updateStatus(Request $request)
    {
        $data = $request->all();
        $user_id = Auth::user()->id;

        $parms = array(
            'status' => $request['update_status'],
            'comment' => $request['comment'],
            'last_updated_user' => $user_id,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $status_update = DB::table('support_tickets')
            ->where('id', $request['status_id'])
            ->update($parms);

        $params = array(
            'status' => $request['update_status'],
            'comment' => $request['comment'],
            'last_updated_user' => $user_id,
            'old_status' => $request['old_status'],
            'ticket_id' => $request['ticket_id'],
            'created_at' => date('Y-m-d H:i:s')
        );

        $status_update = DB::table('status_loop')
            ->insert($params);
        // print_r($status_update);exit;


        if ($status_update) {
            return Response::json(array('success' => 1, 'message' => 'Status Updated.'));
        } else {
            return Response::json(array('success' => 0, 'message' => 'Status Not Updated.'));
        }
    }

    public function getStatus($ticket_id)
    {
        $status_data = DB::table('status_loop as sl')
            ->select(
                'sl.*',
                (DB::raw("(CASE
                                  WHEN sl.status = '0' THEN '<span>Open</span>'
                                  WHEN sl.status = '1' THEN '<span>Initiated</span>'
                                  WHEN sl.status = '2' THEN '<span>Work in Progress</span>'
                                  WHEN sl.status = '3' THEN '<span>Waiting for Customer</span>'
                                  WHEN sl.status = '4' THEN '<span>Confirmation Pending</span>'
                                  WHEN sl.status = '5' THEN '<span>Closed</span>'
                                   END) status_display")),
                (DB::raw("(CASE
                                  WHEN sl.old_status = '0' THEN '<span>Open</span>'
                                  WHEN sl.old_status = '1' THEN '<span>Initiated</span>'
                                  WHEN sl.old_status = '2' THEN '<span>Work in Progress</span>'
                                  WHEN sl.old_status = '3' THEN '<span>Waiting for Customer</span>'
                                  WHEN sl.old_status = '4' THEN '<span>Confirmation Pending</span>'
                                  WHEN sl.old_status = '5' THEN '<span>Closed</span>'
                                   END) old_status_display"))
            )
            ->leftJoin('support_tickets as t', 't.id', '=', 'sl.ticket_id')
            ->where("sl.ticket_id", $ticket_id)
            ->get();
        return Response::json(array(
            'success' => 1,
            'status_data'    => $status_data,
            'message' => 'OK'
        ));
    }
}
