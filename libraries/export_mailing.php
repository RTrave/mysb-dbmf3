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
        MySBEditor::activate();
        $output = MySBEditor::initCode().'
<p>
    '._G('DBMF_exportmailing_subject').':
    <input type="text" name="dbmf_exportmailing_subject" value="" size="24"><br>
    '._G('DBMF_exportmailing_body').':<br>
    <textarea name="dbmf_exportmailing_body" cols="60" rows="8" class="mceEditor"></textarea><br>
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
        <option value="base">'._G("DBMF_exportmailing_replyto_base").'</option>
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
        if( $_POST['dbmf_exportmailing_sendaslist']!='' ) $this->mailing_sendaslist = true;
        else $this->mailing_sendaslist = false;
        if( $_POST['dbmf_exportmailing_unsubscribefields']!='' ) $this->mailing_unsubscribefields = true;
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
        if( !empty($_FILES['dbmf_exportmailing_att2']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att2']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att2']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att2']['name']);
            $this->mailing_att2 = $uploaddir.$_FILES['dbmf_exportmailing_att2']['name'];
        }
        if( !empty($_FILES['dbmf_exportmailing_att3']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att3']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att3']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att3']['name']);
            $this->mailing_att3 = $uploaddir.$_FILES['dbmf_exportmailing_att3']['name'];
        }
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;

        if( $this->mailing_subject=='' or $this->mailing_body=='' ) {
            echo '<p>'._G('DBMF_exportmailing_emptyfield').'</p>';
            return;
        }

        if( $this->config_array['modulo']!='' ) $modulo = $this->config_array['modulo'];
        else $modulo = MODULO_DEFAULT;
        if( $this->config_array['maxbysend']!='' ) $maxbysend = $this->config_array['maxbysend'];
        else $maxbysend = MAXBYSEND_DEFAULT;

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

        if( $this->mailing_firstid!='' ) $recup_flag = true;
        else $recup_flag = false;
        $modulo_index = 0;
        $mails_index = 0;
        $firstid = null;

        $current_mail = new MySBMail('mail_mailing','dbmf3');
        if( $this->replyto_geck!='' ) $current_mail->setReplyTo($this->replyto_addr,$this->replyto_geck);
        if( $this->mailing_att1!='' ) $current_mail->addAttachment($this->mailing_att1);
        if( $this->mailing_att2!='' ) $current_mail->addAttachment($this->mailing_att2);
        if( $this->mailing_att3!='' ) $current_mail->addAttachment($this->mailing_att3);
        if( $this->mailing_unsubscribefields ) {
            $current_mail->addHeader('List-Unsubscribe: <mailto:'.$this->replyto_addr.'?subject=Unsubscribe>');
            $current_mail->addFooter('
<p></p>
<p style="text-align: center; background-color: #cccccc;"><small>'._G('DBMF_exportmailing_unsubscribe').
': <a href="mailto:'.$this->replyto_addr.'?subject=Unsubscribe">'.
$this->replyto_addr.'?subject=Unsubscribe</a></small></p>');
        }
        $current_mail->data['body'] = $this->mailing_body;
        $current_mail->data['subject'] = $this->mailing_subject;

        while($mails_index<=$maxbysend) {

            if( !$data_result=MySBDB::fetch_array($results) ) {
                break;
            }
            $contact = new MySBDBMFContact(null,$data_result);
            if( $recup_flag==true and $contact->id!=$this->mailing_firstid )
                continue;
            if( $recup_flag==true and $contact->id==$this->mailing_firstid )
                $recup_flag = false;
            if( $modulo_index>=$modulo ) {
                $modulo_index = 0;
                if( $this->mailing_sendaslist ) {
                    $current_mail->sendBCCIndividually();
                } elseif( !$current_mail->send(false) ) {
                    $output .= '<samp>'.$current_mail->getError().'</samp>';
                    $output .= 'Last ID tried: <b>'.$firstid.'</b></p>';
                    return $output;
                }
                $current_mail->clearRecipients();
                $firstid = null;
                $output .= _G('DBMF_exportmailing_sendingnew')."!\n<br>";
            }

            if($contact->b1r08!='') {
                $modulo_index++;
                $mails_index++;
                if( $firstid==null ) 
                    $firstid = $contact->id;
                $current_mail->addBCC($contact->b1r08,$contact->firstname.' '.$contact->lastname);
            } else {
                $output .=  _G('DBMF_exportmailing_sendingnomail').': '.
                            $contact->firstname.' '.$contact->lastname.' (id:'.$contact->id.')<br>';
            }

        }
        if( $firstid!=null ) {
            if( $this->mailing_sendaslist ) {
                $current_mail->sendBCCIndividually();
            } elseif( !$current_mail->send(false) ) {
                $output .= '<samp>'.$current_mail->getError().'</samp>';
                $output .= 'Last ID tried: <b>'.$firstid.'</b></p>';
                return $output;
            }
            $output .= _G('DBMF_exportmailing_sendinglast')."!\n<br>";
        }
        if( $this->mailing_sendaslist )
            $output .= '<samp>'.$current_mail->getError().'</samp>';
        $current_mail->close();
        $output .= '
mails sent: '.$mails_index.'<br>
</p>';

        if($this->mailing_att1!='') unlink($this->mailing_att1);
        if($this->mailing_att2!='') unlink($this->mailing_att2);
        if($this->mailing_att3!='') unlink($this->mailing_att3);
        return $output;
    }

}

?>
