{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($QUALIFIED_MODULE, 'VTEForecast')}</h3>
    </div>
    <hr>
    <div class="row-fluid">
        <div class="span12 btn-toolbar">
		<h1>{$ROOT_RECORD->getName()}</h1>		
		{if $ROOT_RECORD->getDepth() > 0}
			{assign var="PARENT" value=$ROOT_RECORD->getParent()}
			(<a href="{$PARENT->getCreateChildUrl()}">{vtranslate('Parent', 'VTEForecast')}:{$PARENT->getName()}</a>)
		{/if}
		
        </div>        
    </div>
	<hr> 
	
	     <form class="form-horizontal" action="index.php" method="post" name="AddChildren" id="AddChildren">
                <input type="hidden" name="parrent_id" id="parrent_id" value="{$ROOT_RECORD->getId()}" />
			<table class="table  table-bordered table-condensed ">
				<tr>
					<th>#</th>
					<th>{vtranslate('Id','VTEForecast')}</th>
					<th>{vtranslate('Name','VTEForecast')}</th>			
					<th>{vtranslate('Title','VTEForecast')}</th>			
					<th>{vtranslate('Role','VTEForecast')}</th>			
					<th>{vtranslate('Email','VTEForecast')}</th>
					<th>{vtranslate('Office Phone','VTEForecast')}</th>
					<th>{vtranslate('Reports To','VTEForecast')}</th>
				</tr>
				{foreach from=$RECORDS item=RECORD}
				<tr>
					<td>
						<input type="checkbox" name="chk" value="{$RECORD.id}" {if ($RECORD.isChecked)}checked{/if}/>					
					</td>
					<td>{$RECORD.id}</td>					
					<td>
					{$RECORD.name}
					{if ($RECORD.isChecked||$RECORD.isDisabled)}							
						&nbsp;<a href="{$RECORD.objRecord->getCreateChildUrl()}" data-url="{$RECORD.objRecord->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', 'VTEForecast')}"><span class="icon-plus-sign"></span></a>		
					{/if}
					</td>
					<td>
						{$RECORD.title}
					</td>	
					<td>
						{$RECORD.rolename}
					</td>	
					<td>
						{$RECORD.email}
					</td>	
					<td>
						{$RECORD.phone_work}
					</td>	
					<td>
                        {$RECORD.reports_to_name}
					</td>	
					
				</tr>
				{/foreach}
			</table>
			</br>
			<div class="textAlignCenter">
				<button class="btn btn-success" id="btnSave" type="button">{vtranslate('LBL_SAVE','VTEForecast')}</button>
				<a class="cancelLink" href="?module=VTEForecast&parent=Settings&view=Settings" type="reset">Cancel</a>
			</div>
        </form>		
	
</div>
	