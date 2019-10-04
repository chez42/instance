{*<!--
/* ********************************************************************************
* The content of this file is subject to the Multiple SMTP ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}

{strip}
    <style>
        .state-highlight{
            height: 2em; line-height: 2em; border: 1px solid #FFD600;background-color:#F9FFB3; border-style: dashed;
        }
        .blocks-content{
            background: white;
            border: 1px solid #dddddd;
        }
        .modal-Fields .span7{
            margin-bottom: 9px;
        }
        .ui-sortable-helper{
            background: white;
            -webkit-box-shadow: 0 0 3px -1px #dddddd;
            -moz-box-shadow: 0 0 3px -1px #dddddd;
            box-shadow: 0 0 3px -1px #dddddd;
        }
        .HelpInfoPopover .icon-info-sign{
            margin-top: 5px;
            margin-left: 5px;
        }
        .blocksSortable {
            min-height: 30px;
        }
    </style>
    <div class='modelContainer'>
        <div id="massEdit">
            <div class="modal-header contentsBackground">
                <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>{vtranslate('LBL_OUTGOING_SERVER', 'Settings:Vtiger')}</h3>&nbsp;{vtranslate('LBL_OUTGOING_SERVER_DESC', 'Settings:Vtiger')}
            </div>
            <div class="modal-body">
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        <th nowrap="">
                            <a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="ASC" data-columnname="subject">Server Name&nbsp;&nbsp;</a>
                        </th>
                        <th nowrap="">
                            <a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="ASC" data-columnname="accountid">User Name&nbsp;&nbsp;</a>
                        </th>
                        <th nowrap=""></th>
                    </tr>
                    </thead>
                </table>
                <div class="clearfix"></div>
                <div class="blocks-content padding1per" >
                    <div class="row-fluid">
                        <div class="blocksSortable ui-sortable" data-column="1">
                            {foreach item=server from=$SERVERS_LIST}
                                <div class="blockSortable" data-id="{$server.id}">
                                    <div class="padding1per border1px">
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <img class="alignMiddle" src="{vimage_path('drag.png')}" /> &nbsp;&nbsp;{$server.server}
                                            </div>
                                            <div class="span4">
                                                {$server.server_username}&nbsp;
                                            </div>
                                            <div class="span2">
                                                <span class="pull-right">
                                                    <a class="edit_info" href="javascript:void(0);" data-url="index.php?module=MultipleSMTP&view=MassActionAjax&mode=showMassEditForm&userid={$server.userid}&id={$server.id}">
                                                        <i class="icon-pencil alignMiddle" title="Edit"></i>
                                                    </a>&nbsp;
                                                &nbsp;&nbsp;  <a class="delete_server" data-userid="{$server.userid}" data-url="index.php?module=MultipleSMTP&action=DeleteAjax&userid={$server.userid}&id={$server.id}">
                                                        <i class="icon-trash alignMiddle" title="Delete"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
                    <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
                <button class="btn btn-success edit_info" data-url="index.php?module=MultipleSMTP&view=MassActionAjax&mode=showMassEditForm&userid={$USERID}" type="" name=""><strong>{vtranslate('LBL_CREATE', $MODULE)}</strong></button>
            </div>
        </div>
    </div>
{/strip}