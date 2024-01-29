<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use Mail;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    
    public function supportView()
    {
       $support_data = DB::table('support_tickets')->select('*')->get();
       
      // print_r($support_data);exit;
       $support_emails = DB::table('support_tickets as st')
                    ->select('st.*','e.subject')
                    ->leftJoin('support_email as e', 'st.id', '=', 'e.id')
                    ->get();
           // echo "<pre>"; print_r($support_emails);exit;
        return view('support_view',
                                  ['support_data'=>$support_data,
                                   'support_emails'=>$support_emails,
                                   ]);
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
                     ->where("id",$id)
                     ->first();

         $last_ticket_replies = DB::select(DB::raw("SELECT sr.*, sta.type, GROUP_CONCAT(sta.filename) AS attachments FROM `support_ticket_replies` as sr LEFT JOIN support_ticket_attachments as sta ON sr.id = sta.ticket_id AND sta.type='reply' WHERE sr.ticket_id = $id GROUP BY sr.id ORDER BY sr.id DESC Limit 1"));

         $ticket_replies = DB::select(DB::raw("SELECT sr.*, sta.type, GROUP_CONCAT(sta.filename) AS attachments FROM `support_ticket_replies` as sr LEFT JOIN support_ticket_attachments as sta ON sr.id = sta.ticket_id AND sta.type='reply' WHERE sr.ticket_id = $id GROUP BY sr.id ORDER BY sr.id DESC"));

        $users = DB::table('users')->select('name','id')->get();

        $attachments = DB::table('support_ticket_attachments')->select('*')->where('ticket_id', $id)->get();

        return view('view_ticket',['ticket_data'=>$ticket_data,'users'=>$users, 'ticket_replies' =>$ticket_replies, 'last_ticket_replies'=>$last_ticket_replies,'attachments'=>$attachments]);
    }



   public function storeReplyTicket(Request $request)
    {
        $ticket_info = DB::table('support_tickets')->where('id', $request->ticket_id)->first();
        if (!isset($ticket_info->id))
            return redirect()->back();

        $maildata["email"][] = $ticket_info->email_from;
        $maildata["email"][] = "supportdev@tekroi.com";

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
                $name = $request->ticket_id. "_" .time().'_'.$name;
                $file->move(public_path().'/attachment/', $name);
                $fileUploaded[] = public_path('attachment/'.$name);
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

         // Send a Email to Client with attachments If any
         $maildata["subject"] = "RE: ". $ticket_info->subject_ticketid;
         $maildata["body"] = $request->summary;

        Mail::send('emails.myTestMail', $maildata, function($message)use($maildata, $fileUploaded) {
            $message->to($maildata["email"])
                    ->subject($maildata["subject"]);

            if(count($fileUploaded) > 0)
                foreach ($fileUploaded as $file){
                    $message->attach($file);
                }

        });

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
                        's.created_at',
                        's.comments',
                        (DB::raw("(CASE  
                                  WHEN s.status = '0' THEN 'Pending'
                                  WHEN s.status = '1' THEN 'Completed'
                                   END) status"))
                    );
                     

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "s.name like '%". $data['search_user'] ."%' || s.email like '%". $data['search_user'] ."%' || s.subject like '%". $data['search_user'] ."%'"  
                            
                         );
                    }



                    if(isset($data['feedback_count'])){

                        $count = $data['feedback_count'];
                    }else{
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
                } catch(\Exception $e) {
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
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'ticket_id',
                'subject',
                'email_from',
                'domain',
                'assigned',
                'status',
                // 'total_hours',
                'created_at'

            );

            try {
                DB::enableQueryLog();

                    /**
                     * Database query object selection
                     */

                    $query = DB::table('support_tickets as s');
                    

                    // Join Table based on Whether User is Administrator or Others.
                    if (Auth::user()->role == 1) {
                        $query->join('users as u','u.id','=','s.assigned');
                    } else {
                        $logged_user_id = Auth::user()->id;
                        $query->join('users as u','u.id','=','s.assigned')
                                ->where(function($subquery) use ($logged_user_id) {
                                    $subquery->where('s.assigned', $logged_user_id)
                                        ->oRwhere('s.re_assigned', $logged_user_id);
                                });
                    }

                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()

                    $query->select(
                        's.id',
                        's.ticket_id',
                        's.subject',
                        's.email_from',
                        's.domain',
                        'u.name as assigned',
                        's.status',
                        // 's.total_hours',
                        's.created_at',                        

                        (DB::raw("(CASE
                                  WHEN s.status = '0' THEN '<span style=".'color:white;background-color:green;padding:5px;border-radius:15px;'.">Open</span>'
                                  WHEN s.status = '1' THEN '<span style=".'color:white;background-color:red;padding:5px;border-radius:15px;'.">Closed</span>'
                                   END) status"))
                    );


                    if ($data['search_support']!=''){

                         $query->whereRaw(

                            "s.ticket_id like '%". $data['search_support'] ."%' || s.subject like '%". $data['search_support'] ."%' || s.email_from like '%". $data['search_support'] ."%' || s.domain like '%". $data['search_support'] ."%' || u.name like '%". $data['search_support'] ."%'
                            "

                         );
                    }



                    if(isset($data['search_count'])){

                        $count = $data['search_count'];
                    }else{
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
                } catch(\Exception $e) {
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
    public function viewEmailBody($id)
    {
      $view_body = DB::table('support_email')
                    ->select('id','body')
                    ->where("id",$id)
                    ->first();
          return Response::json(array('success'=>1,
            'mail_body'    => $view_body,
            'message'=>'OK'
          ));

    }
    

     public function updateSupportStatus(Request $request)
  {
    $data = $request->all();
    $parms = array('status'=>$request['update_status'],
                   'updated_at' => date('Y-m-d H:i:s')
                    );

      $password = DB::table('support_email')
                  ->where('id', $request['status_id'])
                  ->update($parms);
      if($password)
      {
        return Response::json(array('success'=>1,'message'=>'Status Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Status Not Updated.'));
      }   
  }
  public function updateSupportComments(Request $request)
  {
    $data = $request->all();
    $parms = array('comments'=>$request['update_comments'],
                   'updated_at' => date('Y-m-d H:i:s')
                    );

      $password = DB::table('support_email')
                  ->where('id', $request['comments_id'])
                  ->update($parms);
      if($password)
      {
        return Response::json(array('success'=>1,'message'=>'Comments Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Comments Not Updated.'));

      }   


  }

  public function addTicketDetails(Request $request)
  {
    $data = $request->all();
      $user_id = Auth::id();
       
     for ($i=0; $i < count($data['date']); $i++) { 
         // echo "<pre>";print_r($data['date']);exit;
      $params = array(
                     'date'        => $data['date'][$i],                      
                     'start_time'  => $data['start_time'][$i],
                     'end_time'    => $data['end_time'][$i],
                     'total_time'  => $data['total_time'][$i],
                     'description' => $data['description'][$i],
                     'ticket_id'   => $request['ticket_id'],                      
                     'user_id'     => $user_id = Auth::id(),
                     'created_at'  => date('Y-m-d H:i:s')
                     
                   );
          //echo"<pre>"; print_r($params);exit;
         $details = DB::table('ticket_details')
                      ->insert($params);
          }
       

       if($details)
      {
        return Response::json(array('success'=>1,'message'=>'Ticket Details Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Ticket Details Not Added.'));

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
  
  public function updateDetails(Request $request)
  {
    $data = $request->all();

    $parms = array(
                   're_assigned'  => $request['re_assigned'],
                   'issue_summary'=> $request['issue_summary'],
                   'updated_at'   => date('Y-m-d H:i:s')
                    );

       $details = DB::table('support_tickets')
                  ->where('id', $request['ticket_id'])
                  ->update($parms);
        // print_r($details);exit;
      if($details)
      {
        return Response::json(array('success'=>1,'message'=>'detials Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'details Not Updated.'));

      }   


  }

  public function addTicket(Request $request)
  {
    $data = $request->all();
    $params = array(
                     'ticket_id'      => $request['ticket_id'],
                     'ticket_summary' => $request['ticket_summary'],                      
                     'created_at'     => date('Y-m-d H:i:s')
                   );
      $details = DB::table('support_tickets')
                  ->insert($params);
     if($details)
      {
        return Response::json(array('success'=>1,'message'=>'Ticket Detials Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Ticket Details Not Updated.'));

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
                            ->leftJoin('users as u','u.id','=','t.user_id');
                    
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

                 if ($data['tecket_id']!=''){

                    $query->whereRaw(
                            "ticket_id = ".$data['tecket_id'].""
                         );
                  }

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "t.date like '%". $data['search_user'] ."%'"  
                            
                         );
                    }


                    if(isset($data['user_count'])){

                        $count = $data['user_count'];
                    }else{
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
                    //echo "<pre>"; print_r($query->toSql());
                    /**
                     * Get
                     */
                    $users = $query->get();
                } catch(\Exception $e) {
                    $users = [];
                    $userCount = 0;
                }    

                $response['draw'] = $data['draw'];
                $response['recordsTotal'] = $userCount;
                $response['recordsFiltered'] = $userCount;

                $response['data'] = $users;

                return response()->json($response);
            }


    }

    public function updateStatus(Request $request)
  {
    $data = $request->all();
    
    $parms = array('status'=>$request['update_status'],
                   'updated_at' => date('Y-m-d H:i:s')
                    );

      $password = DB::table('support_tickets')
                  ->where('id', $request['status_id'])
                  ->update($parms);
      if($password)
      {
        return Response::json(array('success'=>1,'message'=>'Status Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Status Not Updated.'));
      }   
  }


}

?>