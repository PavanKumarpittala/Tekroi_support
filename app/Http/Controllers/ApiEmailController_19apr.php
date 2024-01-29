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
    public function getOutlookMails()
    {
        $CLIENT_ID = "4546fd13-0944-4bc1-b0ba-66ba76bcb46d";
        $CLIENT_SECRET = "vB98Q~bWPDTN5a1LrvQ03Uc~cuq_4XwhMTfkQdg-";
        $TENANT = "fcb610c8-8233-4f53-9e08-7e49c64f5e1f";
        // $REFRESH_TOKEN = "0.AVYAyBC2_DOCU0-eCH5Jxk9eHxP9RkVECcFLsLpmuna8tG2fACA.AgABAAEAAAD--DLA3VO7QrddgJg7WevrAgDs_wUA9P8vB9K2ivrmF_oIkG-0A5MQ4fHKTIjXm-5NhltfT2SVn3GsI1JBGsU6ntxe_ceKJs9aUSosX92lVxiZkLQmvwos2-YGB-3Y_1vij7jaYACudPnGMKFRjsi1p2ULLwrYtvF5XrrH6a8KmCjRS-tADlT2p8sOQ5sQkYDGw9YJShW9ZFsUpEeQAW7bev_2vzTd7HGheUcGLjgdayBC9KHeN2KLm4n4SjMaTAiyKe9p8tyWZw5oi_czRd1jiv9pFCDw2y90P2uMsL7PWWfYwyyYpsxxbAnvwGoX2IRQbpfcPuGqAfTjtIscl6zX6DhqaoJy8WoH315fI-I1gHuR2bNkjSM2dboQ4KcNPqavxaDU-pfNvNhbJ1uxHOYKWAFIdmOrjIHjpLM668vdXhsg_MAS3jVdSSgad78clld5-0UsDJT8funONLaXdGOpDDsOqHz0H54u2y8lNC1pgy7Q8h4kddZMqDiCNUCjmWKm6uw54U4m_cXD_OSa02is8vZoyVg6eb8tnxE1acxfFLO7cn4HeilszshIBoHNYfjMcxpTsEJbHcvb8XSCBzX6juFudwSHedo6Fiuv5D7fxMipyp2sqz1EBJUobUbZ1R9MuwxD53BRVGfpSGIMoI2YYLEWA5y07iI7-HuxPw3-N8CDFFTXNJYP4NeBBEME6kCqMD--17Y3BVLa-knv_tOCjVUWf-2c0wdebgNiyPm2J9K75Is33dcaPjAHyO_cXc7Fi0U92zpJ_eHc0GpSceoFeLQ656HGbzOy8lHBav3gyYiVNVPpuVErjrq2Z8nDjSX1lDHqTeLZf_JUMlg8nuc1";

        $REFRESH_TOKEN = "0.AVYAyBC2_DOCU0-eCH5Jxk9eHxP9RkVECcFLsLpmuna8tG2fACA.AgABAAEAAAD--DLA3VO7QrddgJg7WevrAgDs_wUA9P_tAdqBR7ubOsacYTanwYYXuRfbyVuU3SPDRo5hlWtNegupyWlyizsmek8CuF18EB05zDQ_ClSKPX5hJw-9ELIr6RhuyW-9YTNlMeHr-2Ey3C07TPCUN5EL5sdWg6OvsJJ9LrXfLL6AaqtAuhil14y_6ZKwg5sFGv9xkK6JDq6tvQQpriNeHlSw0yG9AwQ0xgiVXH3nIHz0Hgx6y0wmbo3RQlXmgWffQMJzJSAt4qHepuAfYz7Qnz96NZTcY9KRbw7HpQ7VDizHVQ1vVCeNdISoA_XmeSFNIIcb2ns-FtH701YOR7hniGMbD69NwnuHDiMyg_BHcAqIXkVvlp5LUu3-XG1cGeeICzZ5LLj8TI6fI9pJTZ7R7DYK0T_bZHU5g63oOfnvMOTJ74guRvixbLTGr3I2To0sC64d41GYv8mrPPxRPzPFoBVR5USRCM3M1oI5nGoMVbznwj1RhZaXFYBizlUTYB2oiL_oopH-tClU0D0q1hRS1CKqKGfVIEaKmYLHTupoV1ICI806nWya9xDrVrb1n4uvSwlGYd9MTv4SMsVEPAmZs1e8SBmH95gXIis9924fiqHS_bTDbWZTvACOO0_rkfEfJ9g-gCsElNIwU8MagPiFLL6yHtyM7iYgy0_UaPRC7yWkz0-_wowyc_tiI7fTnBSaO1nC4dlJ90XLBvBn7V3iX5YMtjAApbPjeKimzpq7CPdw7rPTdyjBlz86qlsH6zemQFlF1U8-qJflBk__Cbh7FeRe4QVZkOmS4H5dDgxCyeno-qxix_D_BEeZur8Kj37KxUrLDlhC-14OM6XkSydCtAyR02Mz";

        $url = "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";

        $param_post_curl = [
            'client_id' => $CLIENT_ID,
            'client_secret' => $CLIENT_SECRET,
            'refresh_token' => $REFRESH_TOKEN,
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param_post_curl));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //ONLY USE CURLOPT_SSL_VERIFYPEER AT FALSE IF YOU ARE IN LOCALHOST !!!
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // NOT IN LOCALHOST ? ERASE IT !

        $oResult = curl_exec($ch);
        if (!empty($oResult)) {
            echo ("Connecting to the mail box... \n");
            $array_php_resul = json_decode($oResult, true);
            if (isset($array_php_resul["access_token"])) {

                $access_token = $array_php_resul["access_token"];
                //$cm = new ClientManager($options = ["options" => ["debug" => true]]);
                $cm = new ClientManager();
                $client = $cm->make([
                    'host'          => 'outlook.office365.com',
                    'port'          => 993,
                    'encryption'    => 'ssl',
                    'validate_cert' => false,
                    'username'      => 'support@tekroi.com',
                    'password'      => $access_token,
                    'protocol'      => 'imap',
                    'authentication' => "oauth"
                ]);

                try {
                    // Connect to the IMAP Server
                    if ($client->connect())
                        // echo "Connectied success";
                    $folder = $client->getFolder('INBOX');
                    $today = date('Y-m-d');
                    $subractedDay = date('d.m.Y', strtotime($today . ' - 1 days'));
                    $messages = $folder->query()->since($subractedDay)->unseen()->get();

                    return $messages;

                } catch (\Exception $e) {
                    echo 'Exception : ',  $e->getMessage(), "\n";
                }

            } else {
                echo ('Error : ' . $array_php_resul["error_description"]);
            }
        } else {
            echo curl_errno($ch);
        }

        return false;
    }
    public function supportEmail()
    {
        Log::info(date('Y-m-d H:i:s'). 'CRON Job Support Email Running');
        // exit;

        $supportEmail = 'support@tekroi.com';
        $emails = $this->getOutlookMails();

        if (!$emails) return 'No Email';

        foreach ($emails as $email)
        {
            $to_address = $cc_email_address = [];
            $subject = !empty($email->getSubject()) ? $email->getSubject() : "No Subject";
            $fromAddress = $email->getFrom()[0]->mail;
            $fromName = $email->getFrom()[0]->full;

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
            if (strtolower($fromAddress) == $supportEmail)
                continue; 

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
                $domain = $email->getFrom()[0]->host;
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
                        // Send Mail details to default Email
                        $user_email = 'info@tekroi.com';
                        $user_name = 'Tekroi Info';
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

    function getTicketId($subject)
    {
        if (preg_match('/^Re:/i', trim($subject))) {
            preg_match('#\[Ticket \# (.*?)\]#', $subject, $match);
            if ($match) return $match[1];
        } else
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

        $support_email = 'support@tekroi.com';
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
