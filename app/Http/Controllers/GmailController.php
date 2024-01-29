<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GmailController extends Controller
{
    function index() {

        $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $username = 'tekroipvt@gmail.com'; // Password : Tekroi@123
        $password = 'klyyzxohwizjvhsy';
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect: ' . imap_last_error());
        $date = date("d M Y", strToTime("- 1 days"));
        $emails = imap_search($inbox, "UNSEEN SINCE \"$date\"");
        // print_r($emails);
        if ($emails) {
            foreach ($emails as $email_number)
            {
                $header = imap_headerinfo($inbox, $email_number);
                $header->fromaddress;
                $overview = imap_fetch_overview($inbox,$email_number,0);
                $structure = imap_fetchstructure($inbox, $email_number);
                $message = imap_fetchbody($inbox,$email_number,1);

                if(isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
                    $part = $structure->parts[1];

                    // var_dump($message);
                    // var_dump($part);

                    // echo   $part->parameters[0]->attribute;
                    // echo $part->parameters[0]->value;
                    // echo $part->ifdescription;

                    // echo "*" .$message."*";

                    if($part->encoding == 3 && ($part->parameters[0]->attribute=='charset' && $part->parameters[0]->value== 'utf-8')) { // Internation mail without attachment
                        $message = imap_base64($message);

                    }else if($part->encoding == 3 && $part->ifdescription == 1) {  // Internation mail with attachment
                        // $message = imap_base64($message);
                        // $message =utf8_encode(imap_base64($message));
                        $message = base64_decode($message);
                    }
                    else if($part->encoding == 3){  // Indian mails with Attachment

                    $message = quoted_printable_decode($message);
                    }
                    else if($part->encoding == 1) {
                        $message = imap_8bit($message);
                    } else {
                        $message = imap_qprint($message);
                    }


                }else{ // Indian mail without attachment

                $message = quoted_printable_decode($message);
                }


                $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                // $from_name = $header->from[0]->personal;
                $received_at = $header->date;
                $subject = isset($header->subject) ? $header->subject : "No Subject";
                $from_address = $header->fromaddress;
                $toaddress = $header->toaddress;

                echo "Subject : " . $subject."<br />";
                echo "Message : " . $message."<br />";
            }
        }
        else
        {
            echo "No Unread Email";
        }



    }
}
