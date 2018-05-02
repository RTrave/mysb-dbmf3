<?php
    global $mail;

    $mail->subject = _G('DBMF_autosubs_notifysubj');

    $mail->body = 'Hi,<br><br>
You have been registred as contact:<br>
'.$mail->data['body'].'<br>
<br>
thanks.';

?>
