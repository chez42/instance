<div class="AllocationTypesWrapper">
    <h2 style="width:100%; background-color:lightblue; text-align:center;font-size:25px;padding-top:2px;padding-bottom:2px;">Asset Allocation By Type</h2>
	
	<div id="sector_pie_holder" class="sector_pie_holder" style="height: 450px; "></div>
	
	<table class="table table-bordered" style="padding-top:30px; width:100%; font-size:16pt;">
		<thead>
		<tr>
			<th style="font-weight:bold; background-color:RGB(245, 245, 245);">Description</th>
			<th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Weight</th>
			<th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Value</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
			<tr style="background-color:{$heading['color']};">
				<td style="color:white;">{$heading['title']}</td>
				<td style="text-align:right; color:white;">{$heading['percentage']}%</td>
				<td style="text-align:right; color:white;">${$heading['value']|number_format:2:".":","}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
