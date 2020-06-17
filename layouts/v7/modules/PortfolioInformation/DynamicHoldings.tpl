{assign var='DYNATABLE' value=$ESTIMATE_TABLE}
{assign var='DYNAHEADINGS' value=$DYNATABLE['table_headings']}
{assign var='DYNAROWS' value=$DYNATABLE['table_values']['rows']}
{assign var='DYNARULES' value=$DYNATABLE['table_values']['rules']}
{assign var='DYNACATEGORIES' value=$DYNATABLE['table_categories']}
{assign var='DYNATOTALS' value=$DYNATABLE['TableTotals']}

<table class="holdings_report DynaTable">
    <thead>
    <tr>
        {foreach from=$DYNAHEADINGS key=k item=heading}
            <td style="{$heading['heading_td_style']}"><span style="{$heading['heading_span_style']}">{$heading['heading']}</span></td>
        {/foreach}
    </tr>
    </thead>
    <tbody>
    {foreach from=$DYNACATEGORIES item=CatArray}
        {counter start=1 print=false assign='count' name='category_count'}
        {counter start=0 print=false assign='indenting' name='category_indenting'}
        {foreach from=$CatArray key=k item=cat}
            {if $k neq 'category_id' AND $k neq 'totals'}
                {if $cat != ''}
                    <tr>
                        {assign var='INDENTING' value=$count * 2}
                        <td>{for $x=0 to $INDENTING}&nbsp;{/for}<strong>{$cat}</strong></td>
                        {foreach from=$DYNAHEADINGS key=a item=heading}
                            {if $a neq 'heading'}
                                {if $DYNARULES[$a]['cat_smarty_modifier'] != ''}
                                    {capture assign=test}
                                        <span style="{$DYNARULES[$a]['cat_span_style']}">{$DYNARULES[$a]['cat_prefix']}{ldelim}$CatArray['totals'][$a]|{$DYNARULES[$a]['cat_smarty_modifier']}{rdelim}{$DYNARULES[$a]['cat_suffix']}</span>
                                    {/capture}
                                    <td style="{$DYNARULES[$a]['cat_td_style']}">
                                    {if $CatArray['totals'][$a] neq ''}
                                        {eval var=$test}
                                    {/if}
                                    </td>
                                {else}
                                    <td style="{$DYNARULES[$a]['cat_td_style']}">
                                    {if $CatArray['totals'][$a] neq ''}
                                        <span style="{$DYNARULES[$a]['cat_span_style']}">{$DYNARULES[$a]['cat_prefix']}{$CatArray['totals'][$a]}{$DYNARULES[$a]['cat_suffix']}</span>
                                    {/if}
                                    </td>
                                {/if}
                            {/if}
                        {/foreach}
{*                        <td colspan="{$DYNAHEADINGS|@count}-1">&nbsp;</td>*}
                    </tr>
                {/if}
                {foreach from=$DYNAROWS item=r}
                    {foreach from=$r key=index item=row}
                        {if $index eq 'fields' AND $r['category_id'] eq $CatArray['category_id'] AND $count eq $CatArray|@count-2}
                            <tr>
                                {foreach from=$row key=k item=v}
                                    {if $DYNARULES[$k]['smarty_modifier'] != ''}
                                        {capture assign=test}
                                            {$DYNARULES[$k]['prefix']}{ldelim}$v|{$DYNARULES[$k]['smarty_modifier']}{rdelim}{$DYNARULES[$k]['suffix']}
                                        {/capture}
                                        <td style="{$DYNARULES[$k]['value_td_style']}">
                                            {eval var=$test}
                                        </td>
                                    {else}
                                        <td style="{$DYNARULES[$k]['value_td_style']}">
                                            <span style="{$DYNARULES[$k]['value_span_style']}">{$DYNARULES[$k]['prefix']}{$v}{$DYNARULES[$k]['suffix']}</span>
                                        </td>
                                    {/if}
                                {/foreach}
                            </tr>
                        {/if}
                    {/foreach}
                {/foreach}
            {/if}
            {counter name='category_count'}
        {/foreach}
    {/foreach}
    <tr>
        {foreach from=$DYNAHEADINGS key=a item=heading}
            {assign var='VALSET' value=0}{*This is needed so we can appropriately place empty <td>'s if the total heading doesn't match the heading*}
            {foreach from=$DYNATOTALS key=k item=v}
                {if $a eq $k}
                    {if $DYNARULES[$k]['total_smarty_modifier'] != ''}
                        {capture assign=test}
                            {$DYNARULES[$k]['total_prefix']}{ldelim}$v|{$DYNARULES[$k]['total_smarty_modifier']}{rdelim}{$DYNARULES[$k]['total_suffix']}
                        {/capture}
                        <td style="{$DYNARULES[$k]['total_td_style']};">
                            {if $DYNARULES[$k]['hide_from_total'] neq 1}
                                {eval var=$test}
                            {/if}
                        </td>
                    {else}
                        <td style="{$DYNARULES[$k]['total_td_style']};">
                        {if $DYNARULES[$k]['hide_from_total'] neq 1}
                            <span style="{$DYNARULES[$k]['total_span_style']}">{$DYNARULES[$k]['total_prefix']}{$v}{$DYNARULES[$k]['total_suffix']}</span>
                        {/if}
                        </td>
                    {/if}
                    {assign var='VALSET' value=1}
                {/if}
            {/foreach}
            {if $VALSET eq 0}
                <td style="border-top:1px dotted black;">&nbsp;</td>
            {/if}
        {/foreach}
    </tr>
    </tbody>
</table>