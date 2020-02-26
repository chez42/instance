{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is: vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{if $SETTING_EXIST}
<a name="dfilter">
	<i class='material-icons' border='0' align="absmiddle" title="{vtranslate('LBL_FILTER')}" alt="{vtranslate('LBL_FILTER')}">create</i>
</a>&nbsp;
{/if}
{if !empty($CHART_TYPE)}
    {assign var=CHART_DATA value=ZEND_JSON::decode($DATA)}
    {assign var=CHART_VALUES value=$CHART_DATA['values']}
{/if}
{if (!empty($DATA) && empty($CHART_TYPE))|| !empty($CHART_VALUES)}
<a href="javascript:void(0);" name="widgetFullScreen">
	<i class="material-icons" hspace="2" border="0" align="absmiddle" title="{vtranslate('LBL_FULLSCREEN')}" alt="{vtranslate('LBL_FULLSCREEN')}">fullscreen</i>
</a>
{/if}
{if !empty($CHART_TYPE) && $REPORT_MODEL->isEditable() eq true}
<a href="{$REPORT_MODEL->getEditViewUrl()}" name="customizeChartReportWidget">
	<i class="material-icons" hspace="2" border="0" align="absmiddle" title="{vtranslate('LBL_CUSTOMIZE',$MODULE)}" alt="{vtranslate('LBL_CUSTOMIZE',$MODULE)}">create</i>
</a>
{/if}
<a href="javascript:void(0);" name="drefresh" data-url="{$WIDGET->getUrl()}&linkid={$WIDGET->get('linkid')}&content=data">
	<i class="material-icons" hspace="2" border="0" align="absmiddle" title="{vtranslate('LBL_REFRESH')}" alt="{vtranslate('LBL_REFRESH')}">refresh</i>
</a>
{if !$WIDGET->isDefault()}
	<a name="dclose" class="widget" data-url="{$WIDGET->getDeleteUrl()}">
		<i class="material-icons" hspace="2" border="0" align="absmiddle" title="{vtranslate('LBL_REMOVE')}" alt="{vtranslate('LBL_REMOVE')}">close</i>
	</a>
{/if}