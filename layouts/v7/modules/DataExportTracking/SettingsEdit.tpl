{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
<div class="container-fluid">
	<div class="contents row-fluid">
		{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
		{*<form id="DataExportTracking" class="form-horizontal" method="POST">*}
			<div class="widget_header row-fluid">
				<div class="span8"><h3>{vtranslate('DataExportTracking', $QUALIFIED_MODULE)}</h3></div>
				<div class="span4 btn-toolbar"><div class="pull-right">
					<button class="btn btn-success" id="btn_save"  title="{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
					<a href="index.php?module=DataExportTracking&parent=Settings&view=Settings" class="cancelLink" title="{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
				</div></div>
			</div>
			<hr>
	
			<input type="hidden" name="default" value="false" />
			<input type="hidden" name="id" id="setting_id" value="{$MODEL['id']}" />
			<table class="table table-bordered table-condensed themeTableColor">
				<thead>
					<tr class="blockHeader"><th colspan="2" class="{$WIDTHTYPE}">{vtranslate('LBL_DATA_EXPORT_TRACKING', $QUALIFIED_MODULE)}</th></tr>
				</thead>
				<tbody>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_LISTVIEW_EXPORTS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
							<input type="checkbox" id="track_listview_exports" {if $MODEL['track_listview_exports']}checked{/if}/>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_REPORT_EXPORTS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
							<input type="checkbox" id="track_report_exports" {if $MODEL['track_report_exports']}checked{/if}/>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_SCHEDULED_REPORTS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
							<input type="checkbox" id="track_scheduled_reports" {if $MODEL['track_scheduled_reports']}checked{/if}/>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_COPY_RECORDS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
							<input type="checkbox" id="track_copy_records" {if $MODEL['track_copy_records']}checked{/if}/>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_NOTIFICATION_EMAIL', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
							<input type="email" id="notification_email" value="{$MODEL['notification_email']}" />
						</td>
					</tr>
					
				</tbody>
			</table>		
		{*</form>*}
	</div>
</div>
{/strip}