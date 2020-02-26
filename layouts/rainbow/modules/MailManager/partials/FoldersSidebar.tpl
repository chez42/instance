<div id="modules-menu" class="modules-menu mmModulesMenu" style="width: 100%;">
        <div>
            <span><i class="material-icons">email</i>&nbsp;{$MAILBOX->username()}</span><br/><br/>
            <div class="btn-group">
                <span class="cursorPointer mailbox_refresh CountBadge btn btn-success btn-sm" title="{vtranslate('LBL_Refresh', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('LBL_Refresh', $MODULE)}" data-tippy aria-describedby="tippy-1">
                    <i class="material-icons">refresh</i>
                </span>
                 
                <span class="cursorPointer mailbox_setting CountBadge btn btn-info btn-sm" title="{vtranslate('JSLBL_Settings', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('JSLBL_Settings', $MODULE)}" data-tippy aria-describedby="tippy-2">
                    <i class="material-icons">settings</i> 
                </span>
                <span id="mail_compose" class="btn btn-danger cursorPointer btn-sm" title="{vtranslate('LBL_Compose', $MODULE)}" tippytitle data-toggle="toolstip" data-original-title="{vtranslate('LBL_Compose', $MODULE)}" data-tippy aria-describedby="tippy-3">
            <i class="material-icons">create</i> 
                </span>
                
            </div>
        <div id='folders_list'></div>
        </div>
</div>