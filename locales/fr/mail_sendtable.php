<?php
    global $mail;

    $mail->subject = 'Tableau de contacts';

    $mail->body = 'Bonjour '.$mail->geckos.'

ci-joint le tableau de contacts sélectionnés, avec les informations suivantes:
'.$mail->data['infos'].'
';

?>
