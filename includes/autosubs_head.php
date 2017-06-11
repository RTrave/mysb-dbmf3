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

if(!MySBRoleHelper::checkAccess('dbmf_autosubs')) return;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo MySBConfigHelper::Value('website_name'); ?></title>
    <meta name="viewport" content="initial-scale=1.0, width=device-width">
    <link rel="stylesheet" type="text/css" href="mysb.css" media="all">
    <link rel="stylesheet" type="text/css" href="mysbhandheld.css" media="(max-width: 520px)">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="images/favicon_32.png" type="image/x-icon" >

    <script src="jscripts/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="jscripts/spin.min.js" type="text/javascript"></script>
    <script src="jscripts/mysb.js" type="text/javascript"></script>
</head>

<body>
<noscript><div class="advert" style="background-color: #ffe4e7; border: 4px solid #ffab67; font-size: 24px;">Javascript needed but not activated.</div><br></noscript>

<div id="spinlayer">
</div>
<div id="overlayBg">
</div>
<script>
desactiveOverlay();
</script>

<div id="allshadow">

<div id="mysbMiddle" class="roundtop roundbottom">
<div class="content">

<div id="mysbMessages"></div>

<?php 
echo '
<h1>'.MySBConfigHelper::Value('website_name').'</h1>';
?>
