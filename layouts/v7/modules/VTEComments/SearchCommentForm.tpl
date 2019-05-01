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
                    <h4 class="pull-left">{vtranslate('Search Comments', $QUALIFIED_MODULE)}</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group otherFilters">
                    <label class="control-label fieldLabel col-sm-2">{vtranslate('Comments', $QUALIFIED_MODULE)}:</label>
                    <div class="controls col-sm-10">
                        <input name="commentcontent" class="inputElement" type="text" value="{$VTECOMMENT_FILTERS['commentcontent']}" />
                    </div>
                </div>
                <div class="form-group otherFilters">
                    <label class="control-label fieldLabel col-sm-2">{vtranslate('Users', $QUALIFIED_MODULE)}:</label>
                    <div class="controls col-sm-10">
                        {include file="modules/VTEComments/uitypes/OwnerFieldTaskSearchView.tpl" FIELD_MODEL=$OWNER_FIELD}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label fieldLabel col-sm-2">{vtranslate('Date', $QUALIFIED_MODULE)}:</label>
                    <div id="taskManagementContainer" class="controls col-sm-10">
                        <div class="btn-group dateFilters pull-left">
                            <button type="button" class="btn btn-default {if $VTECOMMENT_FILTERS['date'] eq "all"}active{/if}" data-filtermode="all">{vtranslate('LBL_ALL', $QUALIFIED_MODULE)}</button>
                            <button type="button" class="btn btn-default {if $VTECOMMENT_FILTERS['date'] eq "today"}active{/if}" data-filtermode="today">{vtranslate('LBL_TODAY', $QUALIFIED_MODULE)}</button>
                            <button type="button" class="btn btn-default {if $VTECOMMENT_FILTERS['date'] eq "thisweek"}active{/if}" data-filtermode="thisweek">{vtranslate('LBL_THIS_WEEK', $QUALIFIED_MODULE)}</button>
                            <button type="button" class="btn btn-default dateRange dateField" data-calendar-type="range" data-filtermode="range"><i class="fa fa-calendar"></i></button>
                            <button type="button" class="btn btn-default hide rangeDisplay">
                                <span class="selectedRange"></span>&nbsp;
                                <i class="fa fa-times clearRange"></i>
                            </button>
                        </div>
                    </div>
                </div>
                {foreach item =FIELD_NAME from=$ADD_FIELDS}
                    <div class="form-group otherFilters">
                        {assign var=FIELD_MODEL value=$ALL_FIELDS.$FIELD_NAME}
                        <label class="control-label fieldLabel col-sm-2">{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}:</label>
                        <div class="controls col-sm-10">
                            {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
                            {assign var=DEFAULT_VALUE value=$VTECOMMENT_FILTERS[$FIELD_NAME]}
                            {if $DEFAULT_VALUE}
                                {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue', $DEFAULT_VALUE)}
                            {/if}
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) }
                        </div>
                    </div>
                {/foreach}
            </div>
            <div class="modal-footer">
                <button name='saveButton' class="btn btn-success saveButton" data-dismiss="modal" aria-hidden="true" >{vtranslate('LBL_SEARCH', $QUALIFIED_MODULE)}</button>
            </div>
        </form>
    </div>
</div>