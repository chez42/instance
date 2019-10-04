<fieldset>
    <legend style="margin: 0 !important;"><span style="padding: 0 10px;">{vtranslate('SELECT_THE_MODULES', $MODULE_LBL)}</span></legend>
<div class="dcm_module_setting_container" style="padding: 20px; width: 980px;">
    <form id="dcm_module_setting" action="" enctype="multipart/form-data" method="POST" onsubmit="saveModuleSetting(this); return false;">
        <input type="hidden" name="module" value="DuplicateCheckMerge" />
        <input type="hidden" name="action" value="Settings" />
        <input type="hidden" name="task" value="saveModuleSetting" />
        <div class="dupecheck-module-setting row-fluid">
            <div class="span12 select-modules" style="text-align: center; padding: 5px;">
            {foreach item=ENTITY_MODULE from=$ENTITY_MODULES}
                <div class="span2 select-module-cel" style="text-align: center;">
                    <input type="checkbox" name="dcm_module_setting[]" {if $ENTITY_MODULE.active eq 1}checked{/if} value="{$ENTITY_MODULE.name}" />
                    <label>{$ENTITY_MODULE.tablabel}</label>
                </div>
            {/foreach}
            </div>
            <div class="span12 dcm-save-btn" style="text-align: center; margin: 30px 0 0;">
                <button class="btn btn-success" type="submit"><strong>{vtranslate('SAVE_BTN', $MODULE_LBL)}</strong></button>
            </div>
        </div>
    </form>
</div>
</fieldset>