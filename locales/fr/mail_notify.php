<?php
    global $mail, $_SERVER;

    if( $mail->data['notifs_new']!=0 )
        $mail->subject = $mail->data['notifs_new'].' nouveau(x) mémento(s) actif(s)';
    elseif( $mail->data['notifs']!=0 )
        $mail->subject = $mail->data['notifs'].' mémento(s) actif(s)';

    $mail->body = 'Bonjour '.$mail->data['geckos'].',<br>
<br>
vous avez:<br>
';
    if( $mail->data['notifs_new']!=0 )
        $mail->body .= '
'.$mail->data['notifs_new'].' nouveau(x) mémento(s) actif(s)<br>
sur ';
    $mail->body .= ''.$mail->data['notifs'].' mémento(s) actif(s)<br>
<br>
';

?>
