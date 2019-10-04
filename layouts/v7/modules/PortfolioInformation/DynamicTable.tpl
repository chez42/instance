{assign var='DYNAHEADINGS' value=$DYNATABLE['table_headings']}
{assign var='DYNAROWS' value=$DYNATABLE['table_values']['rows']}
{assign var='DYNARULES' value=$DYNATABLE['table_values']['rules']}
{assign var='DYNACATEGORIES' value=$DYNATABLE['table_categories']}
{assign var='DYNATOTALS' value=$DYNATABLE['TableTotals']}

<table class="holdings_report DynaTable">
    <thead>
        <tr>
            {foreach from=$DYNAHEADINGS key=k item=heading}
                <td style="display:block;{$heading['heading_td_style']}"><span style="{$heading['heading_span_style']}">{$heading['heading']}</span></td>
            {/foreach}
        </tr>
    </thead>
    <tbody>
        {foreach from=$DYNACATEGORIES item=CatArray}
        {counter start=1 print=false assign='count' name='category_count'}
        {counter start=0 print=false assign='indenting' name='category_indenting'}
            {foreach from=$CatArray key=k item=cat}
                {if $k neq 'category_id'}
                    {if $cat != ''}
                    <tr>
                        {assign var='INDENTING' value=$count * 2}
                        <td>{for $x=0 to $INDENTING}&nbsp;{/for}<strong>{$cat}</strong></td>
                        <td colspan="{$DYNAHEADINGS|@count}-1">&nbsp;</td>
                    </tr>
                    {/if}
                    {foreach from=$DYNAROWS item=r}
                        {foreach from=$r key=index item=row}
                            {if $index eq 'fields' AND $r['category_id'] eq $CatArray['category_id'] AND $count eq $CatArray|@count-1}
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
                                            <span style="{$DYNARULES[$k]['value_td_style']}">{$DYNARULES[$k]['prefix']}{$v}{$DYNARULES[$k]['suffix']}</span>
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
                {foreach from=$DYNATOTALS key=k item=v}
                    {if $a eq $k}
                        {if $DYNARULES[$k]['smarty_modifier'] != ''}
                            {capture assign=test}
                                {$DYNARULES[$k]['prefix']}{ldelim}$v|{$DYNARULES[$k]['smarty_modifier']}{rdelim}{$DYNARULES[$k]['suffix']}
                            {/capture}
                            <td style="{$DYNARULES[$k]['value_td_style']}">
                                {eval var=$test}
                            </td>
                        {else}
                            <td style="{$DYNARULES[$k]['value_td_style']}">
                                <span style="{$DYNARULES[$k]['value_td_style']}">{$DYNARULES[$k]['prefix']}{$v}{$DYNARULES[$k]['suffix']}</span>
                            </td>
                        {/if}
                    {else}
                        <td>&nbsp;</td>
                    {/if}
                {/foreach}
        {/foreach}
        </tr>
    </tbody>
</table>