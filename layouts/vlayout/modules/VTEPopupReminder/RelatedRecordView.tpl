{if $TABNO eq 0}
    {assign var="TABNO" value=''}
{else}
    {assign var="TABNO" value=$TABNO+1}
{/if}
<div class="tab-pane" id="module_{$MODULE_LABEL}{$TABNO}" data-tabno="{$TABNO}">
    <script>
        selected_module.push('{$MODULE_LABEL}{$TABNO}');
    </script>
    <div class="row-fluid referenceField">
    <form class="form-horizontal recordEditView" id="module_{$MODULE_LABEL}{$TABNO}_Fields" name="module_{$MODULE_LABEL}{$TABNO}_Fields" method="post" action="index.php">
        {include file="modules/CalendarPopup/EditViewBlocks.tpl" RECORD_STRUCTURE=$RECORD_STRUCTURE_MODEL->getStructure() MODULE=$MODULE_LABEL RECORD_STRUCTURE_MODEL=$RECORD_STRUCTURE_MODEL}
    </form>
</div>