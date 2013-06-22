<?php
    global $mail;

    $mail->subject = 'Tableau de contacts';

    $mail->body = 'Bonjour '.$mail->data['geckos'].'<br>
<br>
ci-joint le tableau de contacts sélectionnés, avec les informations suivantes:<br>
'.$mail->data['infos'].'<br>
<br>';

?>
