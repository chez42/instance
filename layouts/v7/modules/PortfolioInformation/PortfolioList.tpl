<link rel="stylesheet" href="layouts/vlayout/modules/Omniscient/css/Report_Buttons.css" type="text/css" />
{foreach key=index item=jsModel from=$SCRIPTS}
<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
<!--<h2>All {$MODULE_TITLE} Portfolios</h2>-->
{*<div class="widget_header row-fluid">
<h4>Reports</h4>
</div>*}
<input type="hidden" name="MODULE" value="{$MODULE}" />
<input type="hidden" name="RECORD" value="{$RECORD}" />
<input type="hidden" name="CALLING_RECORD" value="{$CALLING_RECORD}" />
<input type="hidden" name="ACCOUNT_NUMBERS" value='{$ACCOUNT}' />
<br />
<div style="margin-left:auto; margin-right:auto; width:400px;">

    {*
<a href="#" id="HoldingsReport" onclick="return false;" style="width:85px; float:left;" data-account='{$ACCOUNT}' data-calling="{$CALLING_RECORD}">Holdings</a>
{*<input type="button" id="HoldingsReport" value="Generate Holdings Report" data-account='{$ACCOUNT}' data-calling="{$CALLING_RECORD}"/>*}
    {*
<a href="#" class="load_report report_detail" onclick="return false;" style="width:85px; float:left; margin-left:4px;">Income</a>
{*<a href="#" class="load_report report_detail" onclick="return false;" style="width:85px; float:left; margin-left:4px;">Performance</a> *}
    {*
<a href="#" class="load_report report_detail" onclick="return false;" style="width:85px; float:left;  margin-left:4px;">Overview</a><br />*}
</div>


