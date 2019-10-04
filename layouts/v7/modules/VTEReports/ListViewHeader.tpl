{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

{strip}
<div class="listViewPageDiv">
	<div class="listViewTopMenuDiv">
		<div class="listViewActionsDiv row-fluid">
			<span class="btn-toolbar span4">
				<span class="btn-group listViewMassActions">
					<button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
					<ul class="dropdown-menu">
						{foreach item="LISTVIEW_MASSACTION" from=$LISTVIEW_MASSACTIONS}
							<li id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}"><a href="javascript:void(0);" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0}onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick="Vtiger_List_Js.triggerMassAction('{$LISTVIEW_MASSACTION->getUrl()}')"{/if} >{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
						{/foreach}
						{if $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
							<li class="divider"></li>
							{foreach item=LISTVIEW_ADVANCEDACTIONS from=$LISTVIEW_LINKS['LISTVIEW']}
								<li id="{$MODULE}_listView_advancedAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_ADVANCEDACTIONS->getLabel())}"><a {if stripos($LISTVIEW_ADVANCEDACTIONS->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$LISTVIEW_ADVANCEDACTIONS->getUrl()|substr:strlen("javascript:")};'{else} href='{$LISTVIEW_ADVANCEDACTIONS->getUrl()}' {/if}>{vtranslate($LISTVIEW_ADVANCEDACTIONS->getLabel(), $MODULE)}</a></li>
							{/foreach}
						{/if}
					</ul>
				</span>
                <span class="btn-group">
							<button class="btn dropdown-toggle addButton" data-toggle="dropdown" id="{$MODULE}_listView_basicAction_Add">
                                <i class="icon-plus"></i>&nbsp;
                                <strong>{vtranslate('Add Report', $MODULE)}</strong>&nbsp;
                                <i class="caret icon-white"></i></button>
							<ul class="dropdown-menu">
                                <li id="{$MODULE}_listView_basicAction_tabular_report">
                                    <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=tabular">{vtranslate('Tabular Report', $MODULE)}</a>
                                </li>
                                <li id="{$MODULE}_listView_basicAction_summaries_report">
                                    <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=summaries">{vtranslate('Summaries Report', $MODULE)}</a>
                                </li>
                                <li id="{$MODULE}_listView_basicAction_summaries_matrix_report">
                                    <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=summaries_matrix">{vtranslate('Matrix Report', $MODULE)}</a>
                                </li>
                            </ul>
				</span>
			</span>
			<span class="foldersContainer btn-toolbar span4"></span>
			<span class="span4 btn-toolbar">
				{include file='ListViewActions.tpl'|@vtemplate_path:$MODULE}
			</span>
		</div>
	</div>
<div class="listViewContentDiv" id="listViewContents">
{/strip}