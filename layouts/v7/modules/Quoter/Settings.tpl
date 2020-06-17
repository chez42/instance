{*/* * *******************************************************************************
* The content of this file is subject to the Quoter ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="container-fluid">
    <h4>{vtranslate('Item Details Customizer (Advanced)', $QUALIFIED_MODULE)}</h4>
    <hr>
    <div class="clearfix"></div>
    <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
    <input type="hidden" name="action" value="SaveAjax"/>
    <div class="related-tabs summaryWidgetContainer ">
        <ul class="nav nav-tabs">
            {foreach from=$SETTINGS name=SETTING_NAME  key=MODULE item=SETTING}
                <li  class="tab-item {if $smarty.foreach.SETTING_NAME.first}active{/if}" >
                    <a href="#module_{$MODULE}" data-toggle="tab" class="textOverflowEllipsis">
                        <strong>{vtranslate($MODULE, $MODULE)}</strong>
                    </a>
                </li>
            {/foreach}
            <li class="pull-right">
                <a target="_blank" href="https://www.vtexperts.com/vtiger-item-details-customizer-advanced-upgrading-vtiger-7/">{vtranslate('LBL_UPGRADING_FROM_VTIGER6',$QUALIFIED_MODULE)}</a>
            </li>
        </ul>
        <div class="tab-content col-lg-12 col-md-12">
            {foreach from=$SETTINGS key=MODULE name=SETTING_NAME item=MODULE_SETTING}
                {assign var = "TOTAL_SETTING" value=$TOTAL_SETTINGS.$MODULE}
                {assign var = "SECTION_VALUES" value=$SECTIONS_SETTINGS.$MODULE}
                <div class="tab-pane moduleTab  {if $smarty.foreach.SETTING_NAME.first}active{/if}" id="module_{$MODULE}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <input type="hidden" name="module_name" value="{$MODULE}">
                            <ul class="nav nav-pills" style="display:  block; margin-top: 10px; margin-bottom:0;">
                                <li role="presentation" class='active'><a href="#ItemField_{$MODULE}"  data-toggle = "pill">{vtranslate('LBL_ITEMS', $QUALIFIED_MODULE)}</a></li>
                                <li role="presentation"><a href="#totalsTab_{$MODULE}"  data-toggle = "pill">{vtranslate('LBL_TOTALS', $QUALIFIED_MODULE)}</a></li>
                                <li role="presentation"><a href="#sectionTab_{$MODULE}"  data-toggle = "pill">{vtranslate('LBL_SECTIONS', $QUALIFIED_MODULE)}</a></li>
                            </ul>
                        </div>

                        {**********List All Field************}
                        <div class="col-lg-6 col-md-6 select_field_container">
                            <span class="display_field_name"></span>
                            <span class="copy_icon"><img src="layouts\vlayout\modules\Quoter\images\copy-icon.png" alt=""/></span>
                            <select class="select2 select_field_name" style="width: 220px">
                                <option value="">{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}</option>
                                {foreach from=$MODULE_SETTING item = SETTING key=COLUMN}
                                    {if in_array($SETTING->columnName,$COLUMN_DEFAULT)}
                                        <option value="{$SETTING->columnName}">{vtranslate($SETTING->columnName,$QUALIFIED_MODULE)}</option>
                                    {else}
                                        {if $SETTING->customHeader}
                                            <option value="{$SETTING->columnName}">{$SETTING->customHeader}</option>
                                        {/if}
                                    {/if}
                                {/foreach}
                                {foreach item=FIELD_VALUE key=FIELD_NAME from=$TOTAL_SETTING}
                                    <option value="{$FIELD_NAME}">{vtranslate($FIELD_VALUE.fieldLabel,'Quoter')}</option>
                                {/foreach}
                                {foreach from=$MODULE_SETTING['all_field'] item=FIELD_MODEL}
                                    <option value="{$FIELD_MODEL->get('name')}">{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="tab-content fieldBlockContainer">
                        {********COLUMNS ITEMS*********}
                        <div class="tab-pane itemTab active" id="ItemField_{$MODULE}">
                            <form name="frmColumn" class="frmColumn">
                                <div class="colContainer " >
                                    {foreach from=$MODULE_SETTING item = SETTING key=COLUMN}
                                        {if is_numeric($COLUMN)}
                                            {include file="ColumnsDetails.tpl"|@vtemplate_path:$QUALIFIED_MODULE SETTING = $SETTING MODULE = $MODULE COLUMN_DEFAULT = $COLUMN_DEFAULT MAPPED_COLUMN=$MAPPED_COLUMNS[$MODULE][$COLUMN] }
                                        {/if}
                                    {/foreach}

                                </div>
                                <div class="base_column">
                                    {include file="ColumnsDetails.tpl"|@vtemplate_path:$QUALIFIED_MODULE SETTING = $MODULE_SETTING['base_column'] MODULE = $MODULE COLUMN_DEFAULT = $COLUMN_DEFAULT BASE = 'hide' MAPPED_COLUMN = $MAPPED_COLUMNS[$MODULE]['base_column'] }
                                </div>
                                <div style="margin-top: 20px;">
                                    <span class="pull-left">
                                        <button class="btn btn-success btnSaveSettings" type="submit" >{vtranslate('LBL_SAVE')}</button>
                                    </span>
                                    <span class="pull-right">
                                        <button class="btn btn-default btnAddNewColumn" type="button" >
                                            <i class="fa fa-plus"></i> &nbsp; <strong>{vtranslate('LBL_ADD_NEW_COLUMN',$QUALIFIED_MODULE)}</strong>
                                        </button>
                                    </span>
                                    <span class="clearfix"></span>
                                </div>
                            </form>
                        </div>

                        {*********TOTAL FIELDS*********}
                        <div class="tab-pane totalTab" id="totalsTab_{$MODULE}">
                            <form name="frmTotal" class="frmTotal">
                                <div class="fieldTotalContainer" style="padding-top: 10px;" >
                                    <table class="table table-bordered tblTotalFieldsContainer">
                                        <tbody>
                                            <tr>
                                                <th>{vtranslate('LBL_TOOLS',$QUALIFIED_MODULE)}</th>
                                                <th>{vtranslate('LBL_LABEL_FIELD',$QUALIFIED_MODULE)}<span class="redColor">*</span></th>
                                                <th>{vtranslate('LBL_FORMULA',$QUALIFIED_MODULE)}</th>
                                                <th>{vtranslate('LBL_DATA_ENTRY',$QUALIFIED_MODULE)}</th>
                                                <th>{vtranslate('LBL_RUNNING_SUBTOTAL',$QUALIFIED_MODULE)}</th>
                                            </tr>
                                            {if empty($TOTAL_SETTING)}
                                                {include file="TotalField.tpl"|@vtemplate_path:$QUALIFIED_MODULE FIELD_VALUE = array() FIELD_NAME='' }
                                            {else}
                                                {foreach item=FIELD_VALUE key=FIELD_NAME from=$TOTAL_SETTING name = total_field}
                                                    {assign var = INDEX_TOTAL value=$smarty.foreach.total_field.iteration-1}
                                                    {include file="TotalField.tpl"|@vtemplate_path:$QUALIFIED_MODULE FIELD_VALUE =  $FIELD_VALUE FIELD_NAME=$FIELD_NAME}
                                                {/foreach}
                                            {/if}

                                        </tbody>
                                    </table>
                                    <table class="hide fieldBasic">
                                        {include file="TotalField.tpl"|@vtemplate_path:$QUALIFIED_MODULE FIELD_VALUE = array() FIELD_NAME='' }
                                    </table>
                                </div>
                                <div style="margin-top: 20px;">
                                        <span class="pull-left">
                                            <button class="btn btn-success btnSaveTotalsSettingField" type="submit" id="">{vtranslate('LBL_SAVE')}</button>
                                        </span>
                                        <span class="pull-right">
                                            <button class="btn btn-default addNewTotalField" type="button" >
                                                <i class="fa fa-plus"></i> &nbsp; <strong>{vtranslate('LBL_ADD_NEW_FIELD',$QUALIFIED_MODULE)}</strong>
                                            </button>
                                        </span>
                                    <span class="clearfix"></span>
                                </div>
                            </form>
                        </div>

                        {********SECTION********}
                        <div class="tab-pane sectionTab" id="sectionTab_{$MODULE}">
                            <form name="frmSection" class="frmSection">
                                <div class="row-fluid sectionsContainer" style="width: 70%; padding: 10px;" >
                                    <table class="table table-bordered blockContainer tblSectionsContainer">
                                        <tbody>
                                            <tr>
                                                <th width="5%">{vtranslate('LBL_TOOLS',$QUALIFIED_MODULE)}</th>
                                                <th>{vtranslate('LBL_SECTIONS',$QUALIFIED_MODULE)}</th>
                                            </tr>
                                            {if empty($SECTION_VALUES)}
                                                {include file="Section.tpl"|@vtemplate_path:$QUALIFIED_MODULE SECTION_VALUE = '' INDEX_SECTION = ''}
                                            {else}
                                                {foreach item = SECTION_VALUE key=INDEX_SECTION from=$SECTION_VALUES}
                                                    {include file="Section.tpl"|@vtemplate_path:$QUALIFIED_MODULE SECTION_VALUE = $SECTION_VALUE}
                                                {/foreach}
                                            {/if}

                                        </tbody>
                                    </table>
                                    <table class="hide fieldBasic">
                                        {include file="Section.tpl"|@vtemplate_path:$QUALIFIED_MODULE SECTION_VALUE = '' INDEX_SECTION = ''}
                                    </table>
                                </div>
                                <div style="margin-top: 20px;">
                                    <span class="pull-left">
                                        <button class="btn btn-success btnSaveSectionsValue" type="submit" >{vtranslate('LBL_SAVE')}</button>
                                    </span>
                                    <span class="pull-right">
                                        <button class="btn btn-default addNewSection" type="button" >
                                            <i class="fa fa-plus"></i> &nbsp; <strong>{vtranslate('LBL_ADD_NEW_VALUE',$QUALIFIED_MODULE)}</strong>
                                        </button>
                                    </span>
                                    <span class="clearfix"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
{/strip}