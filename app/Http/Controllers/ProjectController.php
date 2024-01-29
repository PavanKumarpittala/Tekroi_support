<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use Hash;
use Auth;
use Mail;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    } 
    
    public function project()
    {
      $users = DB::table('users')->select('name','id')
                                 ->orderBy('name')
                                 ->whereNotIn('role', [1,4])
                                 ->where('status',1)
                                 ->get();
        return view('projects',['users'=>$users]);
    }
    public function viewCustomerDetails($id)
    { 
      \DB::statement("SET SQL_MODE=''");
        $view_customer_details = DB::select(DB::raw("SELECT p.*, GROUP_CONCAT(u.name) as employee_list FROM projects p, users u WHERE FIND_IN_SET(u.id, p.employees) and p.id=".$id." GROUP BY p.employees"));

      //echo "<pre>"; print_r($view_customer_details); exit;

        $view_primary_contact = DB::table('customer_contacts as c')
                       ->select('c.*')
                       ->leftJoin('projects as p', 'c.project_id', '=', 'p.id')
                       ->where('c.project_id',$id)
                       ->where('c.primary_contact',1)
                       ->first();

        $view_sub_contacts = DB::table('customer_contacts as c')
                       ->select('c.*')
                       ->leftJoin('projects as p', 'c.project_id', '=', 'p.id')
                       ->where('c.project_id',$id)
                       ->where('c.primary_contact',0)
                       ->first();
        return view('view_customer',['view_customer_details'=>$view_customer_details,'view_primary_contact'=>$view_primary_contact,'view_sub_contacts'=>$view_sub_contacts]);
    }
    public function companyDetails()
    {
        $role_email = Auth::user()->email;

        $customer_list = DB::table('projects as p')
                       ->select('p.*')
                       ->where('p.customer_email',$role_email)
                       ->first();


        $primary_contact = DB::table('customer_contacts as c')
                       ->select('c.*')
                       ->leftJoin('projects as p', 'c.project_id', '=', 'p.id')
                       // ->where('c.project_id', )
                       ->where('c.primary_contact',1)
                       ->where('c.project_id',$customer_list->id)
                       ->first();
       // echo"<pre>";print_r($primary_contact);exit;
        $sub_contacts = DB::table('customer_contacts as c')
                       ->select('c.*')
                       ->where('c.primary_contact',0)
                       ->where('c.project_id',$customer_list->id)
                       ->first();
          // echo"<pre>";print_r($sub_contacts);exit;

        return view('company_details',['customer_list'=>$customer_list,'primary_contact'=>$primary_contact,'sub_contacts'=>$sub_contacts]);
    }


    public function addProject(Request $request)
  {
    $data = $request->all();
     
     $params = array(
                     'project_name'        => $request['project_name'],
                     'customer_name'       => $request['customer_name'],
                     'customer_email'      => $request['customer_email'],
                     'customer_mobile'     => $request['customer_mobile'], 
                     'customer_designation'=> $request['customer_designation'],
                     'division'            => $request['division'],
                     'sub_division'        => $request['sub_division'],
                     'start_date'          => $request['start_date'],
                     'pan_number'          => $request['pan_number'],
                     'gst_number'          => $request['gst_number'],
                     'employees'           => $request['hidden_id'],
                     'created_at'          => date('Y-m-d H:i:s')
                   );
       
      $project_created_id = DB::table('projects')
                  ->insertGetId($params);
       
    for ($i=0; $i < count($data['name']); $i++) {

       // $id = \DB::getPdo()->lastInsertId();
       if($i == 0)
       {
         $is_primary = 1; 
       }else{
         $is_primary = 0; 
       }
        $params1 = array(
                     'project_id'          => $project_created_id,       
                     'contact_name'        => $data['name'][$i],
                     'contact_email'       => $data['email'][$i],
                     'contact_mobile'      => $data['mobile'][$i],
                     'contact_designation' => $data['designation'][$i],
                     'primary_contact'     => $is_primary,               
                     'created_at'          => date('Y-m-d H:i:s')
                   );
           // echo "<pre>";print_r($params1);exit;
        $list = DB::table('customer_contacts')
                  ->insert($params1);
                
     }
     //send mail to customers
     if($project_created_id)
     {
         $user_array = array(
                         'name'          => $request['customer_name'],
                         'email'         => $request['customer_email'],
                         'password'      => Hash::make('Tekroi@2022'),
                         'mobile'        => $request['customer_mobile'],
                         'role'           => 4,
                         'created_at'     => date('Y-m-d H:i:s')
                       );
          $users_list = DB::table('users')
                      ->insert($user_array);
        // $datayt["email"] = $request['customer_email'];
        // $datayt["title"] = "Welcome To Tekroi Support Portal..!";
        // $datayt["body_data"] = $user_array;
        // Mail::send('mailto', $datayt, function($message)use($datayt){
        //      $message->to($datayt["email"], $datayt["email"])
        //              ->subject($datayt["title"]);

        //    });

        //dd('Mail sent successfully');  
        return Response::json(array('success'=>1,'message'=>'Project Details Added Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Project Details Not Added.'));

      }   


  }   

  public function projectjson(Request $request)
    {
       $data = $request->all();
       $role = Auth::user()->role;
        if (isset($data['draw'])) {

        $columnArray
            = array(
                'id',
                'project_name',
                'customer_name',
                'customer_email',
                'customer_mobile',
                'customer_designation',
                'division',
                'pan_number',
                'gst_number',
                'start_date',
                'employees',
                'status'
                
            );

            try {    
                DB::enableQueryLog();

                    /**
                     * Database query object selection
                     */
                    

                    $query = DB::table('projects as p')
                            ->where('status',1);
                                                  
                    
                    // elseif($role==4) {

                    // $role_email = Auth::user()->email;

                    //  $query =  DB::table('projects as p')
                    //         ->leftJoin('customer_contacts as c', 'c.project_id', '=', 'p.id')
                    //           ->where('p.customer_email',$role_email);
                        
                    // }
                    // else{
                    //      $query = DB::table('projects as p')
                    //         ->leftJoin('customer_contacts as c', 'c.project_id', '=', 'p.id'); 
                    // }
                    /**
                     * field selection
                     */
                   // date('Y-m-d', strtotime()
                    $query->select(
                        'p.id',
                        'p.project_name',
                        'p.customer_name',
                        'p.customer_email',
                        'p.customer_mobile',
                        'p.customer_designation',
                        'p.division',
                        'p.pan_number',
                        'p.gst_number',
                        'p.start_date',
                       // 'p.employees',
                        'p.status',
                        (DB::raw("(CASE  
                                WHEN p.status = '0' THEN 'In Active'
                                WHEN p.status = '1' THEN 'Active'
                                 END) status_display")),
                        (DB::raw("(CHAR_LENGTH(p.employees) - CHAR_LENGTH(REPLACE(p.employees, ',', '')) + 1) as employees"))

                    );

                    if ($data['search_user']!=''){

                         $query->whereRaw(

                            "p.project_name like '%". $data['search_user'] ."%' || p.customer_name like '%". $data['search_user'] ."%'"  
                            
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
   
   public function editCompanyDetails($id)
    {
      $users = DB::table('users')->select('name','id')
                                 ->orderBy('name')
                                 ->whereNotIn('role', [1,4])
                                 ->where('status',1)
                                 ->get();
      $details = DB::table('projects as p')
                    ->select('p.*')
                    ->where("p.id",$id)
                    ->first();
          return view('edit_company_details',['details'=>$details,
                                              'users'  =>$users
                                            ]);

    }
    

    public function updateCompanyDetails(Request $request)
  {
    $data = $request->all();
     $params = array(                     
                     'customer_email'      => $request['customer_email'],
                     'customer_mobile'     => $request['customer_mobile'], 
                     'customer_designation'=> $request['customer_designation'],
                     'division'            => $request['division'],
                     'sub_division'        => $request['sub_division'],
                     'pan_number'          => $request['pan_number'],
                     'gst_number'          => $request['gst_number'],
                     'employees'           => $request['hidden_id'],
                     'updated_at'          => date('Y-m-d H:i:s')
                   );
      $address_data = DB::table('projects')
                  ->where('id', $request['id'])
                  ->update($params);             

   
        return redirect()->back()->with('status','Company Details Updated Successfully');      
  }
  
  public function editPrimaryContact($id)
    {
      $details = DB::table('customer_contacts as c')
                    ->select('c.*')
                    ->where('c.primary_contact',1)
                    ->where("c.id",$id)
                    ->first();
          return view('edit_primary_contact',['details'=>$details] );

    }
    

    public function updatePrimaryContact(Request $request)
  {
    $data = $request->all();
     $params = array(                     
                     'contact_name'        => $request['name'],
                     'contact_email'       => $request['email'], 
                     'contact_mobile'      => $request['mobile'],
                     'contact_designation' => $request['designation'],
                     'updated_at'          => date('Y-m-d H:i:s')
                   );
      $address_data = DB::table('customer_contacts')
                  ->where('id', $request['id'])
                  ->update($params);             

   
        return redirect()->back()->with('status','Primary Contact Updated Successfully');      
  }

  public function editSubContacts($id)
    {
      $details = DB::table('customer_contacts as c')
                    ->select('c.*')
                    ->where("c.id",$id)
                    ->first();
          return view('edit_sub_contacts',['details'=>$details] );

    }
    

    public function updateSubContacts(Request $request)
  {
    $data = $request->all();
     $params = array(                     
                     'contact_name'        => $request['name'],
                     'contact_email'       => $request['email'], 
                     'contact_mobile'      => $request['mobile'],
                     'contact_designation' => $request['designation'],
                     'updated_at'          => date('Y-m-d H:i:s')
                   );
      $address_data = DB::table('customer_contacts')
                  ->where('id', $request['id'])
                  ->update($params);              

   
        return redirect()->back()->with('status','Sub Contacts Updated Successfully');      
  }

  public function getCustomer($id)
    {
        
        $customer_data = DB::table('projects as p')
                       ->select('p.*','c.contact_name','c.contact_email','c.contact_mobile','c.contact_designation')
                       ->leftJoin('customer_contacts as c', 'c.project_id', '=', 'p.id') 
                       ->where('c.id',$id)
                       ->first();
         // echo "<pre>";print_r($customer_data);exit;
          return Response::json(array('success'=>1,
            'customer_data'    => $customer_data,
            'message'=>'OK'
          ));

    }

    public function updateCustomer(Request $request)
  {
    $data = $request->all();
     
     $params = array(
                     'customer_email'      => $request['update_customer_email'],
                     'customer_mobile'     => $request['update_customer_mobile'], 
                     'customer_designation'=> $request['update_customer_designation'],
                     'pan_number'          => $request['update_pan_number'],
                     'gst_number'          => $request['update_gst_number'],
                     'updated_at'          => date('Y-m-d H:i:s')
                   );
     echo "idd".$request['id'];
      $update_customer = DB::table('projects')
                          ->where('id', $request['id'])
                          ->update($params);

        echo "<pre>";print_r($update_customer);exit;

      //   $params1 = array(                     
                     
      //                'contact_name'        => $request['update_name'],
      //                'contact_email'       => $request['update_email'],
      //                'contact_mobile'      => $request['update_mobile'],
      //                'contact_designation' => $request['update_designation'],
      //                'updated_at'          => date('Y-m-d H:i:s')
      //              );
      // $address_data = DB::table('customer_contacts')
      //             ->where('project_id', $request['id'])
      //             ->update($params1);
  
      if($list)
      {
        return Response::json(array('success'=>1,'message'=>'Customer Details Updated Successfully.'));

      }else{
        return Response::json(array('success'=>0,'message'=>'Customer Details Not Updated.'));

      }      

  }

   public function deleteCustomer($id,$status)
  {
     if($status == 0)
     {
      $update_status = 1;
      $message = "Customer Activated."; 
     }else{
      $update_status = 0;
      $message = "Customer DeActivated.";
     }


    $delete_customer = array('status'   =>$update_status,
                        'updated_at' => date('Y-m-d H:i:s')
                        );
   
      $customer = DB::table('projects')
                  ->where('id', $id)
                  ->update($delete_customer);

      if($customer)
      {
        return Response::json(array('success'=>1,'message'=>$message));

      }else{
        return Response::json(array('success'=>0,'message'=>$message));
      }   
  }

   
}
