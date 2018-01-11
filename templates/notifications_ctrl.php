<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if(!MySBRoleHelper::checkAccess('dbmf_user')) return;

$sql_mementos = "SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
    "ORDER BY type,monthofyear_memento,date_memento,date_process";
$req_mementos = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
    "ORDER BY type,monthofyear_memento,date_memento,date_process",
    "templates/notifications.php",
    true, 'dbmf3' );
$mementos = array();
while($data_memento = MySBDB::fetch_array($req_mementos)) {
    $mementos[$data_memento['id']] = new MySBDBMFMemento(null, $data_memento);
}
$act_mementos = array();
foreach($mementos as $memento) {
    if($memento->isActive())
        $act_mementos[] = $memento;
}

//echo "count: ".count($mementos)." mementos\n\n";
//echo "count: ".count($act_mementos)." actives mementos\n\n";

$req_users = MySBDB::query('SELECT * FROM '.MySB_DBPREFIX."users ".
    "WHERE id!=0",
    "templates/notifications.php", 'dbmf3' );
while( $data_user=MySBDB::fetch_array($req_users) ) {

    $user = new MySBUser(-1,$data_user);
    //echo "\nUser ".$user->login.":\n";
    $active_nb = 0;
    $active_nb_new = 0;
    foreach( $act_mementos as $memento ) {
        if( $memento->isTreatable($user) ) {
            $active_nb += 1;
            if( $memento->is_notified!=1 )
                $active_nb_new += 1;
        }
    }
    //echo $active_nb.' actives mementos (whose '.$active_nb_new." are new)\n";

    if( $active_nb_new!=0 ) {
        $notif_mail = new MySBMail('notify','dbmf3');
        $notif_mail->addTO($user->mail,$user->lastname.' '.$user->firstname);
        $notif_mail->data['geckos'] = $user->firstname.' '.$user->lastname;
        $notif_mail->data['notifs'] = $active_nb;
        $notif_mail->data['notifs_new'] = $active_nb_new;
        $notif_mail->send();
    } elseif( $active_nb!=0 and date("N")==1 ) {
        $notif_mail = new MySBMail('notify','dbmf3');
        $notif_mail->addTO($user->mail,$user->lastname.' '.$user->firstname);
        $notif_mail->data['geckos'] = $user->firstname.' '.$user->lastname;
        $notif_mail->data['notifs'] = $active_nb;
        $notif_mail->data['notifs_new'] = $active_nb_new;
        $notif_mail->send();
    }

}

foreach( $act_mementos as $memento ) {
    if( $memento->is_notified!=1 )
        $memento->update( array(
            'is_notified' => 1 ));
}
?>
