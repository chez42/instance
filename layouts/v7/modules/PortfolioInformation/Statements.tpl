<input type="hidden" id="capitalization_data" value={$CAPITALIZATION} />
<input type="hidden" id="style_data" value={$STYLE} />
<input type="hidden" id="international_data" value={$INTERNATIONAL} />
<input type="hidden" id="sector_data" value={$SECTOR} />
<input type="hidden" id="asset_class_data" value={$ACLASS} />
<input type="hidden" id="user_preferences" value={$PREFERENCES} />

<div id="preface">
    <p>When printing reports, the Prepared by section is determined based on the owning advisor.  If that owner is a person, then the report uses
    their Name, Title, Email, and Phone Number.  If the owner is a group</p>
</div>

<div id="statements_wrapper">
    <div id="selections">
        <select class="select2">
            <optgroup label="Active Users">
                <option data-id="{$USER->get('id')}" selected>{$USER->get('first_name')} {$USER->get('last_name')}</option>
            </optgroup>
            <optgroup label="Groups">
                {foreach from=$GROUPS key=k item=v}
                    <option data-id="{$k}">{$v}</option>
                {/foreach}
            </optgroup>
        </select>
    </div>
    <div id="statement_editing">
        <div id="left_side">
             <textarea name="prepared_by" id="prepared_by" rows="10" cols="80">
                {if $PREPARED_BY eq null}
                    {$USER->get('first_name')} {$USER->get('last_name')}<br>
                    {if $USER->get('title') neq ''}{$USER->get('title')}<br>{/if}
                    {if $USER->get('email1') neq ''}{$USER->get('email1')}<br>{/if}
                    {if $USER->get('phone_work') neq ''}{$USER->get('phone_work')}{/if}
                {else}
                    {$PREPARED_BY}
                {/if}
             </textarea>
        </div>
        <div id="right_side">
            <h4>Preview</h4>
             <div id="statement_preview">
                 {if $PREPARED_BY eq null}
                     {$USER->get('first_name')} {$USER->get('last_name')}<br>
                     {if $USER->get('title') neq ''}{$USER->get('title')}<br>{/if}
                     {if $USER->get('email1') neq ''}{$USER->get('email1')}<br>{/if}
                     {if $USER->get('phone_work') neq ''}{$USER->get('phone_work')}{/if}
                 {else}
                     {$FORMATTED_PREPARED_BY}
                 {/if}
             </div>
        </div>
    </div>
</div>