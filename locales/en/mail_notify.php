<?php
    global $mail, $_SERVER;

    if( $mail->data['notifs_new']!=0 ) 
        $mail->subject = $mail->data['notifs_new'].' new active mementos';
    elseif( $mail->data['notifs']!=0 ) 
        $mail->subject = $mail->data['notifs'].' active mementos';

    $mail->body = 'Hi '.$mail->data['geckos'].',<br>
<br>
you have:<br>
';
    if( $mail->data['notifs_new']!=0 ) 
        $mail->body .= '
'.$mail->data['notifs_new'].' new active mementos<br>
on ';
    $mail->body .= ''.$mail->data['notifs'].' active mementos<br>
<br>
';

?>
