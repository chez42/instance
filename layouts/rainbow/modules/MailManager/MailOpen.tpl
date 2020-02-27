{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="container-fluid padding0px">
        <input type="hidden" id="mmFrom" value='{implode(',', $MAIL->from())}'>
        <input type="hidden" id="mmSubject" value='{Vtiger_Functions::jsonEncode($MAIL->subject())}'>
        <input type="hidden" id="mmMsgNo" value="{$MAIL->msgNo()}">
        <input type="hidden" id="mmMsgUid" value="{$MAIL->uniqueid()}">
        <input type="hidden" id="mmFolder" value="{$FOLDER->name()}">
        <input type="hidden" id="mmTo" value='{implode(',', $MAIL->to())}'>
        <input type="hidden" id="mmCc" value='{implode(',', $MAIL->cc())}'>
        <input type="hidden" id="mmDate" value="{$MAIL->date()}">
        <input type="hidden" id="mmUserName" value="{$USERNAME}">
        {assign var=ATTACHMENT_COUNT value=(count($ATTACHMENTS) - count($INLINE_ATT))}
        <input type="hidden" id="mmAttchmentCount" value="{$ATTACHMENT_COUNT}">
        <div class="row" id="mailManagerActions">
        <div class="col-lg-12">
            <div class="col-lg-8 padding0px" id="relationBlock"></div>
            <div class="col-lg-4 padding0px">
                <span class="pull-right">
                    <button type="button" class="btn btn-secondary mailPagination marginRight0px" {if $MAIL->msgno() < $FOLDER->count()}data-folder='{$FOLDER->name()}' data-msgno='{$MAIL->msgno(1)}'{else}disabled="disabled"{/if}>
                        <i class="ti-angle-left"></i>
                    </button>
                    <button type="button" class="btn btn-secondary mailPagination" {if $MAIL->msgno() > 1}data-folder='{$FOLDER->name()}' data-msgno='{$MAIL->msgno(-1)}'{else}disabled="disabled"{/if}>
                        <i class="ti-angle-right"></i>
                    </button>
                </span>
            </div>
        </div>
        </div>

        <div class="row marginTop15px">
            <div class="col-lg-12 ">
                <h5 class="marginTop0px">{$MAIL->subject()}</h5>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-2">
                <div class="mmFirstNameChar">
                <center>
                    {assign var=NAME value=$MAIL->from()}
                    {assign var=FIRST_CHAR value=strtoupper(substr($NAME[0], 0, 1))}
                    {if $FOLDER->isSentFolder()}
                        {assign var=NAME value=$MAIL->to()}
                        {assign var=FIRST_CHAR value=strtoupper(substr($NAME[0], 0, 1))}
                    {/if}
                    <strong>{$FIRST_CHAR}</strong>
                </center>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="mmDisplayName">
                    {if $FOLDER->isSentFolder()}
                        {implode(', ', $MAIL->to())}
                    {else}
                        {$NAME[0]}
                    {/if}
                </span>
                {if $ATTACHMENT_COUNT}
                    &nbsp;&nbsp;<i class="ti-clip fontSize20px"></i>
                {/if}
                <span> 
                    {assign var=FROM value=$MAIL->from()} 
                    &nbsp;&nbsp; 
                    <a href="javascript:void(0)" class="emailDetails" role="tooltip" data-toggle="popover" data-trigger="focus" title="<strong>{vtranslate('LBL_DETAILS', $MODULE)}</strong>" 
                        data-content="<table> 
                        <tr><td class='muted input-info-addon'>{vtranslate('LBL_FROM', $MODULE)}</td><td class='displayEmailValues'>{$FROM[0]}</td></tr> 
                        <tr><td>&nbsp;</td></tr> 
                        <tr><td class='muted input-info-addon'>{vtranslate('LBL_TO', $MODULE)}</td><td class='displayEmailValues'>{foreach from=$MAIL->to() item=TO_VAL}{$TO_VAL}<br>{/foreach}</td></tr> 
                        <tr><td>&nbsp;</td></tr> 
                        {if $MAIL->cc()} 
                            <tr><td class='muted input-info-addon'>{vtranslate('LBL_CC_SMALL', $MODULE)}</td><td class='displayEmailValues'>{foreach from=$MAIL->cc() item=CC_VAL}{$CC_VAL}<br>{/foreach}</td></tr> 
                            <tr><td>&nbsp;</td></tr> 
                        {/if} 
                        {if $MAIL->bcc()} 
                            <tr><td class='muted input-info-addon'>{vtranslate('LBL_BCC_SMALL', $MODULE)}</td><td class='displayEmailValues'>{foreach from=$MAIL->bcc() item=BCC_VAL}{$BCC_VAL}<br>{/foreach}</td></tr> 
                            <tr><td>&nbsp;</td></tr> 
                        {/if} 
                        </table>"> 
                        <i class="ti-info-alt" title="{vtranslate('LBL_SHOW_FULL_DETAILS', $MODULE)}"></i> 
                    </a> 
                </span>
                <span class="pull-left mmDetailDate">
                    {Vtiger_Util_Helper::formatDateTimeIntoDayString($MAIL->date(), true)}
                </span>
            </div>
            <div class="col-lg-4">
                 <div class="clearfix">
                <div class="pull-right">
                    <span class="cursorPointer mmDetailAction" id='mmPrint' title='{vtranslate('LBL_Print', $MODULE)}'><i class="ti-printer"></i></span>
                    <span class="cursorPointer mmDetailAction" id='mmReply' title='{vtranslate('LBL_Reply', $MODULE)}'><i class="ti-angle-left"></i></span>
                    <span class="cursorPointer mmDetailAction" id='mmReplyAll' title='{vtranslate('LBL_Reply_All', $MODULE)}'><i class="ti-angle-double-left"></i></span>
                    <span class="cursorPointer mmDetailAction" id='mmForward' title='{vtranslate('LBL_Forward', $MODULE)}'><i class="ti-share-alt"></i></span>
                    <span class="cursorPointer mmDetailAction" id='mmDelete' title='{vtranslate('LBL_Delete', $MODULE)}' style="border-right: 1px solid #BBBBBB;"><i class="ti-trash"></i></span>
            </div>
        </div>
            </div>
        </div>
        
            <br>
       
        {if $ATTACHMENT_COUNT}
            
            <div class='col-lg-12 alert alert-info'>
                <span><strong>{vtranslate('LBL_Attachments',$MODULE)}</strong></span>
                <span>&nbsp;&nbsp;({count($ATTACHMENTS) - count($INLINE_ATT)}&nbsp;{vtranslate('LBL_FILES', $MODULE)})</span>
                <br><br>
                {foreach item=ATTACHVALUE from=$ATTACHMENTS name="attach"}
                    {assign var=ATTACHNAME value=$ATTACHVALUE['filename']}
                    {if $INLINE_ATT[$ATTACHNAME] eq null}
                        {assign var=DOWNLOAD_LINK value=$ATTACHNAME|@escape:'url'}
                        <span>
                            <i class="{$MAIL->getAttachmentIcon($ATTACHVALUE['path'])}"></i>&nbsp;&nbsp;
                            <a href="index.php?module={$MODULE}&view=Index&_operation=mail&_operationarg=attachment_dld&_muid={$MAIL->muid()}&_atname={$DOWNLOAD_LINK|@escape:'htmlall':'UTF-8'}">
                                {$ATTACHNAME}
                            </a>
                            <span>&nbsp;&nbsp;({$ATTACHVALUE['size']})</span>
                            <a href="index.php?module={$MODULE}&view=Index&_operation=mail&_operationarg=attachment_dld&_muid={$MAIL->muid()}&_atname={$DOWNLOAD_LINK|@escape:'htmlall':'UTF-8'}">
                                &nbsp;&nbsp;<i class="ti-download"></i>
                            </a>
                        </span>
                        <br>
                    {/if}
                {/foreach}
            </div>
            <br><hr class="mmDetailHr"><br>
        {/if}
        
         <div class="row">
            <div class="col-lg-12 mmEmailContainerDiv">
                <div id='mmBody'>{$BODY}</div>
            </div>
        </div>
    </div>
{/strip}