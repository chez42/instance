{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
     <div class="showAllTagContainer hide">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="detailShowAllModal">
                    {assign var="TITLE" value="{vtranslate('LBL_ADD_OR_SELECT_TAG',$MODULE,$RECORD_NAME)}"}
                    {include file="ModalHeader.tpl"|vtemplate_path:$MODULE}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 selectTagContainer">
                                
                                <div class="form-group">
                                    <label class="control-label">
                                        {vtranslate('Select or add new tags', $MODULE)}
                                    </label>
                                    <div class="dropdown">
                                    	{assign var=ALL_TAGS value=Vtiger_Tag_Model::allTagResult()}
                                    	<input id="tagField"  name="tag" type="text" class="autoComplete form-control sourceField select2 dropdown-toggle"  value="" placeholder="Search tag or add new">
                                    	<input type="hidden" name="visibility" value="{Vtiger_Tag_Model::PRIVATE_TYPE}"/>
                                    	<input type="hidden" class="availabletags" value='{Zend_JSON::encode($ALL_TAGS)}'/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {include file="ModalFooter.tpl"|vtemplate_path:$MODULE}
                </form>
            </div>
        </div>
    </div>
{/strip}