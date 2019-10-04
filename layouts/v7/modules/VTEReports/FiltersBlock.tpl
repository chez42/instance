{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
{assign var="ALL_SKIN" value= Vtiger_Util_Helper::getAllSkins()}
{assign var="CURRENT_THEME" value= $USER_MODEL->get('theme')}
<style>
    .detailedViewHeader{
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
    .conditionList input,.conditionList button,
    .conditionList textarea,
    .conditionList select,.conditionList .chzn-container{
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
    .conditionList select.repBox{
        line-height: 17px!important;
        padding-top: 2px;
        padding-left: 15px;
        height: 42px;
        width: 222px!important;
        margin-left: -1px;
    }
    .conditionList input[type=submit],
    .conditionList input[type=button],.btn-group button,.textAlignCenter .btn{
        border: none;
        padding: 8px 15px 8px 15px;
        background: #FF8500;
        color: #fff;
        box-shadow: 1px 1px 4px #DADADA!important;
        -moz-box-shadow: 1px 1px 4px #DADADA!important;
        -webkit-box-shadow: 1px 1px 4px #DADADA!important;
        border-radius: 3px!important;
        -webkit-border-radius: 3px!important;
        -moz-border-radius: 3px!important;
    }
    .conditionList input[type=submit]:hover,
    .conditionList input[type=button]:hover{
        background: #EA7B00;
        color: #fff;
    }
    .conditionList input{
        height: 35px;
    }
    .conditionList input{
        margin-top: 7px;
    }
    .conditionList input[type=checkbox]{
        margin-bottom: 3px;
    }

    .conditionList input[type=radio] {
        margin-right: 10px;
        vertical-align: middle;
    }
    .conditionList select.span3,.conditionList select.span5,.conditionList select.txtBox{
        height: 37px;
    }
    .conditionList .chzn-container-single, .conditionList .chzn-container-single .chzn-single{
        background-color: #ccc!important;
    }
    .conditionList .chzn-container-single .chzn-single {
        background: #fafafa url("./modules/VTEReports/img/select-icon.png") no-repeat 90% 50%;
        border: medium none;
    }
    .conditionList .chzn-container-single .chzn-single div{
        display: none;
    }
    .conditionList .chzn-drop .chzn-search input {
        width: 100%!important;
    }
    .select-style{
        border: 1px solid #ccc;
        width: 220px;
        border-radius: 3px;
        overflow: hidden;
        background: #ccc url("./modules/VTEReports/img/select-icon.png") no-repeat 90% 50%;
        background-color: #ccc;
    }
    .conditionList .chzn-container-single{
        width: 236px!important;
    }
    .conditionList .chzn-container-single .chzn-single{
        background-color: #ccc!important;
        /*width: 200px;*/
    }
    .conditionList .chzn-container-active .chzn-single-with-drop{
    -webkit-box-shadow:none;
    }
    .conditionList .input-append{
    height: 35px;
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
    .gconRow .chzn-drop .chzn-search input {
    width: 67%!important;
    }
    .gconRow{
    margin-bottom: 13px;
    }
    .chzn-single-with-drop{
        border: none;
    }
    .chzn-container-active .chzn-drop{
        width: 234px!important;
        top: 33px!important;
        margin-left: -1px;
    }
    .input-append .date .add-on{
        margin-top: 10px!important;
    }
    .conditionGroup,.conditionRow{
        margin-top: 20px;
    }
    tr[name=conditionColumn] td:nth-child(3) span{
        padding-top: 20px;
    }
    tr[name=conditionColumn] td:nth-child(2) div.select-style{
        margin-top: 20px;
        margin-left: 15px;
    }
    .select2-container ul.select2-choices{
        /*//margin-left: 10px;*/
    }
    .select2-container ul.select2-choices input.select2-input{
        width: 500px!important;
    }
</style>
<script language="JAVASCRIPT" type="text/javascript" src="layouts/v7/modules/VTEReports/resources/VTEReports.js"></script>

<input type="hidden" name="std_filter_columns" id="std_filter_columns" value='{Vtiger_Util_Helper::toSafeHTML($std_filter_columns)}' />
<input type="hidden" name="std_filter_criteria" id="std_filter_criteria" value='{Vtiger_Util_Helper::toSafeHTML($std_filter_criteria)}' />
<input type="hidden" name="sel_fields" id="sel_fields" value='{Vtiger_Util_Helper::toSafeHTML($SEL_FIELDS)}' />

{$BLOCKJS_STD}

<script type="text/javascript">
    var advft_column_index_count = -1;
    var advft_group_index_count = 0;
    var column_index_array = [];
    var group_index_array = [];

    var gf_advft_column_index_count = -1;
    var gf_advft_group_index_count = 0;
    var gf_column_index_array = [];
    var gf_group_index_array = [];
    var rel_fields = {$REL_FIELDS};
</script>

{* ADVANCE FILTER START *}
<table border=0 cellspacing=0 cellpadding=0 width="100%">
    {if $DISPLAY_FILTER_HEADER === true}
            <tr>
                <td class="detailedViewHeader" nowrap align="left" colspan="8">
                    <div style="float:left;vertical-align: middle;">
                        <span class="genHeaderGray" style="">{vtranslate('LBL_ADVANCED_FILTER',$MODULE)}</span> &nbsp;
                    </div>
                    <div style="float:right;">
                        <a href="javascript:void(0);" id="display_adv_filter" style="color: #ffffff" data-mode="hide">
                            <span>{vtranslate('Show filters',$MODULE)}</span>
                            <img src="modules/VTEReports/img/hide.png" >
                        </a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    {if $EMPTY_CRITERIA_GROUPS == true}
                        <div style="float:left;margin:10px;" class="conditionList">
                            <button type='button' class='fgroup_btn' style='float:left;' onclick='addNewConditionGroup("adv_filter_div")'><strong>{vtranslate('LBL_NEW_GROUP',$MODULE)}</strong></button>
                        </div>
                    {/if}
                </td>
            </tr>
    {/if}
    <tr>
        <td class="dvtCellLabel" nowrap align="center" style="padding:0px;" colspan="8" >
            <div style="display:block" id='adv_filter_div' name='adv_filter_div'>
                <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
                </table>
                {assign var=FCON_I value="0"}
<script type="text/javascript">var window_onload = "";</script>
                {foreach key=GROUP_ID item=GROUP_CRITERIA from=$CRITERIA_GROUPS}
                    {assign var=GROUP_COLUMNS value=$GROUP_CRITERIA.columns}
                    <script type="text/javascript">
window_onload += addConditionGroup('adv_filter_div');
                    </script>
                    {foreach key=COLUMN_INDEX item=COLUMN_CRITERIA from=$GROUP_COLUMNS}
                        <script type="text/javascript">
window_onload += 
                            addConditionRow('{$GROUP_ID}');
                            document.getElementById('fop' + advft_column_index_count).value = '{$COLUMN_CRITERIA.comparator}';
                            var conditionColumnRowElement = document.getElementById('fcol' + advft_column_index_count);
                            setSelectedCriteriaValue(conditionColumnRowElement,'{$COLUMN_CRITERIA.columnname}');
                            vtereports_updatefOptions(conditionColumnRowElement, 'fop' + advft_column_index_count, '{$COLUMN_CRITERIA.comparator}');
                            addRequiredElements('f', advft_column_index_count);
                            updateRelFieldOptions(conditionColumnRowElement, 'fval_' + advft_column_index_count);
                            var columnvalue = '{$COLUMN_CRITERIA.value}';
                            if ('{$COLUMN_CRITERIA.comparator}' == 'bw' && columnvalue != '') {ldelim}
                                    var values = columnvalue.split(",");
                                    document.getElementById('fval' + advft_column_index_count).value = values[0];
                                    if (values.length == 2 && document.getElementById('fval_ext' + advft_column_index_count))
                                        document.getElementById('fval_ext' + advft_column_index_count).value = values[1];
                            {rdelim} else {ldelim}
                                document.getElementById('fval' + advft_column_index_count).value = columnvalue;
                            {rdelim}
                        </script>
                        {if $COLUMN_CRITERIA.column_condition !=""}
                          <input type="hidden" name="hfcon_{$GROUP_ID}_{$FCON_I}" id="hfcon_{$GROUP_ID}_{$FCON_I}" value='{$COLUMN_CRITERIA.column_condition}' />
                        {/if}
                        {assign var=FCON_I value=$FCON_I+1}
                    {/foreach}
                    {foreach key=COLUMN_INDEX item=COLUMN_CRITERIA from=$GROUP_COLUMNS}
                        <script type="text/javascript">
                            if (document.getElementById('fcon{$COLUMN_INDEX}'))
                                document.getElementById('fcon{$COLUMN_INDEX}').value = '{$COLUMN_CRITERIA.column_condition}';
                        </script>
                    {/foreach}
                {foreachelse}
                {/foreach}
                {foreach key=GROUP_ID item=GROUP_CRITERIA from=$CRITERIA_GROUPS}
                    <script type="text/javascript">
                        if (document.getElementById('gpcon{$GROUP_ID}'))
                            document.getElementById('gpcon{$GROUP_ID}').value = '{$GROUP_CRITERIA.condition}';
                    </script>
                {/foreach}
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
window.onload = function(){
    window_onload;
};
    jQuery(".allConditionContainer").find("table:first").find('tr').not(":first").hide();
    jQuery(document).on("click", "#display_adv_filter", function() {
        var current_display = jQuery(this).data("mode");
        var parent_table = jQuery(this).closest("table");
        if(current_display == "hide"){
            parent_table.find('tr').not(":first").show();
            jQuery(this).data("mode","show");
            jQuery(this).children("span").html(app.vtranslate("Hide filters"));
            jQuery(this).children("img").attr("src","modules/VTEReports/img/show.png");
        }
        else{
            parent_table.find('tr').not(":first").hide();
            jQuery(this).data("mode","hide");
            jQuery(this).children("span").html(app.vtranslate("Show filters"));
            jQuery(this).children("img").attr("src","modules/VTEReports/img/hide.png");
        }

    });
</script>
