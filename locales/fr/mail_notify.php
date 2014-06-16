<?php
    global $mail, $_SERVER;

    if( $mail->data['notifs_new']!=0 ) 
        $mail->subject = $mail->data['notifs_new'].' nouveaux mémentos actifs';
    elseif( $mail->data['notifs']!=0 ) 
        $mail->subject = $mail->data['notifs'].' mémentos actifs';

    $mail->body = 'Bonjour '.$mail->data['geckos'].',<br>
<br>
vous avez:<br>
';
    if( $mail->data['notifs_new']!=0 ) 
        $mail->body .= '
'.$mail->data['notifs_new'].' nouveaux mémentos actifs<br>
sur ';
    $mail->body .= ''.$mail->data['notifs'].' mémentos actifs<br>
<br>
';

?>
