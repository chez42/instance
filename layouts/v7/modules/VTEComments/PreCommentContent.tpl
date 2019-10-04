<div class="modal-dialog createFieldModal modelContainer ">
    <div class="modal-content">
        <form class="form-horizontal createCustomFieldForm form-modalSearchComment" >
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right ">
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class="fa fa-close"></span>
                        </button>
                    </div>
                    <h4 class="pull-left">{vtranslate('View Previous Version', $QUALIFIED_MODULE)}</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group" style="height: 300px;overflow: auto;scroll-behavior: auto;">
                    <div class="summaryWidgetContainer" style="margin: 0 20px; height: auto;">
                    {foreach from=$COMMENTCONTENTS item=COMMENT}
                        <div class="comment" >
                            <span class="creatorName">{$COMMENT.whodid}</span>
                            <small>{vtranslate('LBL_COMMENT','ModComments')} {strtolower(vtranslate('LBL_MODIFIED','ModComments'))}</small>
                            <span class="commentTime text-muted cursorDefault"><small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT.changedon)}">{Vtiger_Util_Helper::formatDateDiffInStrings($COMMENT.changedon)}</small></span>
                            <div class="commentInfoContentBlock" >
                              <span class="commentInfoContent">{$COMMENT.prevalue|unescape:'html'}</span>
                            </div>
                            <hr class="clearfix" style="margin-top:0; margin-bottom: 10px;">
                        </div>
                    {/foreach}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>