<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
       
    public function timesheets()
    {
      $projects = DB::table('projects')->select('project_name','id')->groupBy("project_name")->groupBy("id")->get();
      return view('timesheets',['projects'=>$projects]);

    }

    public function addTimesheetDetails(Request $request)
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
                     'project_name'=> $request['project_name'],                   
                     'user_id'     => $user_id = Auth::id(),
                     'created_at'  => date('Y-m-d H:i:s')
                     
                   );
          // echo"<pre>"; print_r($params);exit;
        $details = DB::table('timesheets')
                  ->insert($params);
          }
     

       if($details)
      {
        return Response::json(array('success'=>1,'message'=>'Your Timesheet Details Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Your Timesheet Details Not Added.'));

      }   
     

    }

    public function timesheetsList()
    {
      return view('timesheets_list');
    }

    public function timesheetsJson(Request $request)
    {
       $data = $request->all();
       $user_id = Auth::id();

       $role = Auth::user()->role;
       // print_r($role);exit;
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'user_id',
                'project_name',
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
                    if($role==1){
                    $query = DB::table('timesheets as t')
                            ->leftJoin('users as u','u.id','=','t.user_id')
                            ->leftJoin('projects as p','p.id','=','t.project_name');
                    }
                    else {
                        $query = DB::table('timesheets as t')
                        ->leftJoin('users as u','u.id','=','t.user_id')
                         ->leftJoin('projects as p','p.id','=','t.project_name')
                         ->whereIn('t.user_id',function($data) use ($user_id) {
                            $data->select('assigned_user')->from('user_assigned')
                              ->where("user",$user_id);

                         })
                        ->Orwhere('t.user_id',$user_id);
                        // ->get();
                        // print_r($query);
                    }


                    // $assigned_users = DB::table('user_assigned')->select('assigned_user')->where("user",$user_id)->get();
                    //  $assign_data = explode(',', $assigned_users);
                    //    print_r($assign_data);exit;

                    // $query = DB::table('timesheets as t')
                    //         // ->whereIN('user_id',$assign_data)                           
                    //          ->whereRaw("user_id IN(9,14)")
                    //           // ->where('user_id',$user_id)                            
                    //         ->leftJoin('users as u','u.id','=','t.user_id')
                    //         ->leftJoin('projects as p','p.id','=','t.project_name');
                    
                            
                    // }


                    // else{
                    // $query = DB::table('timesheets as t')
                    //           ->where('user_id',$user_id) 
                    //           ->leftJoin('users as u','u.id','=','t.user_id')
                    //           ->leftJoin('projects as p','p.id','=','t.project_name');                       
                    //  }
                        // echo "<pre>"; print_r($query->toSql()); exit();
                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()
                    $query->select(
                        't.id',
                        'u.name as user_id',
                        'p.project_name as project_name',
                        // 't.user_id',
                        // 't.project_name',
                        't.date',
                        't.start_time',
                        't.end_time',
                        't.total_time',
                        't.description'
                                
                    );

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "u.name like '%". $data['search_user'] ."%' || p.project_name like '%". $data['search_user'] ."%'"  
                            
                         );
                    }


                    if(isset($data['branch_count'])){

                        $count = $data['branch_count'];
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
   

}