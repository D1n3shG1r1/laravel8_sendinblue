<?php
/*
    custom config
    mailbox credentials

    Run following command after any changes in this file
    php artisan config:cache
    kishan.singh@kitlabs.in
    KitKi$@$in12
*/
return array(
    "MAILBOX" => array(


    "GODADDY" => array("HOST" => "{imap.secureserver.net:993/imap/ssl}INBOX", "USERNAME" => "info@dkgiri.in", "PASSWORD" => "D1n3shG1r1"),

    "GMAIL" => array("HOST" => "{imap.gmail.com:993/imap/ssl}INBOX", "USERNAME" => "giridineshkumar85@gmail.com", "PASSWORD" => "xblchgzblhbgcfbs"),

    /*"OUTLOOK" => array("HOST" => "{outlook.office365.com:993/imap/ssl}INBOX", "USERNAME" => "kishan.singh@kitlabs.in", "PASSWORD" => 'KitKi$@$in12'),*/

    "OUTLOOK" => array("HOST" => "{outlook.office365.com:993/imap/ssl}INBOX", "USERNAME" => "giridineshkumar85@outlook.com", "PASSWORD" => 'D1n3shG1r1')

    )

);

?>
