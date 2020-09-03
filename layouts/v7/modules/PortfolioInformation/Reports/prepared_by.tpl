{*This is the prepared by block*}
<div class="ReportDraggable">
    <div class="PreparedBy">
        {if $PREPARED_BY.pre_formatted eq 1}
            <p id="PreparedByName"><span class="prepared_by">Prepared By<br /></span>{$PREPARED_BY.content}</p>
        {else}
            <p id="PreparedByName"><span class="prepared_by">Prepared By<br /></span>{$PREPARED_BY.content}</p>
        {/if}
    </div>
</div>