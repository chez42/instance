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
				<div class="span8">
					<h3 style="text-align: center;">{vtranslate('DataExportTracking', $QUALIFIED_MODULE)}</h3>
				</div>
				<div class="span4 btn-toolbar"><div class="pull-right">
					<button id="btn_edit" class="btn btn-success" type="submit" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</strong></button>
				</div></div>
			</div>
			<hr>
	
			<input type="hidden" name="default" value="false" />
			<input type="hidden" name="id" value="{$MODEL['id']}" />
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
                            <span>
                                {if $MODEL['track_listview_exports']}
                                    {vtranslate('LBL_YES', $QUALIFIED_MODULE)}
                                {else}
                                    {vtranslate('LBL_NO', $QUALIFIED_MODULE)}
                                {/if}
                            </span>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_REPORT_EXPORTS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
                             <span>
                                {if $MODEL['track_report_exports']}
                                    {vtranslate('LBL_YES', $QUALIFIED_MODULE)}
                                {else}
                                    {vtranslate('LBL_NO', $QUALIFIED_MODULE)}
                                {/if}
                            </span>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_SCHEDULED_REPORTS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
                             <span>
                                {if $MODEL['track_scheduled_reports']}
                                    {vtranslate('LBL_YES', $QUALIFIED_MODULE)}
                                {else}
                                    {vtranslate('LBL_NO', $QUALIFIED_MODULE)}
                                {/if}
                            </span>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_TRACK_COPY_RECORDS', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
                            <span>
                                {if $MODEL['track_copy_records']}
                                    {vtranslate('LBL_YES', $QUALIFIED_MODULE)}
                                {else}
                                    {vtranslate('LBL_NO', $QUALIFIED_MODULE)}
                                {/if}
                            </span>
						</td>
					</tr>
					<tr>
						<td width="20%" class="{$WIDTHTYPE}">
							<label class="muted pull-right marginRight10px">{vtranslate('LBL_NOTIFICATION_EMAIL', $QUALIFIED_MODULE)}</label>
						</td>
						<td class="{$WIDTHTYPE}" style="border-left: none;">
                            <span>
                                {$MODEL['notification_email']}
                            </span>
						</td>
					</tr>
					
				</tbody>
			</table>
            <div class="row-fluid">
                <div class="span12 btn-toolbar">
                    <div class="pull-right">
                        <button id="btn_back" class="btn btn-success" type="submit" title="{vtranslate('LBL_BACK', $QUALIFIED_MODULE)}"><strong>{vtranslate('Show tracking data', $QUALIFIED_MODULE)}</strong></button>
                    </div>
                </div>
            </div>
		{*</form>*}
	</div>
</div>
{/strip}