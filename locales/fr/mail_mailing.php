<?php
    global $mail;

    $mail->subject = $mail->data['subject'];

    $mail->body = '
'.$mail->data['body'].'
';

?>
