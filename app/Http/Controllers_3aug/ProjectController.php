<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;

class ProjectController extends Controller
{
    
    public function project()
    {
        return view('projects');
    }


    public function addProject(Request $request)
  {
    $data = $request->all();
    for ($i=0; $i < count($data['name']); $i++) { 
     $params = array(
                     'project_name'        => $request['project_name'],
                     'customer'            => $request['customer'],
                     'customer_email'      => $request['customer_email'],
                     'customer_mobile'     => $request['customer_mobile'], 
                     'customer_designation'=> $request['customer_designation'],
                     'contact_name'        => $data['name'][$i],
                     'contact_email'       => $data['email'][$i],
                     'contact_mobile'      => $data['mobile'][$i],
                     'contact_designation' => $data['designation'][$i],
                     'division'            => $request['division'],
                     'sub_division'        => $request['sub_division'],
                     'start_date'          => $request['start_date'],
                     'created_at'          => date('Y-m-d H:i:s')
                   );
       // echo"<pre>";print_r($params);exit;
      $project_list = DB::table('projects')
                  ->insert($params);
     }
      if($project_list)
      {
        return Response::json(array('success'=>1,'message'=>'Project Details Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Project Details Not Added.'));

      }   


  }   

  public function projectjson(Request $request)
    {
       $data = $request->all();
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'project_name',
                'customer',
                'start_date',
                'status'
                
            );

            try {    
                DB::enableQueryLog();

                    /**
                     * Database query object selection
                     */
                    $query = DB::table('projects as p');                        

                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()
                    $query->select(
                        'p.id',
                        'p.project_name',
                        'p.customer',
                        'p.start_date',
                        'p.status',
                        (DB::raw("(CASE  
                                  WHEN p.status = '0' THEN 'Close'
                                  WHEN p.status = '1' THEN 'Open'
                                   END) status"))
                    );

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "p.project_name like '%". $data['search_user'] ."%' || p.customer like '%". $data['search_user'] ."%'"  
                            
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
   
  

}
