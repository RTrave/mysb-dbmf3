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


if(isset($_POST['dbmf_contact_delete'])) {

    echo '
<script>
    desactiveOverlay();
    $("#contact'.$_POST['dbmf_contact_delete'].'").fadeOut(1000,"swing");';

    foreach($app->dbmf_hidemementos as $memento) {
        echo '
    $("#memento'.$memento->id.'").fadeOut(1000,"swing");';
    }

    echo '
</script>';
}
?>
