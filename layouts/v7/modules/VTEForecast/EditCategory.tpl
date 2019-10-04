{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row">
        <h3>{vtranslate($QUALIFIED_MODULE, 'VTEForecast')}</h3>
    </div>
    <hr>
    <div class="row form-group">
        <div class="col-lg-12 btn-toolbar">
            <h3>VTEForecast Category</h3>
        </div>
    </div>
    <div class="row form-group">
        <label class="col-lg-2">Name:</label>
        <div class="col-lg-3">        
            <input type="textbox" name="category_name" id="category_name" value="{$CATEGORY.name}" class="form-control" />
            <input type="hidden" name="category_id" id="category_id" value="{$CATEGORY.id}" />
        </div>
        <div class="col-lg-3">    
            &nbsp; Target:
            <input type="checkbox" name="category_is_target" id="category_is_target" {if ($CATEGORY.is_target == 1)}checked="checked" {/if}>
        </div>
    </div>
    <div class="row form-group">
        <label class="col-lg-2">Color column:</label>
        <div class="col-lg-3">
            <div class="input-group salestage_color" style="min-width: 50px;">
                <input id="category_color" type="text" value="{$CATEGORY.color}" class="form-control" />
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
        <table class="table table-hover">
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
        <div class="text-center">
            <button class="btn btn-success" id="btnSave" type="button">{vtranslate('LBL_SAVE','VTEForecast')}</button>
            <a class="btn btn-default" href="?module=VTEForecast&parent=Settings&view=Settings" type="reset">Cancel</a>
        </div>
    </form>
    <link rel="stylesheet" href="layouts/v7/modules/VTEForecast/resources/colorpicker/css/bootstrap-colorpicker.css" type="text/css" media="screen" />
    <script type="text/javascript" src="layouts/v7/modules/VTEForecast/resources/colorpicker/js/bootstrap-colorpicker.js"></script>

</div>
	