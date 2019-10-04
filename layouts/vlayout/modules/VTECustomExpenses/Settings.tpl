{*<!--
/* ********************************************************************************
* The content of this file is subject to the Custom Expenses/Bills ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('VTECustomExpenses', 'VTECustomExpenses')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer">
        <div class="row-fluid">
            <span class="span2"><h4>{vtranslate('LBL_ENABLE_MODULE', 'VTECustomExpenses')}</h4></span>
            <input type="checkbox" name="enable_module" id="enable_module" value="1" {if $ENABLE eq '1'}checked="" {/if}/>
        </div>
    </div>
</div>