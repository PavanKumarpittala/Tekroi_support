<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Response;
use App\General;
use Log;
use Carbon\Carbon;
use PDF;
use \Crypt;
use Mail;
use App\Helpers\Helper as Helper;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
class ApiCorporateUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 public function GSTINTypes()
 {
    $gstin_types = DB::table('gstin_types')
                        ->select('id','name')
                        ->get();
        if(count($gstin_types)){
            return Response::json(array('success'=> 1,
              'gstin_types'=> $gstin_types
            ), 200); 
        }else{
             return Response::json(array('success'=> 0,
              'message'=>'Data Not Found.'
            ), 200);         
        }                         
     
 }
 public function AddCorporateUser(Request $request)
 {
    $request = $request->all();
    Log::info($request);
    //exit;
    $params = array(
                'name'          => $request['company_name'],
                'user_id'       => $request['user_id'],
                'is_gstin_registered'   => $request['is_gstin_registered'],
                'gst_number'            => isset($request['gst_number'])?$request['gst_number']:'',
                'gstin_type'            => isset($request['gstin_type'])?$request['gstin_type']:0,                  
                'pan_number'            => isset($request['pan_number'])?$request['pan_number']:'',
                'main_contact_number'   => $request['main_contact_name'],
                'main_contact_email'    => $request['main_contact_email'],
                'main_contact_mobile'   => $request['main_contact_mobile'],
                'no_of_users'           => $request['no_of_users'],
                'avg_certs_per_mnth'    => $request['avg_certs_per_mnth'],
                'nature_of_business'    => $request['nature_of_business']
               );

    $inserted_id = DB::table('companies_list')
                        ->insertGetId($params);
    foreach ($request['address_array'] as $key => $address) {

        $company_address_params = array('company_id'       => $inserted_id,
                                        'user_id'          => $request['user_id'],
                                        'address'             => $address['area'],
                                        'district_or_city' => $address['district_or_city'],
                                        'state'            => $address['state'],
                                        'pincode'          => $address['pincode'],
                                        'created_at'       => date('Y-m-d H:i:s')
                                        );
        $company_address = DB::table('company_addresses')
                                ->insertGetId($company_address_params);
    }
          
          if($inserted_id){
              return Response::json(array('success'=> 1,
                                          'message'=>'Company Details Added Succesfully.'
              ), 200); 
          }else{
               return Response::json(array('success'=> 0,
                                           'message'=>'Data Not Saved.'
              ), 200);         
          } 

 }
 public function GetCorpUserCompanyDetails($user_id)
 {

    $company_details = array();
    $company_data = DB::table('companies_list')
                        ->select('*')
                        ->where('user_id',$user_id)
                        ->get();
    $company_addresses = DB::table('company_addresses')
                                ->select('*','id as address_id')
                                ->where('user_id',$user_id)
                                ->get();
    $company_details['company_data'] = $company_data;
    $company_details['company_addresses'] = $company_addresses;


      if(sizeof($company_data) > 0){
          return Response::json(array(
                'success'=> 1,
                'company_data'=> $company_details,
                'company_activated' => isset($company_user->is_company_activated)?$company_user->is_company_activated:'',
                'subscription_activated' => isset($company_user->is_subscription_active)?$company_user->is_subscription_active:'',
                 'company_id'    => isset($company_user->id)?$company_user->id:'',
                                 ), 200); 
      }else{
           return Response::json(array('success'=> 0,
                                       'message'=>'Data Not Found.'
          ), 200);         
      }    

     
 }
public function AddCorporateCompanyUser(Request $request)
{
    $request = $request->all();
    Log::info($request); 
    $default_password = "agri@123";
    $params = array(
                     'name'         => $request['user_name'],
                     'email'        => $request['user_email'],
                     'password'     => Crypt::encrypt($default_password),
                     'mobile_number'=> $request['mobile_number'],
                     'user_type'    => $request['user_type'],
                     'company_id'   => $request['company_id']
                     //'allowed_count' => 999
                   );
        $params['created_at'] = date('Y-m-d H:i:s');

        $isExist = DB::table('mobile_users')
                        ->select("*")
                        ->where("email", $request['user_email'])
                        ->exists();
        if ($isExist) { 
         return Response::json(array('success'=>0,'message'=>'Email Already Exist.'));     
            
        }else{

          $inserted_id = DB::table('mobile_users')->insertGetId($params);
          $date = date('Y-m-d H:i:s');
          $date = strtotime($date);
          $final_date = strtotime("+90 day", $date);
          $period_date = date('Y-m-d', $final_date);
     if($inserted_id){

        $data['name'] = $request['user_name'];
        $data['email'] = $request['user_email'];
        $data['ref_id'] = $inserted_id;
        $data['password'] = $default_password;
        $data["title"]   = "AGRI Registration";

        $message = "Dear ".$request['user_name'].", Welcome onboard.
                    Congratulations! Your Agrireach Account has been setup, your password is ".$default_password."";

       // $this->sendMessage($request['mobile_number'],$message);

        // $pdf = PDF::loadView('emails.user_signup', $data);
        // $head["head"] = "AGRI REACH !";
        // Mail::send('emails.qc_email_view', $head, function($message)use($data, $pdf) {
        //     $message->to($data["email"], $data["email"])
        //             ->subject($data["title"])
        //             ->attachData($pdf->output(), "report.pdf");
        // });
          return Response::json(array(
                                'success'=>1,
                                'message'=>'User Created Successfully.',
                                'user_id'=>$inserted_id,
                                'user_type' => $request['user_type'],
                                'user_name' => $request['user_name'],
                                'created_at' => date('Y-m-d')
                              ));

               }else{
                return Response::json(array('success'=>0,'message'=>'User not Created.')); 
              }             

        }

}
public function GetCorporateCompanyUsers($user_id){

 $get_company = DB::table('companies_list')
                    ->select('id','user_id','is_company_activated','is_subscription_active')
                    ->where('user_id',$user_id)
                    ->first();
  $company_user = DB::table('mobile_users')
                        ->select('*')
                        ->where('company_id',$get_company->id)
                        ->get();

    if($company_user){
        return Response::json(array(
                    'success'=> 1,
                    'company_users'=> $company_user
        ), 200); 
    }else{
         return Response::json(array('success'=> 0,
          'message'=>'Data Not Found.'
        ), 200);         
    }


}
 public function ActivateCorporateCompanyUser($user_id)
 {
    $params = array('status' => 1,
                    'updated_at'=> date('Y-m-d H:i:s')
                   );

    $activate_user = DB::table('mobile_users')
                            ->where('id',$user_id)
                            ->update($params);

     if($activate_user){
        return Response::json(array(
                    'success'=> 1,
                    'message'=> 'User Succesfully Activated..!'
        ), 200); 
    }else{
         return Response::json(array('success'=> 0,
          'message'=>'User Not Updated.'
        ), 200);         
    }
    
 }
 public function CommuncationDetails()
 {
    $communication_info = DB::table('communication_info')
                        ->select('number','email')
                        ->get();
    if($communication_info){
       // echo "<pre>"; echo $communication_info['0']->number; exit;
        return Response::json(array(
                    'success'=> 1,
                    'whats_app'=> $communication_info['0']->number,
                    'callus'   => $communication_info['1']->number,
                    'email'    => $communication_info['2']->email,
        ), 200); 
    }else{
         return Response::json(array('success'=> 0,
          'message'=>'Data Not Found.'
        ), 200);         
    }
 }
 public function SaveSubscription(Request $request)
 {
   $request = $request->all();
   $sub_array = array('email'  => $request['email'],
                      'created_at' => date('Y-m-d H:i:s')
                     );
   $isExist = DB::table('subscriptions')
                ->select("id")
                ->where("email", $request['email'])
                ->exists();
    if ($isExist) { 
     return Response::json(array('success'=>0,'message'=>'you have Already Subscribed.'));
    }else{
           $inserted_id = DB::table('subscriptions')
                            ->insertGetId($sub_array);
            if($inserted_id){
                return Response::json(array(
                            'success'=> 1,
                            'messae'=> 'You have Successfully Subscribed to AGRIREACH' 
                ), 200); 
            }else{
                 return Response::json(array('success'=> 0,
                  'message'=>'Something went wrong.'
                ), 200);         
            }
    }
 }
public function BannerImages()
 {
    $upload_dir = url('').'/mobile_banner_images/';
    $banner_images = DB::table('mobile_banner_images')
                        ->select('id','name','image_src',DB::raw('CONCAT("'.$upload_dir.'",image_src ) as image_url'))
                        ->where('status',1)
                        ->get();
      if($banner_images){
          return Response::json(array('success'=> 1,
                                      'banners'=> $banner_images
          ), 200); 
      }else{
           return Response::json(array('success'=> 0,
                                       'message'=>'Data Not Found.'
          ), 200);         
      } 


 }


}