<input type="hidden" id="rep_code_data" value={$REP_CODES} />
<input type="hidden" id="history_data" value={$HISTORY} />

<div id="downloader_wrapper">
{*    <div id="filter_wrapper">
        <table id="filter_table">
            <tr>
                <td><input type="text" id="capitalization_filter" placeholder="All" class="comboTreeInputBox" /></td>
                <td><input type="text" id="style_filter" placeholder="All" class="comboTreeInputBox" /></td>
                <td><input type="text" id="international_filter" placeholder="All" class="comboTreeInputBox" /></td>
                <td><input type="text" id="sector_filter" placeholder="All" class="comboTreeInputBox" /></td>
                <td><input type="text" id="asset_class_filter" placeholder="All" class="comboTreeInputBox" /></td>
            </tr>
        </table>
    </div>*}
    <table id="downloader_list" border="1">
        <thead>
        <tr>
            <td>Date</td>
            {foreach item=i from=$REP_CODES}
                <td>{$i}</td>
            {/foreach}
        </tr>
        </thead>
        <tbody>
        {*FOREACH DATE....*}
        {foreach key=k item=d from=$DATES}
            <tr>
                <td>{$d|date_format:"%A - %B %e, %Y"}</td>
                {foreach item=rc from=$REP_CODES}
                <td>
                        {foreach item=v from=$HISTORY}
                            {if $v.rep_code eq $rc AND $v.copy_date eq $d}
                                {$v.filename}
                            {/if}
                        {/foreach}
                </td>
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>