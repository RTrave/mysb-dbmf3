<?php
    global $mail;

    $mail->subject = 'Table of contacts';

    $mail->body = 'Hi '.$mail->geckos.'<br>
<br>
here is the contacts table, with following informations:<br>
'.$mail->data['infos'].'<br>
<br>';

?>
