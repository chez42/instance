{*<!--
/* ********************************************************************************
* The content of this file is subject to the Multiple SMTP ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('VTEFeedback', 'VTEFeedback')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer">
        <div class="row-fluid">
            <h4 style="width: 27%; float: left; margin-top: 0">{vtranslate('LBL_ENABLE_MODULE', 'VTEFeedback')}</h4>
            <input type="checkbox" name="enable_module" id="enable_module" value="1" {if $ENABLE eq '1'}checked="" {/if}/>
        </div>
    </div>
</div>