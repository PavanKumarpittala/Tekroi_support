<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use \Crypt;
use Auth;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    
    public function user()
    {
        return view('users_list');
    }

    public function userDomain()
    {
      $users = DB::table('users')->select('name','id')->orderBy('name')->get();
      return view('user_domain',['users'=>$users]);
    }
    
    public function addEmployees(Request $request)
  {
    $data = $request->all();
     $params = array(
                     'employee_id'   => $request['employee_id'],
                     'name'          => $request['employee_name'],
                     'email'         => $request['employee_email'],
                     'password'      => Hash::make($request['employee_password']),
                     'mobile'        => $request['employee_mobile'],
                     'role'           => $request['employee_role'],
                     'support_domain' => $request['support_domain'],
                     'created_at'     => date('Y-m-d H:i:s')
                   );
      $users_list = DB::table('users')
                  ->insert($params);
                 
      if($users_list)
      {
        return Response::json(array('success'=>1,'message'=>'Employee Details Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Employee Details Not Added.'));

      }   


  }   

  public function employeejson(Request $request)
    {
       $data = $request->all();
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'employee_id',
                'name',
                'email',
                'mobile',
                'role'                
                
            );

            try {    
                DB::enableQueryLog();

                    /**
                     * Database query object selection
                     */
                    $query = DB::table('users as u');                        

                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()
                    $query->select(
                        'u.id',
                        'u.employee_id',
                        'u.name',
                        'u.email',
                        'u.mobile',
                        'u.role',
                         (DB::raw("(CASE  
                                WHEN u.role = '1' THEN 'Admin'
                                WHEN u.role = '2' THEN 'Manager'
                                WHEN u.role = '3' THEN 'Employee'
                                WHEN u.role = '4' THEN 'Client'
                                 END) role_display"))
                                
                    );

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "u.name like '%". $data['search_user'] ."%' || u.email like '%".$data['search_user']."%' || u.mobile like '%".$data['search_user']."%'"  
                            
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

    public function getUsers($user_id)
    {
      $user = DB::table('users')

                    ->select('*')
                    ->where("id",$user_id)
                    ->first();
          return Response::json(array('success'=>1,
            'user'    => $user,
            'message'=>'OK'
          ));

    }

    public function updateUser(Request $request)
  {
      $request = $request->all();

      $parms = array(
                     'name'    => $request['update_name'],
                     'email'   => $request['update_email'],
                     'mobile'  => $request['update_mobile'],
                     'updated_at' => date('Y-m-d H:i:s')
                    );
      if($request['update_password'])
      {
        $parms['password'] = Hash::make($request['update_password']);
      }
       // print_r($parms);exit;
      $password = DB::table('users')
                  ->where('id', $request['user_hidden'])
                  ->update($parms);
      if($password)
      {
        return Response::json(array('success'=>1,'message'=>'Your Details Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Your Details Not Updated.'));

      }

  }

  public function addDomain(Request $request)
  {
    $data = $request->all();
     $params = array(
                     'user_id'   => $request['user_id'],
                     'domain'          => $request['domain'],
                     'created_at'     => date('Y-m-d H:i:s')
                   );
      $user_domain = DB::table('user_domains')
                  ->insert($params);
                 
      if($user_domain)
      {
        return Response::json(array('success'=>1,'message'=>'User Domain Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'User Domain Not Added.'));

      }

   } 

   public function domainjson(Request $request)
    {
       $data = $request->all();
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'user_id',
                'domain',
                'created_at',
                'status'
            );

            try {    
                DB::enableQueryLog();

                    /**
                     * Database query object selection
                     */
                    $query = DB::table('user_domains as u')
                           ->leftJoin('users as us','us.id','=','u.user_id');

                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()
                    $query->select(
                        'u.id',
                        'us.name as user_id',
                        'u.domain',
                        'u.created_at',
                        'u.status',
                        (DB::raw("(CASE  
                                WHEN u.status = '0' THEN 'In Active'
                                WHEN u.status = '1' THEN 'Active'
                                 END) status_display"))
                                
                    );

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "us.name like '%". $data['search_user'] ."%' || u.domain like '%".$data['search_user']."%'"
                            
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
    public function getDomain($user_id)
    {
      $user = DB::table('user_domains')

                    ->select('*')
                    ->where("id",$user_id)
                    ->first();
          return Response::json(array('success'=>1,
            'user'    => $user,
            'message'=>'OK'
          ));

    }
    public function updateUserDomain(Request $request)
  {
      $request = $request->all();

      $parms = array(
                     'user_id'    => $request['user_id_update'],
                     'domain'   => $request['domain_update'],
                     'updated_at' => date('Y-m-d H:i:s')
                    );
      
      $domains = DB::table('user_domains')
                  ->where('id', $request['user_hidden'])
                  ->update($parms);
      if($domains)
      {
        return Response::json(array('success'=>1,'message'=>'User Domain Details Updated.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'User Domain Details Not Updated.'));

      }

  }
  public function deleteDomain($id,$status)
  {
     if($status == 0)
     {
      $update_status = 1;
      $message = "Domain Activated."; 
     }else{
      $update_status = 0;
      $message = "Domain DeActivated.";
     }


    $delete_domain = array('status'   =>$update_status,
                        'updated_at' => date('Y-m-d H:i:s')
                        );
   
      $domain = DB::table('user_domains')
                  ->where('id', $id)
                  ->update($delete_domain);

      if($domain)
      {
        return Response::json(array('success'=>1,'message'=>$message));

      }else{
        return Response::json(array('success'=>0,'message'=>$message));
      }   
  }


   public function showChangePasswordGet() {
        return view('auth.passwords.change-password');
    }

    public function changePasswordPost(Request $request) {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            // Current password and new password same
            return redirect()->back()->with("error","New Password cannot be same as your current password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:8|confirmed',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();

        return redirect()->back()->with("success","Password successfully changed!");
    }

  
}
