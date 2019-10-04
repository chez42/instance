<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form id="dcm_module_setting" action="" enctype="multipart/form-data" method="POST" onsubmit="saveModuleSetting(this); return false;">
            <div class="modal-header">
                {*<span style="padding: 0 10px;">{vtranslate('SELECT_THE_MODULES', $MODULE_LBL)}</span>*}
                <div class="clearfix">
                    <div class="pull-right ">
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class="fa fa-close"></span>
                        </button>
                    </div>
                    <h4 class="pull-left">{vtranslate('SELECT_THE_MODULES', $MODULE_LBL)}</h4>
                </div>
            </div>
            <div class="dcm_module_setting_container modal-body" >
                <input type="hidden" name="module" value="DuplicateCheckMerge" />
                <input type="hidden" name="action" value="Settings" />
                <input type="hidden" name="task" value="saveModuleSetting" />
                <div class="dupecheck-module-setting clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 select-modules" style="text-align: center; padding: 5px;">
                    {foreach item=ENTITY_MODULE from=$ENTITY_MODULES}
                        <div class="col-lg-3 col-md-3 col-sm-3 select-module-cel" style="text-align: center;">
                            <input type="checkbox" name="dcm_module_setting[]" {if $ENTITY_MODULE.active eq 1}checked{/if} value="{$ENTITY_MODULE.name}" />
                            <label>{$ENTITY_MODULE.tablabel}</label>
                        </div>
                    {/foreach}
                    </div>
                </div>
            </div>
            <div class="modal-footer clearfix">
                <button class="btn btn-success" type="submit"><strong>{vtranslate('SAVE_BTN', $MODULE_LBL)}</strong></button>
            </div>
        </form>
    </div>
</div>