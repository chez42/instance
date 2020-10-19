{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{if $FOLDERS}
    {assign var=INBOX_ADDED value=0}
    {assign var=TRASH_ADDED value=0}
     <style>
		.jstree-default-small .jstree-node, .jstree-default-small .jstree-icon {
			background-image: unset !important;
		}
		.jstree-default-small .jstree-anchor {
			height: 40px !important;
		}
		.jstree ul > li > a{
			margin-top : -28px !important;
		}
		ul.jstree-children > li > ul > li:first-child {
		    margin-left: 18px;
		}
		.jstree-default .jstree-hovered {
		    background: unset !important;
		    border-radius: unset !important;
		    box-shadow: unset !important;
		}
		.jstree-default .jstree-clicked {
		    border-radius: unset !important;
		    box-shadow: unset !important;
		    background: unset !important;
		    color : blue !important;
		}
		.modules-menu .jstree ul li:hover {
			background: unset !important;
		  	border-left: unset !important;
		  	opacity: 1;
		}
		.modules-menu .jstree ul li a {
		  	opacity: 1;
		}
		.modules-menu ul li.active {
			background: unset !important;
		  	border-left: unset !important;
		}
		
		.tree-body { 
			max-width:100%;
			min-height:600px; 
			min-width:100%; 
			margin:0 auto; 
			padding:10px 10px; 
			font-size:14px; 
			font-size:1em; 
			font-weight: bold;
			color: #fff !important;
		}
		.demo { overflow:auto; min-height:600px; }
		
	</style>
    <ul>
        {foreach item=FOLDER from=$FOLDERS}
            {if stripos($FOLDER->name(), 'inbox') !== false && $INBOX_ADDED == 0}
                {assign var=INBOX_ADDED value=1}
                {assign var=INBOX_FOLDER value=$FOLDER->name()}
                <li class="cursorPointer mm_folder mmMainFolder active" data-foldername="{$FOLDER->name()}">
                    <i class="fa fa-inbox fontSize20px"></i>&nbsp;&nbsp;
                    <b>{vtranslate('LBL_INBOX', $MODULE)}</b>
                    <!-- <span class="pull-right mmUnreadCountBadge {if !$FOLDER->unreadCount()}hide{/if}">
                       {$FOLDER->unreadCount()} 
                    </span> -->
                </li>
                <li class="cursorPointer mm_folder mmMainFolder" data-foldername="vt_drafts">
                    <i class="fa fa-floppy-o fontSize20px"></i>&nbsp;&nbsp;
                    <b>{vtranslate('LBL_Drafts', $MODULE)}</b>
                </li>
            {/if}
        {/foreach}
        
        {foreach item=FOLDER from=$FOLDERS}
            {if $FOLDER->isSentFolder()}
                {assign var=SENT_FOLDER value=$FOLDER->name()}
                <li class="cursorPointer mm_folder mmMainFolder" data-foldername="{$FOLDER->name()}">
                    <i class="fa fa-paper-plane fontSize20px"></i>&nbsp;&nbsp;
                    <b>{vtranslate('LBL_SENT', $MODULE)}</b>
                    <!-- <span class="pull-right mmUnreadCountBadge {if !$FOLDER->unreadCount()}hide{/if}">
                       {$FOLDER->unreadCount()} 
                    </span> -->
                </li>
            {/if}
        {/foreach}
        
        {foreach item=FOLDER from=$FOLDERS}
            {if stripos($FOLDER->name(), 'trash') !== false && $TRASH_ADDED == 0}
                {assign var=TRASH_ADDED value=1}
                {assign var=TRASH_FOLDER value=$FOLDER->name()}
                <li class="cursorPointer mm_folder mmMainFolder" data-foldername="{$FOLDER->name()}">
                    <i class="fa fa-trash-o fontSize20px"></i>&nbsp;&nbsp;
                    <b>{vtranslate('LBL_TRASH', $MODULE)}</b>
                    <!-- <span class="pull-right mmUnreadCountBadge {if !$FOLDER->unreadCount()}hide{/if}">
                       {$FOLDER->unreadCount()} 
                    </span> -->
                </li>
            {/if}
        {/foreach}
        <!-- <br>
        <span class="padding15px"><b>{vtranslate('LBL_Folders', $MODULE)}</b></span> -->
        <input type="hidden" name="folderData" value='{$OTHERFOLDER}' />
        <div id="extraFolderList">
	        {*assign var=IGNORE_FOLDERS value=array($INBOX_FOLDER, $SENT_FOLDER, $TRASH_FOLDER)*}
	        {*foreach item=FOLDER from=$FOLDERS}
	            {if !in_array($FOLDER->name(), $IGNORE_FOLDERS)}
	            <li class="cursorPointer mm_folder mmOtherFolder" data-foldername="{$FOLDER->name()}">
	                <b>{$FOLDER->name()}</b>
	                <span class="pull-right mmUnreadCountBadge {if !$FOLDER->unreadCount()}hide{/if}">
	                   {$FOLDER->unreadCount()} 
	                </span>
	            </li>
	            {/if}
	        {/foreach*}
	        
	        <div class="tree-body" >
				<div class="demo" id="tree_folder">
					
				</div>
			</div>
        </div>
    </ul>
{/if}