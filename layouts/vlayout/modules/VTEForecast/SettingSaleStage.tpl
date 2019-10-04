{strip}
    <h3>{vtranslate('Forecast Category and Sales Stage Mapping', 'VTEForecast')}</h3>
    <table class="table">
        <tr><th>{vtranslate('Forecast Category', 'VTEForecast')}</th><th>{vtranslate('Is Target', 'VTEForecast')}</th><th>{vtranslate('Sales Stage', 'VTEForecast')}</th><th>#</th></tr>
        {foreach item=ITEM_CAT from=$CONFIG_CATS}
            <tr style="background-color: {$ITEM_CAT.color}">
                <td>{$ITEM_CAT.name}</td>
                <td>{if $ITEM_CAT.is_target eq '1'}yes{else}no{/if}</td>
                <td>
                    {assign var=STAGE value=$ITEM_CAT.sss|json_decode:1}
                    {foreach from=$STAGE item=entry}
                        <span style="padding:3px; margin-right:5px; border:1px solid #DDD;background-color:#DDD">
													{$entry}
												</span>
                    {/foreach}

                </td>
                <td>
                    <a href="index.php?module=VTEForecast&parent=Settings&view=EditCategory&id={$ITEM_CAT.id}" data-id="" title="{vtranslate('LBL_EDIT_CATEGORY', 'VTEForecast')}"><span class="icon-pencil"></span></a>
                    &nbsp;<a data-id="{$ITEM_CAT.id}" class="delete" href="javascript:;" title="{vtranslate('LBL_DELETE', 'VTEForecast')}"><span class="icon-trash"></span>
                </td>
            </tr>
        {/foreach}
    </table>
    <div><a href="index.php?module=VTEForecast&parent=Settings&view=EditCategory&id=0" class="btn" data-id="" title="">{vtranslate('LBL_ADD_CATEGORY', 'VTEForecast')}</a></div>
{/strip}