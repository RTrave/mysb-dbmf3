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

if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

if(isset($_GET['contact_duplicate'])) {
  echo '
<script>
desactiveOverlay();';
  $contact_orig = new MySBDBMFContact($_GET['contact_duplicate']);
  $contact_dup = $contact_orig->duplicate();
  //echo 'console.log("'.$contact_orig->id.'");';
  $app->pushMessage(_G('DBMF_contact_duplicated').'<br>ID='.$contact_dup->id);
  echo '
loadItem("contacts_results","index.php?mod=dbmf3&inc=contacts_sort&sid='.$contact_dup->id.'");
</script>';
}

if(isset($_GET['callback'])) {
    echo '
<script>
loadItem("'.$_GET['callback_inc'].$_GET['callback_dat'].'",'.
  '"index.php?mod='.$_GET['callback'].
  '&inc='.$_GET['callback_inc'].
  '&id='.$_GET['callback_dat'].'");
</script>';
}

?>
