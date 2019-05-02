{*<table class="collaptable">
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Description</th>
    </tr>
    <tr data-id="1" data-parent="">
        <td>1</td>
        <td>Element 1</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="2" data-parent="1">
        <td>1.1</td>
        <td>Element 2</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="3" data-parent="1">
        <td>1.2</td>
        <td>Element 3</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="6" data-parent="3">
        <td>1.2.1</td>
        <td>Element 6</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="7" data-parent="3">
        <td>1.2.2</td>
        <td>Element 7</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="8" data-parent="3">
        <td>1.2.3</td>
        <td>Element 8</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="4" data-parent="">
        <td>2</td>
        <td>Element 4</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
    <tr data-id="5" data-parent="4">
        <td>2.1</td>
        <td>Element 5</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua.</td>
    </tr>
</table>*}

<div class="GainLossReport">
    <div class="row-fluid ReportTitle detailViewTitle">
        <div class=" span12 ">
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"><span>Positions</span>&nbsp;</span>
                    </div>
                </div>
                <div class="span6">
                    <div class="pull-right">
                        <div class="btn-toolbar">
							<span class="btn-group">
								<button class="btn ExportReport" name="print_pdf"><strong>Generate PDF</strong></button>
							</span>
                        </div>
                        <form method="post" id="export">
                            <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
                            <input type="hidden" value="PortfolioInformation" name="module" />
                            <input type="hidden" value="GainLoss" name="view" />
                            <input type="hidden" value="" name="pie_image" id="pie_image" />
                            <input type="hidden" value="1" name="pdf" />
                            <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                            <input type="hidden" value="{$END_DATE}" name="report_end_date" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detailViewInfo row-fluid">
        <div class="contents">
            <div class="row-fluid">
                {assign var='DYNATABLE' value=$COMPARISON_TABLE}
                {assign var='DYNAHEADINGS' value=$DYNATABLE['table_headings']}
                {assign var='DYNAROWS' value=$DYNATABLE['table_values']['rows']}
                {assign var='DYNARULES' value=$DYNATABLE['table_values']['rules']}
                {assign var='DYNACATEGORIES' value=$DYNATABLE['table_categories']}
                {assign var='DYNATOTALS' value=$DYNATABLE['TableTotals']}
                {assign var='COUNTER' value=1}
                {assign var='PARENT_ID' value=1}
                <table class="table table-bordered DynaTable table-collapse GainLossTable">
                    <thead>
                    <tr>
                        {foreach from=$DYNAHEADINGS key=k item=heading}
                            {if $heading['hidden'] neq 1}
                                <th style="{$heading['heading_td_style']}"><span style="{$heading['heading_span_style']}">{$heading['heading']}</span></th>
                            {/if}
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody>
                    {assign var=CatCount value='0'}
                    {foreach from=$DYNACATEGORIES item=CatArray}
                        {counter start=1 print=false assign='count' name='category_count'}
                        {counter start=0 print=false assign='indenting' name='category_indenting'}
                        {foreach from=$CatArray key=k item=cat}
                            {if $k neq 'category_id' AND $k neq 'totals'}
                                {assign var=CatCount value=$CatCount+1}
                                {if $cat != ''}
                                    <tr data-id="{$COUNTER}" data-parent id="asset_cat_{$CatCount}" data-target=".asset_cat_{$CatCount}">
                                        <td><i class="icon-plus"></i>&nbsp;<strong>{$cat}</strong></td>
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
                                        {assign var='PARENT_ID' value=$COUNTER}
                                        {assign var='COUNTER' value=$COUNTER+1}
                                    </tr>
                                {/if}
                                {foreach from=$DYNAROWS item=r}
                                    {foreach from=$r key=index item=row}
                                        {if $index eq 'fields' AND $r['category_id'] eq $CatArray['category_id'] }
                                            <tr data-id='{$COUNTER}' data-parent='{$PARENT_ID}' class="positions asset_cat_{$CatCount} {foreach from=$row key=k item=v}{if $k eq 'system_generated' AND $v eq 1}system_generated_transaction{/if}{/foreach}">
                                                {foreach from=$row key=k item=v}
                                                    {if $DYNARULES[$k]['smarty_modifier'] != ''}
                                                        {capture assign=test}
                                                            {$DYNARULES[$k]['prefix']}{ldelim}$v|{$DYNARULES[$k]['smarty_modifier']}{rdelim}{$DYNARULES[$k]['suffix']}
                                                        {/capture}
                                                        <td style="{$DYNARULES[$k]['value_td_style']}">
                                                            {eval var=$test}
                                                        </td>
                                                    {else}
                                                        <td style="{$DYNARULES[$k]['value_td_style']}" {$DYNARULES[$k]['html_td_modifiers']} {if $DYNARULES[$k]['value_as_data']|count_characters > 1}{$DYNARULES[$k]['value_as_data']}={$v}{/if}>
                                                            <span style="{$DYNARULES[$k]['value_span_style']}">{$DYNARULES[$k]['prefix']}{$v}{$DYNARULES[$k]['suffix']}</span>
                                                        </td>
                                                    {/if}
                                                {/foreach}
                                            {assign var='COUNTER' value=$COUNTER+1}
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/foreach}
                            {/if}
                            {counter name='category_count'}
                        {/foreach}
                    {/foreach}
                    <tr data-id="{$COUNTER}" data-parent>
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
                        {assign var='COUNTER' value=$COUNTER+1}
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>