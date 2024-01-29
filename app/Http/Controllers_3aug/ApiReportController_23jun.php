<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Response;
use App\General;
use Log;
use Carbon\Carbon;
use PDF;
use Mail;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;

class ApiReportController extends Controller
{

	  public function supportEmail()
   {



            //       $details = [
            //       'subject' => 'Tekroi Ticket Id-',
            //       'title' => 'Mail from Tekroi Support',
            //       'body' => 'Dear ajaja ,Ticket has been assigned to you from ajja.Please look into it and update the status. Ticket Id: sas',
                                
            //           ];        

            // \Mail::to('pujitha.balineni@tekroi.com')->send(new \App\Mail\MyTestMail($details));

            // exit;
    
     // print imap_open("{imap.gmail.com:993/imap/ssl}INBOX", "feedback.agrireach@slc-india.com", "feedback@slcm1021");
       // "{imap.gmail.com:993/debug/imap/ssl/novalidate-cert}INBOX";
      /* connect to gmail */
      
        $hostname = '{mail.tekroi.com:143/notls}INBOX';
        $username = 'pujitha.balineni@tekroi.com';
        $password = 'Tekroi@123';
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect: ' . imap_last_error());
        //$emails = imap_search($inbox, 'ALL');

         $date = date ( "d M Y", strToTime ( "- 1 days" ) );
         //$emails = imap_search ( $inbox, "SINCE \"$date\"");
         $emails = imap_search( $inbox, "UNSEEN SINCE \"$date\"");

        //$UID = imap_uid($imap, 1);
         // echo "<pre>"; print_r($emails); exit;

        //echo "count".count($emails); print_r($emails);
        if ($emails) {
            $output = '';
            $mails = array();
            rsort($emails);
            foreach ($emails as $email_number) {

              $ticket_id = 'TR_'.rand(10,10000);

            // echo "<pre>"; print_r($email_number); exit;
            //   exit;
                $header = imap_headerinfo($inbox, $email_number);
                // echo "<pre>"; print_r($header);
                // exit;
                $message = quoted_printable_decode (imap_fetchbody($inbox, $email_number, 1));
                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                $received_at = $header->date;
                $subject = isset($header->subject)?$header->subject:"No Subject";
                $from_address = $header->fromaddress;
                $toaddress = $header->toaddress;
               // if(imap_search($inbox, 'UNSEEN')){
                    /*Store from and message body to database*/

             /* get mail structure */
             $structure = imap_fetchstructure($inbox, $email_number);
                  // echo "<pre>"; print_r($structure);

             
             $attachments = array();

        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                    /* 3 = BASE64 encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        /* iterate through each attachment and save it */
                  foreach($attachments as $attachment)
                  {
                      if($attachment['is_attachment'] == 1)
                      {
                          $filename = $attachment['name'];
                          if(empty($filename)) $filename = $attachment['filename'];

                          if(empty($filename)) $filename = time() . ".dat";
                          $folder = "attachment";
                          if(!is_dir($folder))
                          {
                               mkdir($folder);
                          }

                       $file_name_uid = $email_number . "-" .time()."-".$filename;
                          $fp = fopen("./". $folder ."/".$file_name_uid, "w+");
                          fwrite($fp, $attachment['attachment']);
                           
                          fclose($fp);

                         // $file_name = $email_number.'_'.$filename;
                      }
                  }
          //     }
          // } 

          /* close the connection */
          // imap_close($inbox);

          echo "all attachment Downloaded";



                  $parts = explode("@",$from); 
                  $domain = $parts[1];

                    $params = array(
                      // 'ticket_id' => 'TR_'.rand(10,50),
                      'email'=>$from, 
                      'body'=>$message,
                      'subject'=>$subject,
                      'name'=>$from_address,
                      // 'attachment'=>$file_name_uid,
                      'received_at'=>$newDate = date("Y-m-d H:i:s", strtotime($received_at)),
                      'created_at'=>date('Y-m-d H:i:s')
                       ); 
                $inserted_email = DB::table('support_email')->insertGetId($params);
                 if($inserted_email)
                 {
          $assign_user = DB::select('select id,employee_id,name,email from users where support_domain ="'.$domain.'" '); 


                    if(isset($assign_user[0]->id)){
                      $assigned_user = $assign_user[0]->id;
                      // exit;

                      // Send Email $assign_user->employee_email;

                  $details = [
                  'subject' => 'Tekroi Ticket Id-'.$ticket_id,
                  'title' => 'Mail from Tekroi Support',
                  'body' => 'Dear '.$assign_user[0]->name.' ,Ticket has been assigned to you from '.$domain.'.Please look into it and update the status. Ticket Id: '.$ticket_id.''
                  
                      ];        

            \Mail::to($assign_user[0]->email)->send(new \App\Mail\MyTestMail($details));




    // client email 
            $details = [
                  'subject' => 'Tekroi Ticket Id-'.$ticket_id,  
                  'title' => 'Mail from Tekroi Support',
                  'body' => 'Dear Sir,
  We have received your request,Based on that we have created a ticket, Ticket Id'.$ticket_id.' And assigned to '.$assign_user[0]->name.''
                  
                      ];        

            \Mail::to($from)->send(new \App\Mail\MyTestMail($details));

                    }else{
                      $assigned_user = 0;
                    }
                   $params_sup = array(
                     'ticket_id' => $ticket_id,
                     'assigned'  => $assigned_user,
                      // 'email'=>$from, 
                      'ticket_summary'=>$message,
                      'attachment'=>$file_name_uid,
                      // 'subject'=>$subject,
                      // 'name'=>$from_address,
                       'received_at'=>$newDate = date("Y-m-d H:i:s", strtotime($received_at)),
                      'created_at'=>date('Y-m-d H:i:s')
                       ); 
                      $inserted_id = DB::table('support_tickets')->insertGetId($params_sup);
                 }

                    //return view('emails.display');
               // }
                // else{
                //     $data = Email::all();
                //     return view('emails.display',compact('data'));

                // }
            }

        }
            imap_close($inbox);


    }

    public function supportTickets()
   {
     // print imap_open("{imap.gmail.com:993/imap/ssl}INBOX", "feedback.agrireach@slc-india.com", "feedback@slcm1021");
       // "{imap.gmail.com:993/debug/imap/ssl/novalidate-cert}INBOX";
      /* connect to gmail */
      
        $hostname = '{mail.tekroi.com:143/notls}INBOX';
        $username = 'pujitha.balineni@tekroi.com';
        $password = 'Tekroi@123';
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect: ' . imap_last_error());
        //$emails = imap_search($inbox, 'ALL');

         $date = date ( "d M Y", strToTime ( "- 1 days" ) );
        $emails = imap_search ( $inbox, "SINCE \"$date\"");
        // $emails = imap_search ( $inbox, "UNSEEN SINCE \"$date\"");

        //$UID = imap_uid($imap, 1);

        //  echo "<pre>"; print_r($emails); exit;

        //echo "count".count($emails); print_r($emails);
        if ($emails) {
            $output = '';
            $mails = array();
            rsort($emails);
            foreach ($emails as $email_number) {

             $ticket_id = 'TR_'.rand(10,10000);

           // echo "<pre>"; print_r($email_number); exit;
            //   exit;
                $header = imap_headerinfo($inbox, $email_number);
                // echo "<pre>"; print_r($header);
                // exit;
                $message = quoted_printable_decode (imap_fetchbody($inbox, $email_number, 1));
                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                $received_at = $header->date;
                $subject = isset($header->subject)?$header->subject:"No Subject";
                $from_address = $header->fromaddress;
                $toaddress = $header->toaddress;
               // if(imap_search($inbox, 'UNSEEN')){
                    /*Store from and message body to database*/

                  $parts = explode("@",$from); 
                 echo $domain = $parts[1];



          // $assign_user = DB::table('employee_details')
          //                       ->select('id','employee_id','employee_email')
          //                       ->where('support_domain','"'.$domain.'"')
          //                       ->get();
          // $assign_user = DB::select('select id, employee_id,employee_email from employee_details where support_domain = "'.$domain.'"');                
  // dd($assign_user);
                   // echo "<pre>"; print_r($assign_user); exit;
            //echo "yetet".$assign_user[0]->id;

        //   if(isset($assign_user->id)){
        //     // echo "aa".$assigned_user = $assign_user->id;
        //     // exit;

        //     // Send Email $assign_user->employee_email;

        // $details = [
        // 'title' => 'Mail from Support',
        // 'body' => 'Dear pujitha,Ticket has been assigned to you from '.$domain.'.Please look into it and update the status. Ticket Id: '.$ticket_id.''
        
        //     ];
           
        //     \Mail::to($assign_user->employee_email)->send($details);          
        //     dd("Email is Sent.");

        //   }else{
        //     $assigned_user = 0;
        //   }
                    $params = array(
                     'ticket_id' => 'TR_'.rand(10,10000),
                     'assigned'  => $assigned_user,

                      // 'email'=>$from, 
                      'ticket_summary'=>$message,
                      // 'subject'=>$subject,
                      // 'name'=>$from_address,
                       'received_at'=>$newDate = date("Y-m-d H:i:s", strtotime($received_at)),
                      'created_at'=>date('Y-m-d H:i:s')
                       ); 
                  $inserted_id = DB::table('support_tickets')->insertGetId($params);
              if($inserted_id){
              //  send client email  'email'=>$from

              }
                    //return view('emails.display');
               // }
                // else{
                //     $data = Email::all();
                //     return view('emails.display',compact('data'));

                // }
            }

        }
            imap_close($inbox);

    }



}


