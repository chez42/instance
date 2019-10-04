{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

{foreach key=index item=cssModel from=$STYLES}
	<link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td class="span5">
                <table  width="100%">
                    <tr>
                        <td>
                            <div class="dashboardTitle textOverflowEllipsis" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}" style="max-width: 280px;">
                                <b>{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}</b>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <div style="margin-bottom: 4px;">
                                <button type='button' class='btn' onclick='window.location.href = "{$detailViewUrl}";'><strong>{vtranslate('LBL_VIEW_DETAILS',"VTEReports")}</strong></button>
                            </div>
                        </td>
                    </tr>
                </table>
			</td>
		</tr>
	</tbody>
</table>
