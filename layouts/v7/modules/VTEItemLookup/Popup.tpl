{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Popup.php *}

{strip}
    <style>
        body::-webkit-scrollbar {
            width: 1px;
        }
        body::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        }
        body::-webkit-scrollbar-thumb {
            background-color: darkgrey;
            outline: 1px solid slategrey;
        }
    </style>
<div id="itemLookUpPopupModal" class="modal-dialog modal-lg" style="width: 99%;">
    <div class="modal-content">
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE={vtranslate($VTE_MODULE,$VTE_MODULE)}}
        <div class="modal-body" style="">
            <div class="row">
                <div class="col-lg-12" id="itemLookUpPopupContainer">
                    <div class="row">
                        <div class="col-lg-2">
                            <div id="popupFillContainer" class="contentsDiv">
                                {include file='PopupFillContents.tpl'|vtemplate_path:$VTE_MODULE}
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div id="popupPageContainer" class="contentsDiv">
                                <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
                                <input type="hidden" id="module" value="{$MODULE}"/>
                                <input type="hidden" id="vte_module" value="{$VTE_MODULE}"/>
                                <input type="hidden" id="parent" value="{$PARENT_MODULE}"/>
                                <input type="hidden" id="sourceRecord" value="{$SOURCE_RECORD}"/>
                                <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
                                <input type="hidden" id="url" value="{$GETURL}" />
                                <input type="hidden" id="multi_select" value="{$MULTI_SELECT}" />
                                <input type="hidden" id="currencyId" value="{$CURRENCY_ID}" />
                                <input type="hidden" id="relatedParentModule" value="{$RELATED_PARENT_MODULE}"/>
                                <input type="hidden" id="relatedParentId" value="{$RELATED_PARENT_ID}"/>
                                <input type="hidden" id="view" name="view" value="{$VIEW}"/>
                                <input type="hidden" id="relationId" value="{$RELATION_ID}" />
                                <input type="hidden" id="selectedIds" name="selectedIds">
                                <input type="hidden" id="decimalSeparator" value="{$DECIMAL_SEPARATOR}" name="decimalSeparator">
                                <input type="hidden" id="digitGroupingSeparator" value="{$DIGIT_GROUPING_SEPARATOR}" name="digitGroupingSeparator">
                                {if !empty($POPUP_CLASS_NAME)}
                                    <input type="hidden" id="popUpClassName" value="{$POPUP_CLASS_NAME}"/>
                                {/if}
                                <div id="popupContents" style="min-height: 500px;" class="">
                                    {include file='PopupContents.tpl'|vtemplate_path:$VTE_MODULE}
                                </div>
                                <input type="hidden" class="triggerEventName" value="{$smarty.request.triggerEventName}"/>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
{/strip}