<?php
    global $mail;

    $mail->subject = 'Table of contacts';

    $mail->body = 'Hi '.$mail->geckos.'

here is the contacts table, with following informations:
'.$mail->data['infos'].'
';

?>
