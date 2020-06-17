{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
<div class="sidebar-menu hidden-xs hidden-sm">
    <div class="module-filters" id="module-filters">
        <div class="sidebar-container lists-menu-container">
        
            <div class="sidebar-header clearfix">
                <h5 class="pull-left">{vtranslate('LBL_FOLDERS',$MODULE)}</h5>
            </div>
            <hr>
            <div class=" menu-scroller scrollContainer" data-mcs-theme="dark" style="position:relative; top:0; left:0;">
                <div class="list-menu-content"> 
               	 	<div class="list-group">
                        <ul id="folders-list" class="lists-menu">
                        {if !empty($FOLDERS)}
	                        {foreach item="FOLDER" from=$FOLDERS}
	                             {assign var=FOLDERNAME value={vtranslate($FOLDER->get('folder_name'), $MODULE)}} 
	                            <li style="font-size:12px;" class='documentFolder connectedSortable'>
	                                <a class="filterName folderFiles" data-folderid="{$FOLDER->getId()}" data-folder-name="{$FOLDER->get('folder_name')}" title="{$FOLDERNAME}">
	                                    <i class="fa fa-folder"></i> 
	                                    <span class="foldername">{if {$FOLDERNAME|strlen > 40} } {$FOLDERNAME|substr:0:40|@escape:'html'}..{else}{$FOLDERNAME|@escape:'html'}{/if}</span>
	                                </a>
	                            </li>
	                        {/foreach}
                        {else}
	                        <li class="noFolderText" style="display: none;">
	                            <h6 class="lists-header"><center> 
	                                {vtranslate('LBL_NO')} {vtranslate('LBL_FOLDERS', $MODULE)} {vtranslate('LBL_FOUND')} ... 
	                            </center></h6>    
	                        </li>
                        {/if}
                        </ul>
                	</div>
            	</div>
        	</div>
            
        </div>
    </div>
    
</div>