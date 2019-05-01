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
    .vteButtonQuickUpdate {
        border-radius: 2px;
        background-image: none !important;
        box-shadow: none !important;
        line-height: 18px;
        cursor: pointer;
        font-weight: 400;
        padding: 6px 16px !important;
        margin: 0px 4px!important;
        background-color: #FFFFFF !important;
    }
    .vtebuttons-header-block{
        float: left;
        width: 25%;
    }
    .c-header{
        padding-top: 5px;
        margin-left: -22%;
    }
    #div_vtebuttons{
        display: none;
    }
</style>
    <div class="col-lg-6 c-header" id="div_vtebuttons">
       {foreach key =key item=HEADER from=$HEADERS}
           <style>
            .p-o-vtebtn{$HEADER['vtebuttonsid']}:hover {
                background-color: #{$HEADER['color']}!important;
                color: #FFFFFF!important;
            }
           </style>
            <div class="vtebuttons-header-block" {if $key gt 3 } style="margin-top: 5px;"{/if} data-vtebuttonid="{$HEADER['vtebuttonsid']}">
                <div style="text-align: left;margin-top: 4px;">
                    <button type="button" class="vteButtonQuickUpdate p-o-vtebtn{$HEADER['vtebuttonsid']}" data-vtebuttonid="{$HEADER['vtebuttonsid']}" style="color: #{$HEADER['color']};border: thin solid #{$HEADER['color']} !important; ">
                        <i class="icon-module {$HEADER['icon']}" style="font-size: inherit;"></i>
                        &nbsp;{$HEADER['header']}</button>
                </div>
            </div>
        {/foreach}
    </div>
{/strip}