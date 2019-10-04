{*<!--
/* ********************************************************************************
* The content of this file is subject to the Comments (Advanced) ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<link type="text/css" rel="stylesheet" href="libraries/jquery/bootstrapswitch/css/bootstrap2/bootstrap-switch.min.css" media="screen">
<style type="text/css">
    .bootstrap-switch{
        margin-left: 15px;
    }
</style>
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('VTEComments', 'VTEComments')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer">
        <div class="row-fluid">
            <span class="span2"><h4>{vtranslate('LBL_ENABLE_MODULE', 'VTEComments')} &nbsp;<input type="checkbox" name="enable_module" id="enable_module" value="1" {if $ENABLE eq '1'}checked="" {/if}/></h4></span>
        </div>
    </div>
    <div class="col-sm-12 row">
        <div class="col-sm-6 row" style="border-right: 1px solid #eee;">
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="row">
                    <div class="col-lg-3"><input type="checkbox" {if $ENABLE_RICHTEXT eq '1'}checked="" {/if} name="enable_richtext" id="enable_richtext" /></div>
                    <div class="col-lg-9" style="padding-top:5px;">
                        <span class="span2">Enable Rich Text on Comments</span>
                        &nbsp;<a class="enable-rich-text-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ENABLE_RICH_TEXT', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="row">
                    <div class="col-lg-3"><input type="checkbox" {if $TAG_FEATURE eq '1'}checked="" {/if} name="tag_feature" id="tag_feature" /></div>
                    <div class="col-lg-9" style="padding-top:5px;">
                        <span class="span2">Enable @Mention/Tag Feature</span>
                        &nbsp;<a class="enable-mention-tag-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ENABLE_MENTION_TAG', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="row">
                    <div class="col-lg-3"><input type="checkbox" {if $EMAIL_TICKET eq '1'}checked="" {/if} name="email_ticket" id="email_ticket" /></div>
                    <div class="col-lg-9" style="padding-top:5px;">
                        <span class="span2">Email Templates on Tickets</span>
                        &nbsp;<a class="enable-email-templates-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ENABLE_EMAIL_TEMPLATES', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="margin-top: 5px;">
                <hr>
            </div>
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="row">
                    <div class="col-lg-3">
                        <input type="checkbox" {if $ROW_TO_SHOW eq '1'}checked{/if} name="row_to_show" id="row_to_show" />
                    </div>
                    <div class="col-lg-9" style="padding-top:5px;">
                        <span class="span2">Enable Conversation/Compact View</span>
                        &nbsp;<a class="enable-show-default-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ENABLE_SHOW_DEFAULT', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="">
                    <div class="col-lg-3">
                        <input type="text" class="inputElement text_fieldLabel text_length" value="{if $TEXT_LENGTH}{$TEXT_LENGTH}{else} 100{/if}" name="text_length" style="width: 75px; margin-top:5px" />
                    </div>
                    <div class="col-lg-9" style="padding-top:8px;    margin-left: -7px;">
                        <span class="span2">Limit Preview to X Characters</span>
                        &nbsp;<a class="limit-characters-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_LIMIT_CHARACTERS', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="margin-top: 5px;">
                <hr>
            </div>
            <div class="col-sm-12 row" style="margin-top: 10px;">
                <div class="">
                    <div class="col-lg-3">
                        <select class="select2" id="slbOrderBy" style="width:100px;">
                            <option value="DESC" {if $ORDER_BY eq 'DESC'}selected {/if}>{vtranslate('Newest First', 'VTEComments')}</option>
                            <option value="ASC" {if $ORDER_BY eq 'ASC'}selected {/if}>{vtranslate('Oldest First', 'VTEComments')}</option>
                        </select>
                    </div>
                    <div class="col-lg-9" style="padding-top:8px; margin-left: -7px;">
                        <span class="span2">Order Comments By</span>
                        &nbsp;<a class="order-by-comments-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ORDERBY_COMMENTS', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            {foreach item=PICKLIST from=$PICKLISTS}
                <div class="col-sm-12 row" style="margin-top: 5px;">
                    <div class="col-lg-2"><input type="checkbox" {if $PICKLIST['presence'] == 2}checked{/if} name="picklist_checkbox" data-field-id="{$PICKLIST['fieldid']}" data-field-name="{$PICKLIST['fieldname']}"/></div>
                    <div class="col-lg-5">
                        <input type="text" class="inputElement picklistLabel" value="{$PICKLIST['fieldlabel']}" placeholder="type to add picklist label" data-field-id="{$PICKLIST['fieldid']}" data-field-name="{$PICKLIST['fieldname']}" {if $PICKLIST['presence'] != 2}disabled="disabled"{/if} style="width:90%;"/>
                        &nbsp;<a class="comments-picklist-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_COMMENTS_PICKLIST', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                    </div>
                    <div class="col-lg-2"><button class="btn btn-success savePickListLabel" data-field-id="{$PICKLIST['fieldid']}" data-field-name="{$PICKLIST['fieldname']}" {if $PICKLIST['presence'] != 2}disabled="disabled"{/if}>Save</button></div>
                    <div class="col-lg-3"><a class="btn btn-success" href="index.php?parent=Settings&module=Picklist&view=Index&source_module=ModComments&picklist_fieldname={$PICKLIST['fieldname']}&picklist_value={$PICKLIST['fieldid']}">Configure picklist values</a></div>
                </div>
            {/foreach}
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="col-lg-2"><input type="checkbox" {if $TEXT_FIELD['presence'] == 2}checked{/if} name="text_field_checkbox" data-field-id="{$TEXT_FIELD['fieldid']}" data-field-name="{$TEXT_FIELD['fieldname']}" /></div>
                <div class="col-lg-5">
                    <input type="text" class="inputElement text_fieldLabel" value="{$TEXT_FIELD['fieldlabel']}" placeholder="type to add text field label" data-field-id="{$TEXT_FIELD['fieldid']}" data-field-name="{$TEXT_FIELD['fieldname']}" {if $TEXT_FIELD['presence'] != 2}disabled="disabled"{/if} style="width:90%;"/>
                    &nbsp;<a class="comments-text-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_COMMENTS_TEXT', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                </div>
                <div class="col-lg-2"><button class="btn btn-success saveTextFieldLabel" data-field-id="{$TEXT_FIELD['fieldid']}" data-field-name="{$TEXT_FIELD['fieldname']}" {if $PICKLIST['presence'] != 2}disabled="disabled"{/if}>Save</button></div>
                <div class="col-lg-3"></div>
            </div>
            <div class="col-sm-12 row" style="margin-top: 5px;">
                <div class="col-lg-2"><input type="checkbox" {if $ALWAYS_SHOW == 1}checked{/if} name="always_show" id="always_show" /></div>
                <div class="col-lg-5" style="padding-top:5px;">
                    <span class="span2">{vtranslate('LBL_ALWAYS_SHOW', 'VTEComments')}</span>
                    &nbsp;<a class="always-show-tooltip" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_ALWAYS_SHOW_TOOLTIP', $MODULE)}"><i class="fa fa-info-circle"></i></a>
                </div>
                <div class="col-lg-5"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6" style="margin-top: 10px;text-align: right;">
        <div class="row">
            <button class="btn btn-success" name="btnSettingSave" id="btnSettingSave" type="button"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
        </div>
    </div>
</div>
<script type="text/javascript" src="libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js"></script>