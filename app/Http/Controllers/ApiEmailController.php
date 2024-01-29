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
use Twilio\Rest\Client as TwilioClient;
use Webklex\PHPIMAP\ClientManager;


class ApiEmailController extends Controller
{
    const SUPPORT_GMAIL_CONFIG = 'tekroi.sap@gmail.com';
    const SUPPORT_OUTLOOKMAIL_CONFIG = 'support@tekroi.com';
    const DEFAULT_USEREMAIL_CONFIG = 'thananjeyan.g@tekroi.com';
    const DEFAULT_USEREMAIL_NAME = 'Siva Reddy';

    public function getOutlookMails()
    {

        $cm = new ClientManager($options = ["options" => ["debug" => false]]);
        $client = $cm->make([
            'host'          => 'imap.gmail.com',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => false,
            'username'      => SELF::SUPPORT_GMAIL_CONFIG,
            'password'      => 'pkpwhkqwtcbnnfah',
            'protocol'      => 'imap',
        ]);

        try {

            // Connect to the IMAP Server
            if ($client->connect())
            {
                // $status = $client->isConnected();
                // if (!$status) echo "Connnection Failed";
                // else echo 'Connection success';
                // exit;

                $folder = $client->getFolder('INBOX');

                $today = date('Y-m-d');
                $subractedDay = date('d.m.Y', strtotime($today . ' - 10 days'));
                $messages = $folder->query()->since($subractedDay)->unseen()->get();

                return $messages;
            }
            else
            {
                echo "Failed to connect to the IMAP server.";
            }

        } catch (\Exception $e) {
            echo 'An Error Occured : ',  $e->getMessage(), "\n";
        }

        return false;
    }
    public function supportEmail()
    {
        Log::info(date('Y-m-d H:i:s'). 'CRON Job Support Email Running');
        // exit;

        $supportEmail = SELF::SUPPORT_OUTLOOKMAIL_CONFIG;
        $emails = $this->getOutlookMails();

        if (!$emails) return 'No Email';

        foreach ($emails as $email)
        {
            $to_address = $cc_email_address = [];
            $subject = !empty($email->getSubject()) ? $email->getSubject() : "No Subject";
            $fromAddress = $email->getFrom()[0]->mail;
            $fromName = $email->getFrom()[0]->full;
            $domain = $email->getFrom()[0]->host;

            $recipients = $email->getTo()->toArray();
            foreach ($recipients as $recipient) {
                $to_address[] = $recipient->mail;
            }

            if (isset($email->getAttributes()['cc'])) {
                $cc_addresses = $email->getAttributes()['cc']->toArray();
                foreach ($cc_addresses as $address) {
                    $cc_email_address[] = $address->mail;
                }
            }

            $message = $email->getHTMLBody();
            $received_at = $email->getDate();
            $email->setFlag('SEEN');

            // Mail address same from support@tekroi.com
            if (strtolower($fromAddress) == strtolower($supportEmail))
                continue;

            // If Inactive Domain
            if($this->isInactiveDomain($domain))
                continue;
            // $domain


            $params = array (
                'email' => $fromAddress,
                'body' => $message,
                'subject' => $subject,
                'name' => $fromName,
                'received_at' => date("Y-m-d H:i:s", strtotime($received_at)),
                'created_at' => date('Y-m-d H:i:s')
            );

            $inserted_email = DB::table('support_email')->insertGetId($params);

            if ($inserted_email)
            {
                echo 'Inserted';

                // Check Whether mail is New Ticket or Old Reply
                $ticket_id = $this->getTicketId($subject);

                $new_support_ticket = true;
                if ($ticket_id) {
                    echo 'Reply support';
                    $support_ticket = $this->getSupportTicket($ticket_id);
                    if ($support_ticket) {
                        $new_support_ticket = false;
                        $params_sup = array(
                            'ticket_id' => $support_ticket->id,
                            'subject' => $subject,
                            'summary' => $message,
                            'email_from' => $fromAddress,
                            'email_name' => $fromName,
                            'received_at' => date("Y-m-d H:i:s", strtotime($received_at)),
                            'created_at' => date('Y-m-d H:i:s')
                        );
                        $inserted_reply_id = DB::table('support_ticket_replies')->insertGetId($params_sup);

                        $this->mailAttachments($email, $inserted_reply_id, 'reply');
                    }
                }

                if ($new_support_ticket) { // New Ticket
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
                        // Send Mail details to Not assigned Any person, default Email
                        $user_email = SELF::DEFAULT_USEREMAIL_CONFIG;
                        $user_name = SELF::DEFAULT_USEREMAIL_NAME;
                    }

                    $params_sup = array(
                        'ticket_id' => $ticket_id,
                        'assigned'  => $assigned_user_id,
                        'email_from' => $fromAddress,
                        'email_name' => $fromName,
                        'ticket_summary' => $message,
                        'domain' => $domain,
                        'subject' => $subject,
                        'subject_ticketid' => $subject_ticketid,
                        'received_at' => date("Y-m-d H:i:s", strtotime($received_at)),
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    $inserted_id = DB::table('support_tickets')->insertGetId($params_sup);
                    $attachment_paths = $this->mailAttachments($email, $inserted_id, 'new');

                    $manager_email = $this->get_manager_email($assigned_user_id);

                    $this->sendEmailToClient($fromAddress, $user_email, $ticket_id, $subject_ticketid, $user_name, $message, $attachment_paths, $manager_email, $cc_email_address, $supportEmail,  $to_address);

                }
            }

        }


        echo '<br>Success Email DONE<br>';

    }

    function isInactiveDomain($domain)
    {
        $rows = DB::table('user_domains')->where('domain', 'LIKE', '%' . $domain . '%')->get();
        if ($rows) {
            foreach($rows as $row) {
                if ($row->status == 1)
                    return false;
            }
            return true;
        }
        return false;
    }

    function getTicketId($subject)
    {
        // if (preg_match('/^Re:/i', trim($subject))) {
        preg_match('#\[Ticket \# (.*?)\]#', $subject, $match);
        if ($match)
            return $match[1];
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


    public function sendEmailToClient($client_email, $user_email, $ticket_id, $subject_ticketid, $username, $mail_message, $attachments = [], $manager_email = null, $cc_email_address = [], $support_email, $to_address = [])
    {
        // Send a Email to Client and Team Member with attachments If any
        $email_to_send[] = $client_email;
        $email_to_send[] = $user_email;

        $email_cc_send = [];
        if ($manager_email)  $email_cc_send[] = $manager_email;
        // $email_cc_send[]= "siva.reddy@tekroi.com";  // Ticket Trackers

        $maildata["to_emails"] = $email_to_send;
        $cc_emails = array_merge($email_cc_send, $cc_email_address, $to_address);


        if (($key = array_search($user_email, $cc_emails)) !== false) {
            unset($cc_emails[$key]);
        }

        $support_email = SELF::SUPPORT_OUTLOOKMAIL_CONFIG;
        $cc_final_mail = [];
        foreach ($cc_emails as $email)
        {
            if (strtolower($email) == $support_email)
            {
                continue;
            }
            $cc_final_mail[]= $email;
        }

        $maildata["cc_emails"] = array_unique($cc_final_mail);
        $maildata["bcc_email"] = $support_email;

        $maildata["subject"] = $subject_ticketid;
        $maildata["body"] = 'Thank you for contacting us. This is an automated response confirming the receipt of your ticket and assigned to ' . $username . '. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies';

        $maildata["ticket_id"] = $ticket_id;
        $maildata["ticket_message"] = $mail_message;

        Mail::send('emails.myTestMail', $maildata, function ($message) use ($maildata, $attachments) {
            $message->to($maildata["to_emails"])
                ->cc($maildata["cc_emails"])
                ->bcc($maildata["bcc_email"])
                ->subject($maildata["subject"]);

            if (is_array($attachments))
                foreach ($attachments as $file) {
                    $message->attach($file);
                }
        });
    }


    public function get_manager_email($assigned_user_id)
    {
        $manager = DB::select('SELECT email FROM users JOIN user_assigned ON user_assigned.user = users.id WHERE  user_assigned.assigned_user ="' . $assigned_user_id . '" ');

        if ($manager) return $manager[0]->email;

        return null;
    }

    public function findUser($domain)
    {
        // Search Employee using Domain
        $assign_user = DB::select('SELECT users.id as id,users.name as name, users.email as email, users.role as role FROM user_domains JOIN users ON user_domains.user_id = users.id WHERE user_domains.status=1 AND user_domains.domain ="' . $domain . '" ');

        return $assign_user;
    }

    function get_cc_email_address($header_cc)
    {
        $cc_email_address = [];
        if ($header_cc && is_array($header_cc)) {
            foreach ($header_cc as $cc) {
                $cc_email_address[] = $cc->mailbox . "@" . $cc->host;
            }
        }
        return $cc_email_address;
    }

    function mailAttachments($email, $ticket_table_id, $ticket_type)
    {
        if (!$email->hasAttachments()) return;

        $attachments = $email->getAttachments();

        /* iterate through each attachment and save it */
        $attachment_paths = array();
        foreach ($attachments as $attachment) {

                $filename = $attachment->getName();

                // Storage::put(public_path("$folder/").$file_name_uid, $attachment->getContent());

                $folder = public_path('attachment');
                if (!is_dir($folder)) {
                    mkdir($folder);
                }

                $file_name_uid = $ticket_table_id . "-" . time() . "-" . $filename;
                $file_full_path = $folder . "/" . $file_name_uid;
                $fp = fopen($file_full_path, "w+");
                fwrite($fp, $attachment->getContent());

                fclose($fp);

                $params_file = array(
                    'ticket_id' => $ticket_table_id,
                    'type'  => $ticket_type,
                    'filename' => $file_name_uid
                );
                $inserted_file_id = DB::table('support_ticket_attachments')->insertGetId($params_file);
                $attachment_paths[] = $file_full_path;
        }
        return $attachment_paths;
    }
}
