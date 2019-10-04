<div class="PortfolioInformationMonthlyIncomeReport">

	<input type="hidden" name="history_chart" value='{$HISTORY_DATA}' />
	<input type="hidden" name="future_chart" value='{$FUTURE_DATA}' />

	<div class="row-fluid ReportTitle detailViewTitle">
		<div class=" span12 ">
			<div class="row-fluid">
				<div class="span6">
					<span class="row-fluid">
						<h4 class="recordspan pushDown"><span>Monthly Income - <a href="index.php?module={$LINK_MODULE}&parenttab=Sales&action=DetailView&record={$ACCOUNTNUMBER}">{$ACCOUNTNAME}</a></span></h4>
					</span>
					<span class="row-fluid"><span class="title_span">As of: {$DATE}</span></span>
				</div>
				<div class="span6">
					<div class="pull-right">
						<form method="post" action="index.php?module=PortfolioInformation&action=PrintReport&report_type=monthly_income&{$ACCOUNT}&calling_record={$CALLING_RECORD}">
        					<input type="submit" name="print_pdf" class="btn" value="Print PDF" />
    					</form>
					</div>
				</div>
    		</div>
    	</div>
    </div>
    
    <div class="detailViewInfo row-fluid">
		<div class="contents">
			<div class="row-fluid">
			
				<div class="idealsteps-container">
					<nav class="idealsteps-nav"></nav>
					<div class="monthly_reports_idealforms">
						<div class="idealsteps-wrap"> 
		
							<section class="idealsteps-step">
								<div class="row-fluid">
									<div class="span2">&nbsp;</div>
									<div class="span8">
										<div id="history_chart" style="height: 400px;"></div>
									</div>
									<div class="span2">&nbsp;</div>
								</div>
							</section>
							
							<section class="idealsteps-step">
								<div class="row-fluid marginBottom10px">
									<h3>Trailing 12 Monthly Income</h3>
								</div>
								<div class="span12" style="margin-left: 0px;">
									<table class="table table-bordered monthy_income_table table-collapse">
										<thead>
											<tr>
												<th rowspan="2">Symbol</th>
 												<th rowspan="2">Description</th>
											    {foreach from=$DISPLAY_MONTHS key=k item=v}
											    	<th>{$v}</th>
											    {/foreach}
    											<th rowspan="2">Total</th>
											</tr>
											<tr class="sub_heading">
												{foreach from=$DISPLAY_MONTHS key=k item=v}
											    	<th>{$DISPLAY_YEARS_CURRENT.$v}</th>
											    {/foreach}
    										</tr>
										</thead>
										<tbody>
											{assign var=MainCount value=0}
											{foreach from=$MAIN_CATEGORIES_PREVIOUS key=main_category_key item=main_category}
										        {assign var=MainCount value=$MainCount+1}
												{assign var=SubCount value=0}
												<tr data-toggle="collapse" id="main_cat_{$MainCount}" data-target=".main_cat_{$MainCount}">
					                      		    <td colspan="15"><i class="icon-plus"></i>&nbsp;<strong>{$main_category.category}</strong> -<em> ${$main_category.category_total|number_format:0}</em></td>
										        </tr>
										        {foreach from=$SUB_SUB_CATEGORIES_PREVIOUS key=sub_sub_category_key item=sub_sub_category}
										            {assign var=SubCount value=$SubCount+1}
													{if $sub_sub_category.category eq $main_category.category}
														<tr class="holdings collapse main_cat_{$MainCount}" data-toggle="collapse" id="sub_cat_{$SubCount}" data-target=".sub_cat_{$SubCount}">
										                    <td colspan="15"><i class="icon-plus"></i>&nbsp;{$sub_sub_category.sub_sub_category}<em> - ${$sub_sub_category.sub_category_total|number_format:0}</em></td>
										                </tr>
										                {foreach from=$PREVIOUS_SYMBOLS key=symbol_key item=symbol}
										                    {if $symbol.sub_sub_category eq $sub_sub_category.sub_sub_category AND $symbol.category eq $main_category.category}
															    <tr class="secondary collapse sub_cat_{$SubCount}">
										                            <td>{$symbol.symbol}</td>
										                            <td>{$symbol.description}</td>
										                            {foreach from=$DISPLAY_MONTHS key=month_key item=month}
										                                {if $PREVIOUS_SYMBOLS_VALUES[$symbol.symbol][$month].month eq $month}
										                          			<td>{$PREVIOUS_SYMBOLS_VALUES[$symbol.symbol][$month].amount|number_format:0}</td>
										                                {else}
										                                    <td>&nbsp;</td>
										                                {/if}
										                            {/foreach}
										                            <td>${$symbol.symbol_total|number_format:0}</td>
										                        </tr>
										                    {/if}
										                {/foreach}
										            {/if}
										        {/foreach}
										    {/foreach}
										    <tr>
										        <td colspan="2">&nbsp;</td>
										        {foreach from=$DISPLAY_MONTHS key=month_key item=month}
										            <td><strong>${$PREVIOUS_MONTHLY_TOTALS[$month].monthly_total|number_format:0}</strong></td>
										        {/foreach}
										        <td><strong>${$PREVIOUS_MONTHLY_TOTALS['grand_total']|number_format:0}</strong></td>
										    </tr>
										</tbody>
									</table>
								</div>
							</section>
						</div>
					</div>
				</div>								
			</div>
			
			<div class="btn-toolbar pull-right">
				<span class="btn-group pull-left">
					<button class="btn previous" id="ReportPreviousButton"><i class="icon-chevron-left"></i></button>
				</span>
				<span class="btn-group pull-right">	
					<button class="btn next" id="ReportNextButton"><i class="icon-chevron-right"></i></button>
				</span>
			</div>
			
		</div>
	</div>
</div>