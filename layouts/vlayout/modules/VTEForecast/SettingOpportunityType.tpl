{strip}
    <h3>{vtranslate('Opportunity Type Mapping', 'VTEForecast')}</h3>
    <table class="table">
        <tr><th>{vtranslate('Target Category', 'VTEForecast')}</th><th>{vtranslate('Opportunity Type', 'VTEForecast')}</th><th>#</th></tr>
        {foreach item=ITEM_CAT from=$CONFIG_CATS_OPPT}
            <tr style="background-color: {$ITEM_CAT.color}">
                <td>{$ITEM_CAT.name}</td>
                <td>
                    {assign var=STAGE value=$ITEM_CAT.sss|json_decode:1}
                    {foreach from=$STAGE item=entry}
                        <span style="padding:3px; margin-right:5px; border:1px solid #DDD;background-color:#DDD">
													{$entry}
												</span>
                    {/foreach}

                </td>
                <td>
                    <a href="index.php?module=VTEForecast&parent=Settings&view=EditCategoryOpptType&id={$ITEM_CAT.id}" data-id="" title="{vtranslate('LBL_EDIT_CATEGORY', 'VTEForecast')}"><span class="icon-pencil"></span></a>
                    &nbsp;<a data-id="{$ITEM_CAT.id}" class="deleteoppt" href="javascript:;" title="{vtranslate('LBL_DELETE', 'VTEForecast')}"><span class="icon-trash"></span>
                </td>
            </tr>
        {/foreach}
    </table>
    <div><a href="index.php?module=VTEForecast&parent=Settings&view=EditCategoryOpptType&id=0" class="btn" data-id="" title="">{vtranslate('LBL_ADD_TARGET_CATEGORY', 'VTEForecast')}</a></div>
{/strip}