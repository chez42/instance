{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
    <style>
        .rcorners2 {
            border-radius: 5px;
            padding: 10px;
            width: 40px;
            height: 40px;
            float: left;
        }
    </style>
    <div class="modal-dialog" style="min-width:150px;">
        <div class='modal-content'>
            <div class="modal-body blockSortable" style="display: flex;flex-wrap: wrap;">
                <div class="header-div">
                    <div class="rcorners2" style="float:left;border: 2px solid #{$HEADER['color']};">
                        <span class="icon-module {$HEADER['icon']}" style="font-size: 17px;color: #{$HEADER['color']};"></span>
                    </div>
                    <div style="float: right;">
                            <span class="l-header muted"
                                  style="vertical-align: left; padding-left: 11px;">{$HEADER['header']}</span><br />
                           <span class="l-value"
                                 style="vertical-align: left; padding-left: 11px;">{$HEADER['field_name']}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
{/strip}