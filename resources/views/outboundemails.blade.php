<!-- resources/views/outboundemails.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Emails</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <style>
           .table {font-size: 12px;}
        </style>

    </head>
    <body>
        <div class="row">
            <h5>Sent Emails:</h5>
        </div>
        <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Subject</th>
                <th scope="col">To</th>
                <th scope="col">From</th>
                <th scope="col">Reply To</th>
                <th scope="col">Message</th>
                <th scope="col">Attachments</th>
                <th scope="col">Replies</th>
                <th scope="col">Sent Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($sentEmails as $k => $emailRw) {

                    $tmp_Id = $emailRw->id;
                    $tmp_messageId = $emailRw->messageId;
                    $tmp_uuid = $emailRw->uuid;
                    $tmp_sentDate = $emailRw->sentDate;
                    $tmp_subject = $emailRw->subject;
                    $tmp_message = $emailRw->message;
                    $tmp_toEmail = $emailRw->toEmail;
                    $tmp_fromEmail = $emailRw->fromEmail;
                    $tmp_replyTo = $emailRw->replyTo;
                    $tmp_attachments = $emailRw->attachments;
                    $tmp_createDateTime = $emailRw->created_at;
                    $tmp_replies = $emailRw->replies;

                    $tmp_toEmailStr = "";
                    $tmp_toEmailArr = json_decode($tmp_toEmail);
                    foreach ($tmp_toEmailArr as $k1 => $v1) {
                        if($k1 == 0){
                            $tmp_toEmailStr .= $v1->name ." [". $v1->email."]";
                        }else{
                            $tmp_toEmailStr .=  ", ". $v1->name ." [". $v1->email."]";
                        }

                    }


                    $tmp_fromEmailStr = "";
                    $tmp_fromEmailArr = json_decode($tmp_fromEmail);
                    foreach ($tmp_fromEmailArr as $k2 => $v2) {
                        if($k2 == 0){
                            $tmp_fromEmailStr .= $v2->name ." [". $v2->email."]";
                        }else{
                            $tmp_fromEmailStr .=  ", ". $v2->name ." [". $v2->email."]";
                        }

                    }


                    $tmp_replyToStr = "";
                    $tmp_replyToArr = json_decode($tmp_replyTo);
                    foreach ($tmp_replyToArr as $k3 => $v3) {
                        if($k3 == 0){
                            $tmp_replyToStr .= $v3->name ." [". $v3->email."]";
                        }else{
                            $tmp_replyToStr .=  ", ". $v3->name ." [". $v3->email."]";
                        }

                    }
              ?>

<tr id="row-<?php echo $tmp_Id; ?>" class="row-<?php echo $tmp_Id; ?>">
    <th scope="row"><?php echo $k + 1;?></th>
    <td><?php echo $tmp_subject; ?></td>
    <td><?php echo $tmp_toEmailStr; ?></td>
    <td><?php echo $tmp_fromEmailStr; ?></td>
    <td><?php echo $tmp_replyToStr; ?></td>
    <td><?php echo $tmp_message; ?></td>
    <td><?php echo $tmp_attachments; ?></td>
    <td><?php
    if(count($tmp_replies) > 0){
        echo '<a href="'.url("/replies?uuid=$tmp_uuid&messageId=$tmp_messageId").'" target="__blank">'. count($tmp_replies) . ' Replies</a>';
    }else{
        echo '<a href="javascript:void(0);">'. count($tmp_replies) . ' Replies</a>';
    }


    ?></td>
    <td><?php echo date("D ,d-M-Y H:i:s", strtotime($tmp_sentDate)); ?></td>
  </tr>

              <?php
                }
             ?>
            </tbody>
          </table>


    </body>
</html>
