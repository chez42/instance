{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<script language="JAVASCRIPT" type="text/javascript" src="layouts/vlayout/modules/VTEReports/resources/VTEReports.js"></script>
{$DATE_FORMAT}
{assign var="ALL_SKIN" value= Vtiger_Util_Helper::getAllSkins()}
{assign var="CURRENT_THEME" value= $USER_MODEL->get('theme')}
{strip}
<script> var none_lang = "{vtranslate('LBL_NONE')}"; </script>

<style type="text/css">
.table-report th{
  border-bottom:1px solid #DDD;
}
.table-report td{
  border:0px;
}
.table-report tr td {
  background: none !important;
}
.table-bordered tr td{
  border:0px;
  vertical-align: middle;
}
.table-bordered input{
  vertical-align: middle;
  margin:auto;
}
.tabbable {
    margin-left: 20px;
    height: 698px;
}
.tab-content{
    text-align: center;
    margin-left: 20px;
    width: 95%;
}
.nav.massEditTabs li.active a {
    background: #008d4c;
    color: #ffffff;
    margin-left: -8px;
    width: 92%;
}
.nav.massEditTabs li a:hover {
    border-bottom: 1px solid #DDD;
    margin-left: -8px;
    width: 92%;
}
.table-report{
    width: 98%;
    padding: 20px 12px 10px 20px;
    font: 13px Arial, Helvetica, sans-serif;
}
.table-report input,.table-report button,
.table-report textarea,
.table-report select,.table-report .chzn-container{
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    border: 1px solid #C2C2C2;
    box-shadow: 1px 1px 4px #EBEBEB;
    -moz-box-shadow: 1px 1px 4px #EBEBEB;
    -webkit-box-shadow: 1px 1px 4px #EBEBEB;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    padding: 7px;
    outline: none;
    line-height: 20px!important;
}
.table-report select.repBox{
    padding: 10px 12px 0;
}
.table-report .input:focus,
.table-report .textarea:focus,
.table-report .select:focus{
    border: 1px solid #0C0;
}
.table-report .textarea-field{
    height:100px;
    width: 55%;
}
.table-report input[type=submit],
.table-report input[type=button],#cancelbtn,#saverunbtn,#saveFolderButton,#cancelLink{
    border: none;
    padding: 8px 15px 8px 15px;
    background: #FF8500;
    color: #fff;
    box-shadow: 1px 1px 4px #DADADA;
    -moz-box-shadow: 1px 1px 4px #DADADA;
    -webkit-box-shadow: 1px 1px 4px #DADADA;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
}
#cancelbtn,#cancelLink{
    background: #FF8500;
    width: 128px;
}
#saverunbtn{
    background: #9cc83e;
    width: 128px;
}
#cancelLink{
    margin-top: 10px;
}

.table-report input[type=submit]:hover,
.table-report input[type=button]:hover{
    background: #EA7B00;
    color: #fff;
}
.table-report input[type=text]{
    height: 35px;
}
.table-report input[type=checkbox]{
    margin-bottom: 3px;
}

.table-report input[type=radio] {
    margin-right: 10px;
    vertical-align: middle;
}
.table-report select.span3,.table-report select.span5,.table-report select.txtBox{
    height: 37px;
}
.disabledTable {
    pointer-events: none;
    opacity: 0.4;
}
.div_column_child_off{
    background: -webkit-linear-gradient(left, #ddd 0, #ddd 16%, transparent 15%);
    border: 1px solid #ddd;
    cursor: pointer;
    display: inherit;
    float: left;
    margin-left: 15px;
    margin-top: 16px;
    padding-left: 54px;
    width: 138px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    display:inline-block;
    position:relative;
    height: 33px;
}
.div_column_child_off:hover,.div_column_child_on{
    background: -webkit-linear-gradient(left, #1998cb 0, #1998cb 16%, transparent 15%);
    border: 1px solid #1998cb;
    cursor: pointer;
    display: inherit;
    float: left;
    margin-left: 15px;
    margin-top: 16px;
    padding-left: 54px;
    width: 138px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    display:inline-block;
    position:relative;
    height: 33px;
}
.div_column_child_off img{
    height: 34px !important;
    margin-left: -52px;
    width: 30px;
    visibility: hidden;
}
.div_column_child_on img{
    height: 34px !important;
    margin-left: -52px;
    visibility: visible;
    width: 30px;
}
.div_column_child_off p,.div_column_child_on p{
    margin: -31px -16px 0px;
}

.pre_div_column_child_off{
    height: 35px;
    width: 35px;
    background-color: #ddd;
}
.block_title_div{
    display: table;
    margin-top:10px;
}
.block_title_content{
    font-weight: bold;
    margin-top:10px;
}
.select-style{
    border: 1px solid #ccc;
    width: 220px;
    border-radius: 3px;
    overflow: hidden;
    background: #ccc url("./modules/VTEReports/img/select-icon.png") no-repeat 90% 50%;
    background-color: #ccc;
}
.table-report .chzn-container-single, .table-report .chzn-container-single .chzn-single{
    background-color: #ccc!important;
}
.table-report .chzn-container-single .chzn-single {
    background: #fafafa url("./modules/VTEReports/img/select-icon.png") no-repeat 90% 50%;
    border: medium none;
}
.table-report .chzn-container-single .chzn-single div{
    display: none;
}
.table-report .chzn-drop .chzn-search input{
    width: 100%!important;
}
.select-style select {
    padding: 5px 8px;
    width: 100%!important;
    border: none;
    box-shadow: none;
    background: transparent;
    background-image: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    height: 30px;
}
.select-style select:focus {
    outline: none;
}
.myDragClass,.drag_to_sort_order:hover{
    cursor: move;
    background-color: #a5afb9;
    border: 1px solid black;
    height: 50px!important;
}
.op-Users{
    background-color: #f99984!important;
}
.op-Groups{
    background-color: #acd5fe!important;
}
.op-Roles{
    background-color: #feec98!important;
}
.op-RoleAndSubordinates{
    background-color: #c4dea2!important;
}
.vte-table-colors{
    margin-left: 53px;
    width: 225px;
}
.vte-table-colors tr{
    height: 20px;
}
.vte-table-colors td {
    border: 2px solid #fff !important;
    padding: 5px 10px;
    vertical-align: top !important;
}
#sharingSelectedColumns{
    max-width: 486px;
}
.scheduled-div{
    border: 1px solid #111 !important;
    cursor: pointer;
    display: inline;
    float: left;
    height: 20px;
    line-height: 20px;
    margin-left: 3px;
    width: 24px;
    text-align: center;
}
.scheduled-div-weekly{
    width: 30px;
}
.scheduled-div-month{
    width: 70px;
    margin-bottom: 6px;
}
.scheduled-div-selected{
    background-color: #0055ff!important;
    color: #FFFFFF;
}
.cb-medium{
    width: 220px!important;
}
.cb-small{
    width: 88px!important;
}
.cb-small2{
    width: 109px!important;
}
.blockHeader th{
    margin: 1em 0 0.5em 0;
    font-weight: normal;
    position: relative;
    text-shadow: 0 -1px rgba(0,0,0,0.6);
    font-size: 28px;
    line-height: 40px;
    background: {$ALL_SKIN[$CURRENT_THEME]};
    border: 1px solid #fff;
    padding: 5px 15px;
    color: white;
    border-radius: 0 10px 0 10px!important;
    box-shadow: inset 0 0 5px rgba(53,86,129, 0.5);
    font-family: 'Muli', sans-serif;
}
.conditionGroup table.small td{
    /*text-align:center ;*/
}
/*for down and up button*/
#next_rep_top{
    position: fixed;
    right: 16px;
    top: 300px;
}
#back_rep_top{
    position: fixed;
    right: 16px;
    top: 238px;
}
.next_rep_top:after,.back_rep_top:after,
.next_rep_top:before, .back_rep_top:before{
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    color:red;
}
.next_rep_top,.back_rep_top {
    opacity: 1;
    -webkit-transition: all .5s ease-in 3s;
    transition: all .5s ease-in 3s;
}

.next_rep_top,.back_rep_top {
    position: absolute;
    bottom: 30px;
    margin-left: -16px;
    display: block;
    width: 48px;
    height: 48px;
    border: 2px solid {$ALL_SKIN[$CURRENT_THEME]};
    background-size: 14px auto;
    border-radius: 50%;
    z-index: 2;
    -webkit-animation: bounce 2s infinite 2s;
    animation: bounce 2s infinite 2s;
    -webkit-transition: all .2s ease-in;
    transition: all .2s ease-in;
}

.next_rep_top:before,.back_rep_top:before {
    position: absolute;
    top: calc(50% - 9px);
    left: calc(50% - 7px);
    transform: rotate(-45deg);
    display: block;
    width: 15px;
    height: 15px;
    content: "";
    border: 7px solid {$ALL_SKIN[$CURRENT_THEME]};
}
.next_rep_top:before{
    border-width: 0 0 4px 4px;
}
.back_rep_top:before {
    border-width: 4px 4px 0 0;
}
@keyframes bounce {
    0%,
    100%,
    20%,
    50%,
    80% {
        -webkit-transform: translateY(0);
        -ms-transform: translateY(0);
        transform: translateY(0);
    }
    40% {
        -webkit-transform: translateY(-10px);
        -ms-transform: translateY(-10px);
        transform: translateY(-10px);
    }
    60% {
        -webkit-transform: translateY(-5px);
        -ms-transform: translateY(-5px);
        transform: translateY(-5px);
    }
}
/*end down and up button*/
.table-filter  td{
    padding:7px;
}
</style>
<form name="NewReport" id="NewReport" action="index.php" method="POST" enctype="multipart/form-data" onsubmit="return changeSteps();">
<input type="hidden" name="module" value="VTEReports">
<input type="hidden" name='secondarymodule' id='secondarymodule' value="{$SEC_MODULE}"/>
<input type="hidden" name="record" id="record" value="{$RECORDID}">
<input type="hidden" name='modulesString' id='modulesString' value=''/>
<input type="hidden" name='reload' id='reload' value='true'/>
<input type="hidden" name='action' id='action' value='Save'/>
<input type="hidden" name='file' id='file' value=''/>
<input type="hidden" name='folder' id='folder' value="{$FOLDERID}"/>
<input type="hidden" name='relatedmodules' id='relatedmodules' value='{$relmodulesstring}'/>
<input type="hidden" name='mode' id='mode' value='{$MODE}' />
<input type="hidden" name='isDuplicate' id='isDuplicate' value='{$isDuplicate}' />
<input type="hidden" name='SaveType' id='SaveType' value='' />
<input type="hidden" name='actual_step' id='actual_step' value='1' />
<input type="hidden" name='cancel_btn_url' id='cancel_btn_url' value='{$cancel_btn_url}' />

<input type="hidden" name="reporttype" id="reporttype" value="{$REPORTTYPE}">

<!-- DISPLAY -->
<table>
    <tr>
    {if $CREATE_MODE eq 'edit'}
        {if $DUPLICATE_REPORTNAME eq ""}
            <td class=heading2 valign=bottom>&nbsp;&nbsp;<b>{$MOD.LBL_EDIT} &quot;{$REPORTNAME}&quot; </b></td>
        {else}
            <td class=heading2 valign=bottom>&nbsp;&nbsp;<b>{$MOD.LBL_DUPLICATE} &quot;{$DUPLICATE_REPORTNAME}&quot; </b></td>
        {/if}
    {else}
        <td class=heading2 valign=bottom>&nbsp;&nbsp;<b>{$MOD.LBL_NEW_TEMPLATE}</b></td>
    {/if}
    </tr>
</table>
<div class="">
    <div id="detailViewLayout">
        <table align="center" style="width: 73%">
            <tr>
                <td valign="top">
                    {*************************		 STEP 1 		************************}
                    <div class="reportTab" id="step1">
                            {include file='modules/VTEReports/Step1.tpl'}
                    </div>
                    {*************************		 STEP 3 END 		************************}

                    {*************************		 STEP 4 		************************}
                    <div class="{$steps_display}" id="step4">
                            {$REPORT_GROUPING}
                    </div>
                    {*************************		 STEP 4 END 		************************}

                    {*************************		 STEP 5 		************************}
                    <div class="{$steps_display}" id="step5">
                            {$REPORT_COLUMNS}
                    </div>
                    {*************************		 STEP 5 END 		************************}
                    {*************************		 STEP 7 		************************}
                    <div class="{$steps_display}" id="step7">
                        {$REPORT_LABELS}
                    </div>
                    {*************************		 STEP 7 END 		************************}
                    <div class="{$steps_display}" id="step7_1"></div>
                    <div class="{$steps_display}" id="step7_2"></div>
                    {*************************		 STEP 6 		************************}
                    <div class="hide" id="step6">
                            {$REPORT_COLUMNS_TOTAL}
                    </div>
                    {*************************		 STEP 6 END 		************************}

                    {*************************		 STEP 8 		************************}
                    <br />
                    <div class="{$steps_display}" id="step8">
                            {$REPORT_FILTERS}
                    </div>
                    <br />
                    {*************************		 STEP 8 END 		************************}
                    {*************************		 STEP 9 		************************}
                    <div class="{$steps_display}" id="step9">
                            {$REPORT_SHARING}
                    </div>
                    <br />
                    {*************************		 STEP 9 END 		************************}
                    {*************************		 STEP 10 		************************}
                    <div class="{$steps_display}" id="step10">
                            {$REPORT_SCHEDULER}
                    </div>
                    <br />
                    {*************************		 STEP 10 END 		************************}
                    {*************************		 STEP 11 		************************}
                    {*<div id="step11" style="display:block;">
                            {php}include("modules/VTEReports/ReportQuickFilter.php");{/php}
                    </div>*}
                    {*************************		 STEP 11 END 		************************}
                    {*************************		 STEP 12 		************************}
                    <div class="{$steps_display}" id="step11">
                            {$REPORT_GRAPHS}
                    </div>
                    {*************************		 STEP 12 END 		************************}

                    {************************************** END OF TABS BLOCK *************************************}
                   </td>
            </tr>
       </table>
    </div>
</div>
<div>
    {****** BUTTONS BOTTOM ******}
    <table width="100%"  border="0" cellspacing="0" cellpadding="5" >
        <tr><td class="small" style="text-align:center;padding:10px 0px 10px 0px;" colspan="3">
                <a class="back_rep_top" name="back_rep_top" id="back_rep_top" style="background: #8f8f73;cursor: not-allowed;"></a>
                <div  id="submitbutton" style="display:{if $MODE !='edit'}none{else}inline{/if};" >
                    <button type="button" class="" id="cancelbtn" onclick=""><strong>{vtranslate('LBL_CANCEL_BUTTON_LABEL',$MODULE)}</strong></button>&nbsp;
                    <button class="" type="button" id="saverunbtn" onclick=""><strong>{vtranslate('LBL_SAVE_BUTTON_LABEL',$MODULE)}</strong></button>
                </div>
                <div  id="submitbutton0B" style="display:{if $MODE !='edit'}inline{else}none{/if};" >
                    <button type="button" class="btn btn-danger backStep" id="cancelbtn0B" onclick=""><strong>{vtranslate('LBL_CANCEL_BUTTON_LABEL',$MODULE)}</strong></button>&nbsp;
                </div>
                <a href="#" class="next_rep_top" id="next_rep_top"></a>
            </td>
        </tr>
    </table>
    {****** BUTTONS BOTTOM END ******}
</div>
</form>
<script>
var constructedOptionValue;
var constructedOptionName;

var roleIdArr=new Array({$ROLEIDSTR});
var roleNameArr=new Array({$ROLENAMESTR});
var userIdArr=new Array({$USERIDSTR});
var userNameArr=new Array({$USERNAMESTR});
var grpIdArr=new Array({$GROUPIDSTR});
var grpNameArr=new Array({$GROUPNAMESTR});

/*Sharing functions*/
function sharing_changed(this_select) {
    var selectedValue = jQuery(this_select).val();
    if (selectedValue != 'share') {
        jQuery('#sharing_share_div').addClass('hide');
    }
    else {
        jQuery('#sharing_share_div').removeClass('hide');
    }
}
jQuery( document ).ready(function(){
    jQuery('#sharing').trigger('change');
    jQuery('#sharingSelectedColumns').on("change", function (e) {
        var selected_css = e.added.css;
        var last_li = jQuery( "div[id*='sharingSelectedColumns']").children("ul").find("li.select2-search-choice:last");
        last_li.css("background-image","none");
        last_li.addClass(selected_css);
    });
});
/*Sharing Ends*/
</script>
{/strip}