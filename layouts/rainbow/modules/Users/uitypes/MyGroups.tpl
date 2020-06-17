{assign var=GROUPS value=$RECORD->getRelatedGroupsInformation()}
{if $GROUPS|@count gt 0}
</div>
<table style="width:100%!important;margin:10px!important;">
	<tr>
		<th>#</th>
            <th>Group Name</th>
            <th>Description</th>
	</tr>
	{foreach from=$GROUPS key=Index item=GROUP}
		<tr>
			<td>{$Index+1}</td>
			<td>{$GROUP['name']}</td>
			<td>{$GROUP['description']}</td>
		</tr>
	{/foreach}
</table>
{/if}
