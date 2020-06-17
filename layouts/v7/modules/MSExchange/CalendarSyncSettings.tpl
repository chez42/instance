{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{strip}
<div class="modal-dialog modal-lg contactFMEdit" id="contactFMEdit">
    <div class="modal-content" >
        {assign var=HEADER_TITLE value={vtranslate('LBL_FIELD_MAPPING', $MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
    	<form class="form-horizontal" name="calendarsyncsettings">
	        <input type="hidden" name="module" value="{$MODULENAME}" />
	        <input type="hidden" name="action" value="SaveSettings" />
	        <input type="hidden" name="sourcemodule" value="{$SOURCE_MODULE}" />
        	<div class="modal-body">
	            <div id="mappingTable">
	                <table  class="table table-bordered moduleLineItemTable">
	                    <thead>
	                        <tr>
	                            <th>{vtranslate('APPTITLE',$MODULENAME)}</th>
	                            <th>{vtranslate('EXTENTIONNAME',$MODULENAME)}</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    	{foreach item=FieldMapping from=$FIELD_MAPPING}
	                            <tr>
	                                <td data-label="{vtranslate('APPTITLE',$MODULENAME)}">
	                                    {vtranslate($MODULE_FIELDS_LABEL[$FieldMapping['CRM']], $SOURCE_MODULE)}
	                                </td>
	                                <td data-label="{vtranslate('EXTENTIONNAME',$MODULENAME)}">
	                                    {vtranslate($FieldMapping['MSExchange'], $MODULENAME)}
	                                </td>
	                            </tr>
                            {/foreach}
		                </tbody>
	                </table>
	            </div>
        	</div>
    	</form>
	</div>
</div>
{/strip}