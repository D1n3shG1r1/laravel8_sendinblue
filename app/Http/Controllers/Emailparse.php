<?php
/*
#GMail Block for using IMAP
https://support.google.com/mail/answer/7126229?hl=en#zippy=%2Ctoo-many-simultaneous-connections-error%2Ci-cant-sign-in-to-my-email-client

*/


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\inbound_model;
use App\Models\outbound_model;
use Illuminate\Support\Arr;

class Emailparse extends Controller
{

    var $SBCONSTANTS;
    var $MAILBOX;

    function __construct()
    {
        $this->SBCONSTANTS = config('sbconstants.sendinblueAPIKeys');
        $this->MAILBOX = config('mailbox.MAILBOX');

    }

    function sendEmail(){
        //http://local.laravelsendinblue.com/sendEmail
        $APIKEY = $this->SBCONSTANTS["APIKEY"];
        $SENDEREMAIL = $this->SBCONSTANTS["SENDEREMAIL"];
        $SENDERNAME = $this->SBCONSTANTS["SENDERNAME"];
        $REPLYTOEMAIL = $this->SBCONSTANTS["REPLYTOEMAIL"];
        $REPLYTONAME = $this->SBCONSTANTS["REPLYTONAME"];

        $SENDEREMAIL = "giridineshkumar85@gmail.com";
        $SENDERNAME = "Giri Dinesh Kumar";
        $REPLYTOEMAIL = "giridineshkumar85@gmail.com";
        $REPLYTONAME = "Giri Dinesh Kumar";

        $SENDEREMAIL = "giridineshkumar85@outlook.com";
        $SENDERNAME = "Giri Dinesh Kumar";
        $REPLYTOEMAIL = "giridineshkumar85@outlook.com";
        $REPLYTONAME = "Giri Dinesh Kumar";

        $toEmail = "info@dkgiri.in";
        $toName = "DK Giri";
        $subject = "Test Email";
        $message = "Hello this is a test email...".date('Y-m-d H:i:s'); // can be html/plain

        $cmd = "curl --request POST --url https://api.brevo.com/v3/smtp/email --header 'accept: application/json' --header 'api-key:$APIKEY' --header 'content-type: application/json' --data '{\"sender\":{\"name\":\"$SENDERNAME\",\"email\":\"$SENDEREMAIL\"},\"replyTo\":{\"name\":\"$REPLYTONAME\",\"email\":\"$REPLYTOEMAIL\"},\"to\":[{\"email\":\"$toEmail\",\"name\":\"$toName\"}],\"subject\":\"$subject\",\"htmlContent\":\"$message\"}'";

        exec($cmd, $out);
        //echo $cmd."<pre>"; print_r($out); die;
        
        if(!empty($out)){
            
            sleep(10);
            $response = json_decode($out[0]);
            if(property_exists($response, 'code') && $response->code == 'bad_request'){
                echo 'code:'.$response->code.', message:'.$response->message; die;
            }else{
                $messageId = $response->messageId;

                //get transactional email details
                $cmd = "curl --request GET --url 'https://api.brevo.com/v3/smtp/emails?messageId=$messageId&sort=desc&limit=1&offset=0' --header 'accept: application/json' --header 'api-key: $APIKEY'";
               // echo $cmd;
    
                exec($cmd, $emdout);
                if(!empty($emdout)){
                    //print_r($emdout);
                    $emailDetailsResp = json_decode($emdout[0]);
    
                    //print_r($emailDetailsResp);
    
                    $rowsCount = $emailDetailsResp->count;
                    $transactionalEmail = $emailDetailsResp->transactionalEmails[0];
                    //$transactionalEmailobj = json_decode($transactionalEmail);
                    $transactionalEmailobj = $transactionalEmail;
                    $messageId = $transactionalEmailobj->messageId;
                    $uuId = $transactionalEmailobj->uuid;
                    $sentDate = $transactionalEmailobj->date;
    
                    //save to outbox
                    $fromArr = array();
                    $fromArr[] = array("name" => $SENDERNAME, "email" => $SENDEREMAIL);
    
                    $toArr = array();
                    $toArr[] = array("name" => $toName, "email" => $toEmail);
    
                    $replyToArr = array();
                    $replyToArr[] = array("name" => $REPLYTONAME, "email" => $REPLYTOEMAIL);
    
                    $outBoxData = array();
                    $outBoxData["id"] = db_randnumber();
                    $outBoxData["messageId"] = $messageId;
                    $outBoxData["uuid"] = $uuId;
                    $outBoxData["sentDate"] = date("Y-m-d H:i:s", strtotime($sentDate));
                    $outBoxData["subject"] = $subject;
                    $outBoxData["message"] = $message;
                    $outBoxData["toEmail"] = json_encode($toArr);
                    $outBoxData["fromEmail"] = json_encode($fromArr);
                    $outBoxData["replyTo"] = json_encode($replyToArr);
                    $outBoxData["attachments"] = "";
                    $outBoxData["created_at"] = date("Y-m-d H:i:s");
    
                    $this->saveSentEmail($outBoxData);
                }
            }
           
        }

    }


    function saveSentEmail($data){

        $outbound = new outbound_model();

        $outbound->id = $data["id"];
        $outbound->messageId = $data["messageId"];
        $outbound->uuid = $data["uuid"];
        $outbound->sentDate = $data["sentDate"];
        $outbound->subject = $data["subject"];
        $outbound->message = $data["message"];
        $outbound->toEmail = $data["toEmail"];
        $outbound->fromEmail = $data["fromEmail"];
        $outbound->replyTo = $data["replyTo"];
        $outbound->attachments = $data["attachments"];
        $outbound->created_at = $data["created_at"];

        $result = $outbound->save();
        dd($result);

    }


    function parseinbound(){
       
        //echo "<pre>"; print_r($this->MAILBOX); die;

        $GODADDYMAILBOXCONFG = $this->MAILBOX["GODADDY"];
        $GOOGLEMAILBOXCONFG = $this->MAILBOX["GMAIL"];
        $OUTLOOKMAILBOXCONFG = $this->MAILBOX["OUTLOOK"];

        $mailBoxName = "GMAIL";

        $host = $GODADDYMAILBOXCONFG["HOST"];
        $user = $GODADDYMAILBOXCONFG["USERNAME"];
        $password = $GODADDYMAILBOXCONFG["PASSWORD"];

        $host = $GOOGLEMAILBOXCONFG["HOST"];
        $user = $GOOGLEMAILBOXCONFG["USERNAME"];
        $password = $GOOGLEMAILBOXCONFG["PASSWORD"];

        $host = $OUTLOOKMAILBOXCONFG["HOST"];
        $user = $OUTLOOKMAILBOXCONFG["USERNAME"];
        $password = $OUTLOOKMAILBOXCONFG["PASSWORD"];

        /* Establish a IMAP connection */
		$conn = imap_open($host, $user, $password)
        or die("unable to connect $mailBoxName: " . imap_last_error());


        $headers = imap_headers($conn);
        /*
        echo "<pre>";
        print_r($conn);
        print_r($headers);
        die;
        */
        //$since = date("D, d M Y", strtotime("-2 days"));
        $since = date("d M Y", strtotime("-2 days"));
        //$search = imap_search($conn, 'SINCE "'.$since.' 00:00:00 -0700 (PDT)"', SE_UID);
        $search = imap_search($conn, 'SINCE "'.$since.' 00:00:00"');

        $totalEmailCount = imap_num_msg($conn);

        //echo "search:<pre>"; print_r($search); die;

        if(!empty($search)){

            $firstId = min($search);
            $lastId = max($search);

            //echo "<PRE>";
            
            $inboundArr = array();
            $mails = array();
            $totalEmailCount = imap_num_msg($conn);
            $c = 0;
            //for($i = $totalEmailCount; $i > 0; $i--){

            for($i = $lastId; $i >= $firstId; $i--){

                $tmpId = db_randnumber();

                $emailFetchOverview = imap_fetch_overview($conn, $i);
                $emailFetchHeader = imap_fetchheader($conn, $i);
                $emailData = imap_headerinfo($conn, $i);
                $threadData = imap_thread($conn, $i);
                $body = imap_body($conn, $i);
                $fetchstructure = imap_fetchstructure($conn, $i);
                $fetchBody = imap_fetchbody($conn, $i,"1"); //1-2
                $bodyStrct = imap_bodystruct($conn, $i, "1"); //1-2

                //$finalMessage = trim(quoted_printable_decode($fetchBody));
                //echo $finalMessage; die;
                //print_r($headers);
                //echo $i;
                //print_r($body);

                //print_r($fetchstructure);
                //print_r($emailFetchOverview);
                //print_r($emailFetchHeader);
                //print_r($emailData);
                //print_r($threadData);
                //print_r($fetchBody);
                //print_r($bodyStrct);
                //die;

                $tmpMsgOffset = $i;
                $tmpDate = $emailData->date;
                $tmpSubject = $emailData->subject;
                $tmpMsgId = $emailData->message_id;
                $tmpRef = "";

                if(property_exists($emailData, "references")){
                    $tmpRef = $emailData->references;
                }

                $tmpinReplyTo = "";
                if(property_exists($emailData, "in_reply_to")){
                    $tmpinReplyTo = $emailData->in_reply_to;
                }

                $tmpMsgno = $emailData->Msgno;
                $toObj = $emailData->to;
                $fromObj = $emailData->from;


                if(property_exists($emailData, "reply_to")){
                    $replyToObj = $emailData->reply_to;
                }else{
                    $replyToObj = $emailData->from;
                }


                $tmpFromObj = array();
                $tmpToObj = array();
                $tmpReplyToObj = array();

                foreach($toObj as $toRw){

                    $tmpToName = "";
                    if(property_exists($toRw, "personal")){
                        $tmpToName = $toRw->personal;
                    }

                    $tmpToMailbox = $toRw->mailbox;
                    $tmpToHost = $toRw->host;
                    $tmpToEmail = $tmpToMailbox."@".$tmpToHost;

                    $tmpToObj[] = array(
                        "name" => $tmpToName,
                        "email" => $tmpToEmail
                    );

                }

                foreach($fromObj as $fromRw){

                    $tmpFromName = "";
                    if(property_exists($fromRw, "personal")){
                        $tmpFromName = $fromRw->personal;
                    }

                    $tmpFromMailbox = $fromRw->mailbox;
                    $tmpFromHost = $fromRw->host;
                    $tmpFromEmail = $tmpFromMailbox."@".$tmpFromHost;

                    $tmpFromObj[] = array(
                        "name" => $tmpFromName,
                        "email" => $tmpFromEmail
                    );

                }


                //need to resolve in outlook
                foreach($replyToObj as $replyToRw){

                    $tmpReplyToName = "";
                    if(property_exists($replyToRw, "personal")){
                        $tmpReplyToName = $replyToRw->personal;
                    }

                    $tmpReplyToMailbox = $replyToRw->mailbox;
                    $tmpReplyToHost = $replyToRw->host;
                    $tmpReplyToEmail = $tmpReplyToMailbox."@".$tmpReplyToHost;

                    $tmpReplyToObj[] = array(
                        "name" => $tmpReplyToName,
                        "email" => $tmpReplyToEmail
                    );

                }


                $mails[] = $i;


                // Check for attachments
                $structure = $fetchstructure;
                $attachments = array();
                $finalMessage = "";
               // print_r($structure);
               // die;

                if(isset($structure->parts) && count($structure->parts))
                {
                    for($j = 0; $j < count($structure->parts); $j++)
                    {
                        //echo "SubType:".$structure->parts[$j]->ifsubtype."&".$structure->parts[$j]->subtype."<br>";
                        $tmpContentArr = array("ALTERNATIVE", "PLAIN", "HTML");

                        if($structure->parts[$j]->ifsubtype && in_array($structure->parts[$j]->subtype, $tmpContentArr) && !$structure->parts[$j]->ifdparameters){
                            //need to resolve in outlook coming blank
                            $tmpFtchBdy = imap_fetchbody($conn, $i, $j+1);

                            // 3 = BASE64 encoding
                            if($structure->parts[$j]->encoding == 3)
                            {
                                $tmpFtchBdy = base64_decode($tmpFtchBdy);
                            }
                            // 4 = QUOTED-PRINTABLE encoding
                            elseif($structure->parts[$j]->encoding == 4)
                            {
                                $tmpFtchBdy = quoted_printable_decode($tmpFtchBdy);
                            }
                            // 1 = 8Bit
                            elseif($structure->parts[$j]->encoding == 1)
                            {
                                $tmpFtchBdy = imap_8bit($tmpFtchBdy);
                            }

                            else{
                                $tmpFtchBdy = imap_qprint($tmpFtchBdy);
                            }

                            $tmpFtchBdy = strip_tags($tmpFtchBdy);
                            $tmpFtchBdyParts = explode(">", $tmpFtchBdy);
                            $finalMessage = $tmpFtchBdyParts[0];
                        }

                    }
                }


                // if there is any attachment found...
                if(isset($structure->parts) && count($structure->parts))
                {
                    for($j = 0; $j < count($structure->parts); $j++)
                    {
                        $attachments[$j] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if($structure->parts[$j]->ifdparameters)
                        {
                            foreach($structure->parts[$j]->dparameters as $object)
                            {
                                if(strtolower($object->attribute) == 'filename')
                                {
                                    $attachments[$j]['is_attachment'] = true;
                                    $attachments[$j]['filename'] = $object->value;
                                }
                            }
                        }

                        if($structure->parts[$j]->ifparameters)
                        {
                            foreach($structure->parts[$j]->parameters as $object)
                            {
                                if(strtolower($object->attribute) == 'name')
                                {
                                    $attachments[$j]['is_attachment'] = true;
                                    $attachments[$j]['name'] = $object->value;
                                }
                            }
                        }

                        if($attachments[$j]['is_attachment'])
                        {
                            $attachments[$j]['attachment'] = imap_fetchbody($conn, $i, $j+1);

                            // 3 = BASE64 encoding
                            if($structure->parts[$j]->encoding == 3)
                            {
                                $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
                            }
                            // 4 = QUOTED-PRINTABLE encoding
                            elseif($structure->parts[$j]->encoding == 4)
                            {
                                $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
                            }
                        }
                    }
                }



                // iterate through each attachment and save it
                $path = storage_path('app/public/');
                $attachmentsPaths = array();
                if(!empty($attachments)){
                    foreach($attachments as $attachment)
                    {
                        if($attachment['is_attachment'] == 1)
                        {
                            $filename = $attachment['name'];
                            if(empty($filename)) $filename = $attachment['filename'];

                            if(empty($filename)) $filename = time() . ".dat";

                            $attchDir =  $path."/attachments";
                            $fileDir = $attchDir.'/'.$tmpId;

                            create_local_folder($attchDir);
                            create_local_folder($attchDir.'/'.$tmpId);

                            $filepath = $fileDir ."/". $i . "-" . $filename;
                            fileWrite($filepath,$attachment['attachment']);

                            //echo "filepath:".$filepath."<br>";
                            $attachmentsPaths[] = array(
                                "name" => $filename,
                                "url" => $filepath
                            );
                        }
                    }
                }

                $inboundArr[$c] = array(
                    "id" => $tmpId,
                    "date" => date("Y-m-d H:i:s", strtotime($tmpDate)),
                    "subject" => $tmpSubject,
                    "emailFrom" => json_encode($tmpFromObj),
                    "emailTo" => json_encode($tmpToObj),
                    "replyTo" => json_encode($tmpReplyToObj),
                    "message" => $finalMessage, //$tmpMessage,
                    "attachments" => json_encode($attachmentsPaths),
                    "messageId" => $tmpMsgId,
                    "references" => $tmpRef,
                    "inReplyTo" => $tmpinReplyTo,
                    "messageNumber" => $tmpMsgno,
                    "messageOffset" => $tmpMsgOffset,
                    "created_at" => date("Y-m-d H:i:s")

                );

                //die;
                //print_r($attachments);
                //die;
                $c++;
            }

            //echo "<pre>";
            //print_r($mails);
           //print_r($inboundArr); die;

            $this->saveInbox($inboundArr);
        }else{
            echo "no emails";
        }
        /* imap connection is closed */
		imap_close($conn);
    }


    function saveInbox($inboundArr){
        //save data to db
        $newRows = array();

        foreach($inboundArr as $inboundRw){

            $inbound = new inbound_model(); //inbound model obj

            $inbound->id = $inboundRw["id"];
            $inbound->date = $inboundRw["date"];
            $inbound->subject = $inboundRw["subject"];
            $inbound->emailTo = $inboundRw["emailTo"];
            $inbound->emailFrom = $inboundRw["emailFrom"];
            $inbound->replyTo = $inboundRw["replyTo"];
            $inbound->message = $inboundRw["message"];
            $inbound->attachments = $inboundRw["attachments"];
            $inbound->messageId = $inboundRw["messageId"];
            $inbound->references = $inboundRw["references"];
            $inbound->inReplyTo = $inboundRw["inReplyTo"];
            $inbound->messageNumber = $inboundRw["messageNumber"];
            $inbound->messageOffset = $inboundRw["messageOffset"];
            $inbound->created_at = $inboundRw["created_at"];
           
            $newRows[] = $inbound->attributesToArray();
        }

        if(!empty($newRows)){
            inbound_model::insert($newRows);
            echo "saved";
        }

    }

    function outbox(){
        //get sent emails and their replies
        //http://local.laravelsendinblue.com/outbox

        $offset = 0;
        $limit = 10;

        $sentItems = DB::table('sb_sent_emails')
            ->select('id', 'messageId', 'uuid', 'sentDate', 'subject', 'message', 'toEmail', 'fromEmail', 'replyTo', 'attachments', 'created_at')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('sentDate', 'desc')
            ->get()
            ->toArray();

        //echo "<pre>";
        //print_r($sentItems);

        if(!empty($sentItems)){
            $repliesArr = array();
            foreach($sentItems as $sentItem){
                $id = $sentItem->id;
                $uuid = $sentItem->uuid;
                $messageId = $sentItem->messageId;
                $replies = DB::table('sb_inbound_parsed_emails')
                ->select('*')
                ->where('references', 'LIKE', "%$uuid%")
                ->orWhere('references', 'LIKE', "%$messageId%")
                ->orderBy('date', 'desc')
                ->get()->toArray();

                $repliesArr[$id] = $replies;

            }

            //print_r($repliesArr);

            if(!empty($repliesArr)){
                foreach($sentItems as &$sentItem){
                    $id = $sentItem->id;
                    $replies = $repliesArr[$id];
                    $sentItem->replies = $replies;
                }
            }

            //print_r($sentItems);
            $data = array();
            $data["sentEmails"] = $sentItems;
            return view("outboundemails", $data);
        }
    }

    function replies(Request $request){

        $uuid = $request->input("uuid");
        $messageId = $request->input("messageId");

        $replies = DB::table('sb_inbound_parsed_emails')
                ->select('*')
                ->where('references', 'LIKE', "%$uuid%")
                ->orWhere('references', 'LIKE', "%$messageId%")
                ->orderBy('date', 'desc')
                ->get()->toArray();


        $data = array();
        $data["replies"] = $replies;
        //echo "<pre>"; print_r($data); die;
        return view("outboundreplies", $data);
    }

}
