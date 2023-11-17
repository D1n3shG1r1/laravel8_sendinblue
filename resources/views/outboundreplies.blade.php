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

           .table .attachmentLinks {
                float: left;
                width: auto;
                margin: 2px;
            }
        </style>

    </head>
    <body>
       <div class="row">
            <h5>Replies:</h5>
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
                <th scope="col">Sent Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($replies as $k => $emailRw) {

                    $tmp_Id = $emailRw->id;
                    $tmp_date = $emailRw->date;
                    $tmp_subject = $emailRw->subject;
                    $tmp_emailTo = $emailRw->emailTo;
                    $tmp_emailFrom = $emailRw->emailFrom;
                    $tmp_replyTo = $emailRw->replyTo;
                    $tmp_message = $emailRw->message;
                    $tmp_attachments = $emailRw->attachments;


                    $tmp_toEmailStr = "";
                    $tmp_toEmailArr = json_decode($tmp_emailTo);
                    foreach ($tmp_toEmailArr as $k1 => $v1) {
                        if($k1 == 0){
                            $tmp_toEmailStr .= $v1->name ." [". $v1->email."]";
                        }else{
                            $tmp_toEmailStr .=  ", ". $v1->name ." [". $v1->email."]";
                        }

                    }


                    $tmp_fromEmailStr = "";
                    $tmp_fromEmailArr = json_decode($tmp_emailFrom);
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

                    $tmp_attachmentStr = "";
                    if($tmp_attachments != ""){
                        $tmp_attachmentArr = json_decode($tmp_attachments);
                        foreach ($tmp_attachmentArr as $k4 => $v4) {

                            $urlParts = explode("/", $v4->url);
                            $attchmntNm = end($urlParts);

                            if($k4 == 0){
                                $tmp_attachmentStr .= '<a class="attachmentLinks" href="'.url("/storage/attachments/$tmp_Id/$attchmntNm").'" target="_blank">'.$v4->name.'</a>';
                            }else{
                                $tmp_attachmentStr .= ', <a class="attachmentLinks" href="'.url("/storage/attachments/$tmp_Id/$attchmntNm").'" target="_blank">'.$v4->name.'</a>';

                            }
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
    <td><?php echo $tmp_attachmentStr; ?></td>
    <td><?php echo date("D ,d-M-Y H:i:s", strtotime($tmp_date)); ?></td>
  </tr>

              <?php
                }
             ?>
            </tbody>
          </table>


    </body>
</html>
