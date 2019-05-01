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
    .vteProgressBarContainer {
        padding-left: 14%;
        max-height: 32px!important;
        padding-right: 0px!important;
    }
    .vteProgressBarContainer .vteProgressBarMiddleContainer {
        width: 95%;
        overflow-x: hidden;
    }
    .p-r-0 {
        padding-right: 0px!important;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer {
        white-space: nowrap;
        display: flex;
        max-width: 100%!important;
        margin-top: -13px;
    }
    .list-inline {
        padding-left: 0;
        margin-left: -5px;
        list-style: none;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn {
        width: 128px!important;
        min-width: 128px;
        padding: 0;
        position: relative;
        padding-top: 16px;
    }
    .row ul li:first-child {
        margin-left: 0;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn .firstColumn {
        border-left: 0;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn .vteProgressBarHeaderEmpty {
        cursor: pointer;
        height: 28px;
        padding: 0 15px 0 0;
        display: inline-block;
        color: #e2e0e0;
        position: relative;
        width: 124px;
        max-width: 124px;
        border-top: 15px solid #e2e0e0;
        border-bottom: 15px solid #e2e0e0;
        border-left: 10px solid transparent;
        box-shadow: 0 -2px 0 #e2e0e0;
        margin-top: 2px;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn .vteProgressBarHeaderTitleContainer .vteProgressBarHeaderTitle {
        width: 100%;
        color: black;
        text-align: center;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn .vteProgressBarHeaderTitleContainer {
        margin-top: -10px;
        padding-left: 5px;
        width: 105px;
        max-width: 105px;
    }
    .vteProgressBarContainer .vteProgressBarHeaderContainer .vteProgressBarHeaderColumn .vteProgressBarHeaderEmpty:after {
        content: '\0000a0';
        width: 0;
        height: 0;
        border-left: 10px solid;
        border-top: 15px solid transparent;
        border-bottom: 15px solid transparent;
        display: inline-block;
        position: absolute;
        top: -15px;
        right: -10px;
    }
    .vteProgressBar-Active:after {
        color: #21ba5c;
    }
    .vteProgressBar-Active {
        border-top: 15px solid #21ba5c !important;
        border-bottom: 15px solid #21ba5c!important;
        border-left: 10px solid transparent!important;
        box-shadow: 0 -2px 0 #21ba5c!important;
    }
    .vteProgressBar-Active .vteProgressBarHeaderTitle{
        color:#fff!important;
    }
    .vteProgressBarContainer .progressbarNavigator {
        width: 4%;
        margin-top: -8px;
    }
    .p-x-0 {
        padding-left: 0px!important;
        padding-right: 0px!important;
    }
    .vteProgressBarMainContainer .progressbarPrev {
        right: 32px;
        margin-top: 18px;
    }
    .cursorPointer {
        cursor: pointer;
        text-decoration: none;
    }
    .pull-left {
        float: left!important;
    }
    .f-20 {
        font-size: 20px!important;
    }
    .vteProgressBarMainContainer .progressbarNext {
        right: 0;
        margin-top: 18px;
    }
    .slider-wrap{
        position: relative;
        width: 100%;
        height: 150px;
        overflow-y:hidden;
        overflow-x:scroll;
    }
    .slider-wrap {
        position: relative;
        width: 100%;
        height: 230px;
        overflow-y:hidden;
        overflow-x:scroll;
        /*margin-top: 20px;*/
    }
    .slide-wrap {
        position: relative;
        width: 100%;
        top: 0;
        left: 0;
    }
    .slider-slide-wrap {
        position: absolute;
        width: 128px;
        height: 100%;
    }
    #div_vtprogressbar{
      display:none;
    }
</style>
    <div class="vteProgressBarMainContainer" id="div_vtprogressbar" >
        <div class="col-lg-12  col-md-12 col-sm-12 vteProgressBarContainer">
            <div class="vteProgressBarMiddleContainer  col-lg-11 col-md-11 col-sm-11 p-r-0 slider-wrap">
                <div class="vteProgressBarHeaderContainer list-inline slide-wrap">
                    {foreach key = key item=BAR from=$PROGRESSBARS}
                        <div class="vteProgressBarHeaderColumn slider-slide-wrap {if $CURRENT_STATUS eq {vtranslate($BAR,$MODULE_NAME)}}onView{/if}" data-no = "{$key}" data-value="{$BAR}" data-toggle="tooltip"
                            data-placement="bottom" data-field-name="{$FIELD_NAME}"  data-field-label="{$FIELD_LABEL}"   data-original-title="{vtranslate($BAR,$MODULE_NAME)}">
                            <div class="vteProgressBarHeaderEmpty  {if $CURRENT_STATUS eq {vtranslate($BAR,$MODULE_NAME)}} vteProgressBar-Active {/if}">
                                <div class="vteProgressBarHeaderTitleContainer width100Per ">
                                	{if $BAR eq ''}
                                		{$BAR = '--'}
                                	{/if}
                                    <div class="vteProgressBarHeaderTitle textOverflowEllipsis">{vtranslate($BAR,$MODULE_NAME)}</div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
            <div class="progressbarNavigator pull-right col-lg-1 col-md-1 col-sm-1 p-x-0 m-t-8">
                <span class="progressbarPrev  pull-left cursorPointer">
                    <i  class="fa fa-chevron-circle-left f-20"></i>
                </span>
                        <span class="progressbarNext pull-right  cursorPointer">
                    <i class="fa fa-chevron-circle-right f-20"></i>
                </span>
            </div>
        </div>
    </div>
{/strip}