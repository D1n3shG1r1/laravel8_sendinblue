<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Home extends Controller
{
    //

    function sendinblueEmial(){
        Log::info("Cron is working fine test!");
        echo "Cron is working fine test!";

        die;
        //dev.scip.co
        //http://local.laravelsendinblue.com/sendinblueemial
        //echo "hello";
        //https://www.itsolutionstuff.com/post/laravel-8-mail-laravel-8-send-email-tutorialexample.html
        /*
        $details = [
            'title' => 'Mail from ItSolutionStuff.com',
            'body' => 'This is for testing email using smtp'
        ];

        $out = Mail::to('upkit.dineshgiri@gmail.com')->send(new \App\Mail\MyTestMail($details));

        //dd("Email is Sent.");
        echo "$out:<pre>"; print_r($out);
        */
        //dineshleadtech@gmail.com (Registered)
        //Send Email curl
        //$senderEmail = "dineshleadtech132@gmail.com";

        $apikey = "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";

        //$apikey = "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";

        //$apikey = "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"; //smriti

        /*
        [17:58, 22/05/2023] Swapnil SCIP: email.scipmail.com
        [17:58, 22/05/2023] Swapnil SCIP: username: info@scipmail.com
        [17:58, 22/05/2023] Swapnil SCIP: password; info2Way!!
        */



        $senderEmail = "dineshleadtech@gmail.com";
        //$senderEmail = "kirtidkgiri@gmail.com";
        //$senderEmail = "smriti@scip.co";
        //$senderEmail = "info@dkgiri.in";
        $senderName = "dineshld";

        //$replyToEmail = "info@scipmail.com";
        $replyToEmail = "info@dkgiri.in";
        $replyToName = "Dinesh Kumar";

        $toEmail = "giridineshkumar85@gmail.com";
        //$toEmail = "smriti@scip.co";
        //$toEmail = "omnakra@gmail.com";
        //scip.co
        $toName = "dineshkg85";
        $subject = "Test sendinblue goodluck";
        $message = "<html><head></head><body><p>Hello,</p>This is test email.</p></body></html>";

        $cmd = "curl --request POST --url https://api.brevo.com/v3/smtp/email --header 'accept: application/json' --header 'api-key:$apikey' --header 'content-type: application/json' --data '{\"sender\":{\"name\":\"$senderName\",\"email\":\"$senderEmail\"},\"replyTo\":{\"name\":\"$replyToName\",\"email\":\"$replyToEmail\"},\"to\":[{\"email\":\"$toEmail\",\"name\":\"$toName\"}],\"subject\":\"$subject\",\"htmlContent\":\"$message\"}'";

        echo $cmd;
        exec($cmd, $out);
        dd($out);
       die;


        //Create inboundprocess webhook
        //https://developers.brevo.com/docs/inbound-parse-webhooks
        //$url = "https://emailreceiver.scip.co/parsedwebhook";
        //$url = "https://emailreceiver.scip.co/index.php";
        $url = "https://emailreceiver.scip.co/sbparsewebhook.php";
        //$replyTo = "emailreceiver.scip.co";
        //$replyTo = "reply.scip.co";
        //scipmail.com
        //$replyTo = "scipmail.com";
        $replyTo = "dkgiri.in"; //email.dkgiri.in info@dkgiri.in D1n3shG1r1
        // do you have any email like *@reply.scip.co
        $cmd = "curl --request POST --url https://api.sendinblue.com/v3/webhooks --header 'accept: application/json' --header 'api-key:$apikey' --header 'content-type: application/json' --data '{
            \"type\": \"inbound\",
            \"events\": [\"inboundEmailProcessed\"],
            \"url\":\"$url\",
            \"domain\": \"$replyTo\",
            \"description\":\"Webhook to receive replies\"
         }'";


        echo $cmd;
        exec($cmd, $out);
        dd($out);

        //get webhook details
        $cmd = "curl --request GET \
                --url https://api.brevo.com/v3/webhooks/805387 \
                --header 'accept: application/json' \
                --header 'api-key:$apikey'";
        //echo $cmd;
        //exec($cmd, $out);
        //dd($out);

        }

    function getEmailReports(){
        //https://whatsapp.scip.co/sendinblue/inbound.php
        /*
        array:1 [▼
            0 => "{"messageId":"<202305110534.61060994606@smtp-relay.mailin.fr>"}"
            ]
        */
    }




}
