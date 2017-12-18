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


define('MODULO_DEFAULT',50);
define('MAXBYSEND_DEFAULT',150);

/**
 * DBMF Mailing Export class
 * 
 */
class MySBDBMFExportMailing extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }

    public function htmlConfigForm() {
        global $app;
        if($this->config_array['modulo']!='') $modulo = $this->config_array['modulo'];
        else $modulo = MODULO_DEFAULT;
        $str_res = '
'._G('DBMF_exportmailing_config_modulo').': 
    <input type="text" name="dbmf_exportmailing_config_modulo" value="'.$modulo.'"><br>';
        if($this->config_array['maxbysend']!='') $maxbysend = $this->config_array['maxbysend'];
        else $maxbysend = MAXBYSEND_DEFAULT;
        $str_res .= '
'._G('DBMF_exportmailing_config_maxbysend').': 
    <input type="text" name="dbmf_exportmailing_config_maxbysend" value="'.$maxbysend.'"><br>';
        return $str_res;
    }

    public function htmlConfigProcess() {
        global $app;
        global $_POST;
        $str_res = 
            'modulo='.$_POST['dbmf_exportmailing_config_modulo'].';'.
            'maxbysend='.$_POST['dbmf_exportmailing_config_maxbysend'].';';
        return $str_res;
    }

    public function selectionProcess( $selection ) {
        
    }

    public function htmlParamForm() {
        global $app;
        $editor = new MySBEditor();
        $output = $editor->init("exportmailing_body");
        $output .= '
<p>
    '._G('DBMF_exportmailing_subject').':
    <input type="text" name="dbmf_exportmailing_subject" value="" size="24"><br>
    '._G('DBMF_exportmailing_body').':<br>
    <div style="max-width: 640px"><textarea name="dbmf_exportmailing_body" cols="60" rows="8" class="mceEditor" id="exportmailing_body"></textarea></div>
'.$editor->active("exportmailing_body").'
    '._G('DBMF_exportmailing_attachment').' 1:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att1" type="file" /><br>
    '._G('DBMF_exportmailing_attachment').' 2:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att2" type="file" /><br>
    '._G('DBMF_exportmailing_attachment').' 3:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att3" type="file" />
</p>';
        $output .= '
<h4>'._G('DBMF_exportmailing_advancedparams').'</h4>
<p>
    '._G('DBMF_exportmailing_sendaslist').':
    <input type="checkbox" name="dbmf_exportmailing_sendaslist"><br>
    '._G('DBMF_exportmailing_unsubscribefields').':
    <input type="checkbox" name="dbmf_exportmailing_unsubscribefields"><br>
    '._G('DBMF_exportmailing_replyto').':
    <select name="dbmf_exportmailing_replyto">
        <option value="base">'._G('DBMF_exportmailing_replyto_base').'</option>
        <option value="tech">'.MySBConfigHelper::Value('website_name').' ('.MySBConfigHelper::Value('technical_contact').')</option>
        <option value="self">'.$app->auth_user->lastname.' '.$app->auth_user->firstname.' ('.$app->auth_user->mail.')</option>
    </select><br>
    '._G('DBMF_exportmailing_firstid').':
    <input type="text" name="dbmf_exportmailing_firstid" value="" size="8">
</p>
    ';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        if( $_POST['dbmf_exportmailing_replyto']=='tech' ) {
            $this->replyto_addr = MySBConfigHelper::Value('technical_contact');
            $this->replyto_geck = MySBConfigHelper::Value('website_name');
        } elseif( $_POST['dbmf_exportmailing_replyto']=='self' ) {
            $this->replyto_addr = $app->auth_user->mail;
            $this->replyto_geck = $app->auth_user->lastname.' '.$app->auth_user->firstname;
        } else {
            $this->replyto_addr = MySBConfigHelper::Value('technical_contact');
            $this->replyto_geck = '';
        }
        if( isset($_POST['dbmf_exportmailing_sendaslist']) and $_POST['dbmf_exportmailing_sendaslist']!='' ) 
            $this->mailing_sendaslist = true;
        else $this->mailing_sendaslist = false;
        if( isset($_POST['dbmf_exportmailing_unsubscribefields']) and $_POST['dbmf_exportmailing_unsubscribefields']!='' ) 
            $this->mailing_unsubscribefields = true;
        else $this->mailing_unsubscribefields = false;
        $this->mailing_firstid = $_POST['dbmf_exportmailing_firstid'];
        $this->mailing_subject = $_POST['dbmf_exportmailing_subject'];
        $this->mailing_body = $_POST['dbmf_exportmailing_body'];
        $uploaddir = MySB_ROOTPATH.'/modules/dbmf3/files/';
        if( !empty($_FILES['dbmf_exportmailing_att1']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att1']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att1']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att1']['name']);
            $this->mailing_att1 = $uploaddir.$_FILES['dbmf_exportmailing_att1']['name'];
        }
        else $this->mailing_att1 = "";
        if( !empty($_FILES['dbmf_exportmailing_att2']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att2']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att2']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att2']['name']);
            $this->mailing_att2 = $uploaddir.$_FILES['dbmf_exportmailing_att2']['name'];
        }
        else $this->mailing_att2 = "";
        if( !empty($_FILES['dbmf_exportmailing_att3']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att3']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att3']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att3']['name']);
            $this->mailing_att3 = $uploaddir.$_FILES['dbmf_exportmailing_att3']['name'];
        }
        else $this->mailing_att3 = "";
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput() {
        global $app;

        if( $this->mailing_subject=='' or $this->mailing_body=='' ) {
            echo '<p>'._G('DBMF_exportmailing_emptyfield').'</p>';
            return;
        }

        if( $this->config_array['modulo']!='' ) $modulo = $this->config_array['modulo'];
        else $modulo = MODULO_DEFAULT;
        if( $this->config_array['maxbysend']!='' ) $maxbysend = $this->config_array['maxbysend'];
        else $maxbysend = MAXBYSEND_DEFAULT;

        $sql_all =  'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts WHERE '.$_SESSION['dbmf_query_where'].
            ' ORDER by id';
        $results = MySBDB::query( $sql_all,
            "MySBDBMFExportCSV::htmlResultOutput()",
            false, 'dbmf3');

        $output = '
<p>
'.MySBDB::num_rows($results).' contacts<br>';
        if( $this->mailing_sendaslist )
            $output .= '(send as list)';
        $output .= '
</p>
<h3>'._G('DBMF_exportmailing_sending').'</h3>
<div id="rsvp_mailing_displaymail">
<p><b>'.$this->mailing_subject.'</b></p>
<br>
'.MySBUtil::str2html($this->mailing_body).'
<br>
</div>
<p>';

        $current_mail = new MySBMail('mailing','dbmf3');
        if( $this->replyto_geck!='' ) $current_mail->setReplyTo($this->replyto_addr,$this->replyto_geck);
        if( $this->mailing_att1!='' ) $current_mail->addAttachment($this->mailing_att1);
        if( $this->mailing_att2!='' ) $current_mail->addAttachment($this->mailing_att2);
        if( $this->mailing_att3!='' ) $current_mail->addAttachment($this->mailing_att3);
        if( $this->mailing_unsubscribefields ) {
            //$current_mail->addHeader('List-Unsubscribe: <mailto:'.$this->replyto_addr.'?subject=Unsubscribe>');
            $current_mail->addFooter('
<p></p>
<p style="text-align: center; background-color: #cccccc;"><small>'._G('DBMF_exportmailing_unsubscribe').
': <a href="mailto:'.$this->replyto_addr.'?subject=Unsubscribe">'.
$this->replyto_addr.'?subject=Unsubscribe</a></small></p>');
        }
        $current_mail->data['body'] = $this->mailing_body;
        $current_mail->data['subject'] = $this->mailing_subject;

        if( $this->mailing_firstid!='' ) $recup_flag = true;
        else $recup_flag = false;

        $this->bad_adresses = array();
        $this->mailing_flag = true;
        $mails_index = 0;
        $this->firstid = null;
        $this->count = 0;

        while($this->mailing_flag) {

            $modulo_index = 0;
            $this->mailinglist = array();
            while(  $data_result=MySBDB::fetch_array($results) ) {
                $contact = new MySBDBMFContact(null,$data_result);
                if( $recup_flag==true and $contact->id!=$this->mailing_firstid )
                    continue;
                if( $recup_flag==true and $contact->id==$this->mailing_firstid )
                    $recup_flag = false;
                if( $contact->mail!='' ) {
                    $multiaddress = explode( ',', $contact->mail );
                    foreach( $multiaddress as $singaddress )
                        $this->mailinglist[$singaddress] = $contact;
                    if( $this->firstid==null )
                        $this->firstid = $contact->id;
                    $mails_index++;
                    $modulo_index++;
                } else {
                    $output .=  '<small>'._G('DBMF_exportmailing_sendingnomail').': '.
                    $contact->firstname.' '.$contact->lastname.' (id:'.$contact->id.')</small><br>';
                }
                if( $mails_index>=$maxbysend or
                    $modulo_index>=$modulo )
                    break;
            }
            if( $mails_index==$maxbysend or !$data_result )
                $this->mailing_flag = false;

            $output .= $this->sendMail($current_mail);

        }

        $current_mail->close();
        
        $output .= '
'._G('DBMF_exportmailing_nbmail').': '.($mails_index).'<br>
</p>';

        if( $mails_index==$maxbysend and ($data_result=MySBDB::fetch_array($results)) ) {
            $contact = new MySBDBMFContact(null,$data_result);
            $output .= _G('DBMF_exportmailing_nextid').': '.$contact->id.'<br>';
            $rescue_mail = new MySBMail('blank');
            $rescue_mail->addTO($app->auth_user->mail,$app->auth_user->lastname.' '.$app->auth_user->firstname);
            $rescue_mail->data['body'] = _G('DBMF_exportmailing_nextid').': <b>'.$contact->id.'</b><br><br>';
            $rescue_mail->data['subject'] = 'Next ID: '.$contact->id.' / '.$this->mailing_subject;
            $rescue_mail->send();
        }

        if( $this->firstid!=null ) {
            $output .= _G('DBMF_exportmailing_errorid').': '.$this->firstid.'<br>';
            $rescue_mail = new MySBMail('blank');
            $rescue_mail->addTO($app->auth_user->mail,$app->auth_user->lastname.' '.$app->auth_user->firstname);
            $rescue_mail->data['body'] = _G('DBMF_exportmailing_errorid').': <b>'.$this->firstid.'</b><br><br>';
            $rescue_mail->data['subject'] = 'Rescue ID: '.$this->firstid.' / '.$this->mailing_subject;
            $rescue_mail->send();
        }

        if($this->mailing_att1!='') unlink($this->mailing_att1);
        if($this->mailing_att2!='') unlink($this->mailing_att2);
        if($this->mailing_att3!='') unlink($this->mailing_att3);
        return $output;
    }

    public function sendMail($smail) {
        global $app;
        $this->count++;
        $output = _G('DBMF_exportmailing_sendingnew')." ".$this->count."<br>";
        $mail_sent = false;
        $this->previous_error = '';
        while( !$mail_sent ) {
            $tmp_mail = clone $smail;
            $tmp_mail->clearRecipients();
            foreach( $this->mailinglist as $address=>$contact ) 
                if( $contact!=null ) {
                    $tmp_mail->addBCC( $address, $contact->firstname.' '.$contact->lastname );
                }
            if( $this->mailing_sendaslist ) {
                $tmp_mail->sendBCCIndividually();
                $mail_sent = true;
            } else {
                if( !$tmp_mail->send(false) ) {
                    $output .= '<samp>'.$tmp_mail->getError().'</samp>';
                    if( !$this->checkMailError($tmp_mail->getError()) ) {
                        $output .= 'Last ID tried: <b>'.$this->firstid.'</b></p>';
                        return $output;
                    } else {
                        foreach($this->bad_adresses as $badaddress)
                            if( isset($this->mailinglist[$badaddress]) ) {
                                $output .= '<small><i>'.$badaddress.' removed!</i></small><br>';
                                if( $this->mailinglist[$badaddress]!=null ) {
                                    $this->mailinglist[$badaddress]->addMementoSimple(
                                            'bad address: '.$this->mailinglist[$badaddress]->mail.'<br>'.
                                            $tmp_mail->getError() );
                                    $this->mailinglist[$badaddress]->update( array('mail' => '' ) );
                                }
                                $this->mailinglist[$badaddress] = null;
                            }
                    }
                } else $mail_sent = true;
            }
        }
        $this->firstid = null;
        return $output;
    }

    public function checkMailError($error) {
        global $app;
        if( $this->previous_error==$error )
            return false;
        $this->previous_error = $error;
        $check = false;
        $badmails = MySBUtil::extractEmails($error);
        foreach($badmails as $badmail) {
            if( $badmail!=$app->auth_user->mail and $badmail!=MySBConfigHelper::Value('technical_contact') ) {
                $this->bad_adresses[$badmail] = $badmail;
                $check = true;
            }
        }
        return $check;
    }

}

?>
