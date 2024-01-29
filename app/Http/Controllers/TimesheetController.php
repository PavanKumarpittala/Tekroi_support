<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

//use DB;
use Response;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    } 
       
    public function timesheets()
    {
      // $projects = DB::table('projects')->select('project_name','id')->groupBy("project_name")->groupBy("id")->get();
    $user_id = Auth::id();
        $projects = DB::select(DB::raw("SELECT * FROM `projects` WHERE status = 1 and concat(',',employees,',') like '%,". $user_id.",%' ORDER BY project_name ASC"));
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
                     'project_name'=> $request['project_name'][$i],
                     'status'      => $request['status'][$i],
                     'who_assigned'=> $request['who_assigned'][$i],                 
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
      $users = DB::table('users')->select('name', 'id')->whereNotIn('role', [4])->where('status', 1)->orderBy('name')->get();
      return view('timesheets_list',['users' => $users]);
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
                'description',
                'status'.
                'who_assigned'
                
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
                    }else {

                     $user_assigned = DB::table('user_assigned')
                                            ->select('assigned_user')
                                            ->where('user',$user_id)
                                            ->get();
                    if(sizeof($user_assigned) > 0)
                    {
                     $user_assigned = $user_assigned[0]->assigned_user;
                     
                     
                     $parts = explode(',', $user_assigned);
                     
                     array_push($parts,$user_id);
                     $user_assigned = implode(',', $parts);
                     
                    }else{
                      $user_assigned = $user_id;
                    }
                    
                        $query = DB::table('timesheets as t')
                         ->leftJoin('users as u','u.id','=','t.user_id')
                         ->leftJoin('projects as p','p.id','=','t.project_name')
                         
                         ->whereRaw("CONCAT(',',('".$user_assigned."'),',') LIKE CONCAT('%,',t.user_id,',%')");

                        // ->Orwhere('t.user_id',$user_id);

                     //print_r($query);exit;
                    }
                    $query->select(
                        't.id',
                        'u.name as user_id',
                        'p.project_name as project_name',
                        't.date',
                        (DB::raw("DATE_FORMAT(t.date, '%d-%m-%Y') as date")),
                        't.start_time',
                        't.end_time',
                        't.total_time',
                        't.description',
                        't.status',
                        't.who_assigned'
                                
                    );
                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "p.project_name like '%". $data['search_user'] ."%'"  
                            
                         );
                    }

                    // if ($data['user_select']!=''){

                    //      $query->whereRaw(

                    //         "u.name like '%". $data['user_select'] ."%'"  
                            
                    //      );
                    // }
//----This start-----
                    if ($data['user_select'] != '') {
                      $query->where(function ($subquery) use ($data) {
                          $subquery->whereRaw("u.name like '%" . $data['user_select'] . "%'")
                                   ->orWhereRaw("t.user_id = '" . $data['user_select'] . "'");
                      });
                  }
                  //-------end of function

                    // if ($data['select_date']!=''){

                    //      $query->whereRaw(

                    //         "t.date like '%". $data['select_date'] ."%'"  
                            
                    //      );
                    // }

                    if (isset($data['min']) && isset($data['max'])) {
                      $query->whereBetween('t.date', [$data['min'], $data['max']]);
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
                    // echo "<pre>"; print_r($query->toSql());
                    /**
                     * Get
                     */
                    $users = $query->get();
                } catch(\Exception $e) {
                  echo "Gopinadh";
                 echo "<pre>"; print_r($e); 
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

    public function getDescription($id)
    {
      $description_data = DB::table('timesheets as t')
                    ->select('t.*')                      
                    ->leftJoin('users as u', 'u.name', '=', 't.id')
                    ->where("t.id",$id)
                    ->first();
          return Response::json(array('success'=>1,
            'description_data'    => $description_data,
            'message'=>'OK'
          ));

    }
   

}