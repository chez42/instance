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
        <div class="span8 btn-toolbar">
		<h3>{vtranslate('VTEForecast Category', 'VTEForecast')}</h3><br>
            {vtranslate('Name', 'VTEForecast')}:
		<input type="textbox" name="category_name" id="category_name" value="{$CATEGORY.name}" />
		<input type="hidden" name="category_id" id="category_id" value="{$CATEGORY.id}" />
        &nbsp; {vtranslate('Target', 'VTEForecast')}:
        <input type="checkbox" name="category_is_target" id="category_is_target" {if ($CATEGORY.is_target == 1)}checked="checked" {/if}>


        <div class="input-group salestage_color">
            {vtranslate('Color column', 'VTEForecast')}:
            <input id="category_color" type="text" value="{$CATEGORY.color}" class="form-control" style="width: 60px;" />
                <span class="input-group-addon">
                {if ($CATEGORY.color == '')}
                    <i style="background-color: red;border: 1px solid #CCC;border-radius: 10px;"></i>
                {else}
                    <i style="background-color: {$CATEGORY.color};border: 1px solid #CCC;border-radius: 10px;"></i>
                {/if}
                </span>
        </div>
        <script>
            $(function(){
                $('.salestage_color').colorpicker();
            });
        </script>
        </div>
    </div>
	<hr> 	
	     <form class="form-horizontal" action="index.php" method="post" name="EditCategory" id="EditCategory">                
			<table class="table table-bordered table-condensed ">
				<tr>
					<th>#</th>					
					<th>{vtranslate('Sales Stage','VTEForecast')}</th>			
				</tr>
				{foreach from=$SALE_STAGES item=STAGE}
				<tr>
					<td>
						<input type="checkbox" name="chk" value="{$STAGE->name}" {if ($STAGE->checked)}checked{/if}/>					
					</td>
					<td>{$STAGE->name}</td>
					
				</tr>
				{/foreach}
			</table>
			</br>
			<div class="textAlignCenter">
				<button class="btn btn-success" id="btnSave" type="button">{vtranslate('LBL_SAVE','VTEForecast')}</button>
				<a class="cancelLink" href="?module=VTEForecast&parent=Settings&view=Settings" type="reset">Cancel</a>
			</div>
        </form>
    <link rel="stylesheet" href="layouts/vlayout/modules/VTEForecast/resources/colorpicker/css/bootstrap-colorpicker.css" type="text/css" media="screen" />
    <script type="text/javascript" src="layouts/vlayout/modules/VTEForecast/resources/colorpicker/js/bootstrap-colorpicker.js"></script>

</div>
	