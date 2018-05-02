<?php
    global $mail;

    $mail->subject = _G('DBMF_autosubs_notifysubj');

    $mail->body = 'Bonjour,<br><br>
Vous vous êtes inscrit avec les informations suivantes:<br>
'.$mail->data['body'].'<br>
<br>
merci.';

?>
