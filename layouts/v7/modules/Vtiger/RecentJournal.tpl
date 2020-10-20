{strip}
	<div class="widget_contents" style="padding:0px !important;margin-bottom:0px !important;">
		<div class="activities">
			<div class="streamline b-accent">
				{if count($RECENT_ACTIVITIES) neq '0'}
					{foreach item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
	
						<div class="sl-item b-info">
							<div class='row'>
									
								<div class='col-lg-7 col-md-7 col-sm-7 col-xs-7' style="margin-left:20px;">
									<div class="summaryViewEntries" style="font-weight:bold;font-size: 16px;">
										{wordwrap(html_entity_decode($RECENT_ACTIVITY->get('subject')),75,"<br>\n")}
									</div>
									
									<span title="{$RECENT_ACTIVITY->get('createddate')}">{$RECENT_ACTIVITY->get('createddate')|date_format:"%A, %B %e, %Y"}</span>
								</div>
	
								<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4 ' style='padding-right:30px;'>
									<div class="row">
										<div class="pull-right">
											<strong>
												<span class="value">
													{if $RECENT_ACTIVITY->get('module') eq 'Calendar'}
														{vtranslate('Events', $RECENT_ACTIVITY->get('module'))}
													{else}
														{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}
													{/if}
												</span>
											</strong>&nbsp&nbsp;
										</div>
									</div>
								</div>
							</div>
						<hr>
					</div>
				{/foreach}
				
			{else}
				<div class="summaryWidgetContainer noContent">
					<p class="textAlignCenter">{vtranslate('No data available.',$MODULE_NAME)}</p>
				</div>
			{/if}
			{if $PAGING_MODEL->isNextPageExists()}
				<div class="row">
					<div class="pull-right">
						<a href="javascript:void(0)" class="moreRecentJournals">{vtranslate('LBL_SHOW_MORE',$MODULE_NAME)}</a>
					</div>
				</div>
			{/if}
			</div>
		</div>
	</div>
{/strip}