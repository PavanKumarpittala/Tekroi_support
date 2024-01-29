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
        Log::info(date('Y-m-d H:i:s'));
        /*  Email details of Support Mail id */
        $hostname = '{mail.tekroi.com:143/notls}INBOX';
        $username = 'supportdev@tekroi.com';
        $password = 'Tekroi@123';
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect: ' . imap_last_error());
        $date = date("d M Y", strToTime("- 1 days"));
        $emails = imap_search($inbox, "UNSEEN SINCE \"$date\"");
        if ($emails) {
            foreach ($emails as $email_number) {
                $header = imap_headerinfo($inbox, $email_number);

                echo $header->fromaddress;
                $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1.1));

                if ($message == "") {
                    $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1));
                }


                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                // $from_name = $header->from[0]->personal;
                $received_at = $header->date;
                $subject = isset($header->subject) ? $header->subject : "No Subject";
                $from_address = $header->fromaddress;
                $toaddress = $header->toaddress;

                if (isset($header->cc))
                    $cc_email_address = $this->get_cc_email_address($header->cc);
                else
                    $cc_email_address = [];

                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);
                // echo "<pre>"; print_r($structure);


                $params = array(
                    'email' => $from,
                    'body' => $message,
                    'subject' => $subject,
                    'name' => $from_address, // 'attachment'=>$file_name_uid,
                    'received_at' => $newDate = date("Y-m-d H:i:s",strtotime($received_at)), 'created_at' => date('Y-m-d H:i:s')
                );
                $inserted_email = DB::table('support_email')->insertGetId($params);

                if ($inserted_email) {
                    echo 'Inserted';
                    /* Fetch Domain of Employee and Send mail */
                    $parts = explode("@", $from);
                    $domain = $parts[1];

                    // Check Whether mail is New Ticket or Old Reply
                    $ticket_id = $this->getTicketId($subject);

                    $new_support_ticket = true;
                    if($ticket_id) {
                        echo 'Reply support';
                        $support_ticket = $this->getSupportTicket($ticket_id);
                        if ($support_ticket) {
                            $new_support_ticket = false;
                            $params_sup = array(
                                'ticket_id' => $support_ticket->id,
                                'subject'=> $subject,
                                'summary' => $message,
                                'email_from'=>$from,
                                'email_name'=>$from_address,
                                'received_at' => date("Y-m-d H:i:s", strtotime($received_at)),
                                'created_at' => date('Y-m-d H:i:s')
                            );
                            $inserted_reply_id = DB::table('support_ticket_replies')->insertGetId($params_sup);

                            $this->mailAttachments($structure, $inbox, $email_number,$inserted_reply_id, 'reply');
                        }

                    }

                    if ($new_support_ticket){ // New Ticket
                        echo 'New Support';
                        $ticket_id = $this->generateTicketId();
                        $assign_user = $this->findUser($domain);
                        $subject_ticketid = "[Ticket # $ticket_id] : " . $subject;
                        $assigned_user_id = 0;
                        $manager_email = null;
                        if (isset($assign_user[0]->id)) {
                            $assigned_user_id = $assign_user[0]->id;
                            $user_email = $assign_user[0]->email;
                            $user_name = $assign_user[0]->name;
                            $user_role = $assign_user[0]->role;
                            if ($user_role == 3)
                                $manager_email = $this->get_manager_email($assigned_user_id);

                        } else {
                            // Send Mail details to default Email
                            $user_email = 'info@tekroi.com';
                            $user_name = 'Tekroi Info';
                        }

                        $params_sup = array(
                            'ticket_id' => $ticket_id,
                            'assigned'  => $assigned_user_id,
                            'email_from'=>$from,
                            'email_name'=>$from_address,
                            'ticket_summary' => $message,
                            'domain' => $domain,
                            'subject'=>$subject,
                            'subject_ticketid'=>$subject_ticketid,
                            'received_at' =>date("Y-m-d H:i:s", strtotime($received_at)),
                            'created_at' => date('Y-m-d H:i:s')
                        );
                        $inserted_id = DB::table('support_tickets')->insertGetId($params_sup);
                        $attachment_paths = $this->mailAttachments($structure, $inbox, $email_number,$inserted_id, 'new');

                        $manager_email = $this->get_manager_email($assigned_user_id);

                        $this->sendEmailToClient($from, $user_email, $ticket_id, $subject_ticketid, $user_name, $message, $attachment_paths, $manager_email, $cc_email_address, $username);

                        // $this->sendAssignedTicketToEmployee($from, $user_email, $ticket_id, $subject_ticketid, $user_name, $message, $attachment_paths);
                    }

                }

            }
        }
        imap_close($inbox);
    }

    function getTicketId($subject)  {
        if(preg_match('/^Re:/i', trim($subject))) {
            preg_match('#\[Ticket \# (.*?)\]#', $subject, $match);
            if($match) return $match[1];
        }
        else
        return false;
    }

    function generateTicketId()
    {
        $letters = "ABCEDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomString = '';
        for ($i = 0; $i < 3; $i++) {
            $getRandomNumber = rand(0, strlen($letters) - 1);
            $randomString .= $letters[$getRandomNumber];
        }
        $randomNumber = rand(time() % 1000000, 9999999);
        $randomAlphaNumeric = $randomString . '-' . $randomNumber;
        return $randomAlphaNumeric;
    }

    public function getSupportTicket($ticket_id)
    {
        $ticket = DB::table('support_tickets')->where('ticket_id', $ticket_id)->first();
        return $ticket;
    }


    public function sendEmailToClient($client_email, $user_email, $ticket_id, $subject_ticketid, $username, $mail_message, $attachments = [], $manager_email= null, $cc_email_address = [], $support_email)
    {
        // Send a Email to Client and Team Member with attachments If any
        $email_to_send[]= $client_email;
        $email_to_send[]= $user_email;

        $email_cc_send = [];
        if ($manager_email)  $email_cc_send[]= $manager_email;
        // $email_cc_send[]= "siva.reddy@tekroi.com";  // Ticket Trackers

        $maildata["to_emails"] = $email_to_send;
        $maildata["cc_emails"] = array_merge($email_cc_send, $cc_email_address);

        if (($key = array_search($support_email, $maildata["cc_emails"])) !== false) {
            unset($maildata["cc_emails"][$key]);
        }

        $maildata["subject"] = $subject_ticketid;
        $maildata["body"] = 'Thank you for contacting us. This is an automated response confirming the receipt of your ticket and assigned to '.$username.'. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies';
        // $maildata["body"] = 'Dear Sir/Madam,
        //                      We have received your request, Based on that we have created a ticket, Ticket Id details # ' . $ticket_id . ' and assigned to ' . $username;

        $maildata["ticket_id"] = $ticket_id;
        $maildata["ticket_message"] = $mail_message;

       Mail::send('emails.myTestMail', $maildata, function($message)use($maildata, $attachments) {
           $message->to($maildata["to_emails"])
                    ->cc($maildata["cc_emails"])
                   ->subject($maildata["subject"]);

           if(count($attachments) > 0)
               foreach ($attachments as $file){
                   $message->attach($file);
               }

       });

    }

    // public function sendAssignedTicketToEmployee($client_email, $user_email, $ticket_id, $subject_ticketid, $username, $mail_message, $attachments = [])
    // {
    //     // Send a Email to Client with attachments If any
    //     $maildata["email"] = [$user_email];
    //     $maildata["subject"] = $subject_ticketid;
    //     $maildata["body"] = 'Dear '.$username.', Ticket has been assigned to you from '.$client_email.'.Please look into it and update the status.';

    //     $maildata["ticket_id"] = $ticket_id;
    //     $maildata["ticket_message"] = $mail_message;

    //    Mail::send('emails.myTestMail', $maildata, function($message)use($maildata, $attachments) {
    //        $message->to($maildata["email"])
    //                ->subject($maildata["subject"]);

    //        if(count($attachments) > 0)
    //            foreach ($attachments as $file){
    //                $message->attach($file);
    //            }

    //    });

    // }


    public function get_manager_email($assigned_user_id)
    {
        $manager = DB::select('SELECT email FROM users JOIN user_assigned ON user_assigned.user = users.id WHERE  user_assigned.assigned_user ="' . $assigned_user_id . '" ');

        if ($manager) return $manager[0]->email;

        return null;
    }

    public function findUser($domain)
    {
        // Search Employee using Domain
        $assign_user = DB::select('SELECT users.id as id,users.name as name, users.email as email, users.role as role FROM user_domains JOIN users ON user_domains.user_id = users.id WHERE status=1 AND user_domains.domain ="' . $domain . '" ');

        return $assign_user;
    }

    function get_cc_email_address($header_cc) {
        $cc_email_address = [];
        if ($header_cc && is_array($header_cc)) {
            foreach($header_cc as $cc) {
                $cc_email_address[] = $cc->mailbox. "@" . $cc->host;
            }
        }
        return $cc_email_address;
    }
    function mailAttachments($structure, $inbox, $email_number, $ticket_table_id, $ticket_type)
    {
        $attachments = array();

        /* if any attachments found... */
        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                    /* 3 = BASE64 encoding */
                    if ($structure->parts[$i]->encoding == 3) {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        /* iterate through each attachment and save it */
        $attachment_paths = array();
        foreach ($attachments as $attachment) {
            if ($attachment['is_attachment'] == 1) {
                $filename = $attachment['name'];
                if (empty($filename)) $filename = $attachment['filename'];

                if (empty($filename)) $filename = time() . ".dat";
                $folder = public_path('attachment');
                if (!is_dir($folder)) {
                    mkdir($folder);
                }

                $file_name_uid = $ticket_table_id . "-" . time() . "-" . $filename;
                $file_full_path = $folder . "/" . $file_name_uid;
                $fp = fopen($file_full_path, "w+");
                fwrite($fp, $attachment['attachment']);

                fclose($fp);

                $params_file = array(
                    'ticket_id' => $ticket_table_id,
                    'type'  => $ticket_type,
                    'filename'=>$file_name_uid
                );
                $inserted_file_id = DB::table('support_ticket_attachments')->insertGetId($params_file);
                $attachment_paths[] = $file_full_path;
            }
        }

        return $attachment_paths;
    }


}
