<input type="hidden" id="capitalization_data" value={$CAPITALIZATION} />
<input type="hidden" id="style_data" value={$STYLE} />
<input type="hidden" id="international_data" value={$INTERNATIONAL} />
<input type="hidden" id="sector_data" value={$SECTOR} />
<input type="hidden" id="asset_class_data" value={$ACLASS} />
<input type="hidden" id="user_preferences" value={$PREFERENCES} />

<div id="indexes_wrapper">
    <div id="filter_wrapper">
        <table id="filter_table">
            <tr>
{*                <td><input type="text" id="capitalization_filter" placeholder="All Capitalization" class="comboTreeInputBox" /></td>
                <td><input type="text" id="style_filter" placeholder="All Styles" class="comboTreeInputBox" /></td>
                <td><input type="text" id="international_filter" placeholder="All International" class="comboTreeInputBox" /></td>
                <td><input type="text" id="sector_filter" placeholder="All Sectors" class="comboTreeInputBox" /></td>*}
                <td><input type="text" id="asset_class_filter" placeholder="All Asset Classes" class="comboTreeInputBox" /></td>
            </tr>
        </table>
    </div>
    {foreach key=index item=GROUPED_SYMBOLS from=$LIST}
        <table class="index_list">
            <thead>
                <tr>
                    <td>Symbol</td>
                    <td>Description</td>
{*                    <td>Capitalization</td>
                    <td>Style</td>
                    <td>International</td>
                    <td>Sector</td>*}
                    <td>Base Asset Class</td>
                </tr>
            </thead>
            <tbody>
            {foreach key=index item=value from=$GROUPED_SYMBOLS}
                <tr data-id="{$value.symbol_id}" data-enabled="1">
                    <td>{$value.security_symbol}</td>
                    <td>{$value.description}</td>
{*                    <td class="capitalization_filter" data-capitalization_filter="{$value.capitalization}">{$value.capitalization}</td>
                    <td class="style_filter" data-style_filter="{$value.style}">{$value.style}</td>
                    <td class="international_filter" data-international_filter="{$value.international}">{$value.international}</td>
                    <td class="sector_filter" data-sector_filter="{$value.sector}">{$value.sector}</td>*}
                    <td class="asset_class_filter" data-asset_class_filter="{$value.base_asset_class}">{$value.base_asset_class}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {/foreach}
</div>