<div class='modal-dialog modal-lg'>
	<div class = "modal-content">
		{assign var=TITLE value="{vtranslate('PandaDoc Tokens',$MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
        <div class='modal-body'>
        	<div class="table-responsive" style="max-height: 400px;overflow-x: hidden;">
	        	<table class="table table-bordered">
	        		<tr>
	        			<th> Token </th>
	        			<th> Value </th>
	        		</tr>
					{foreach item=TOKEN key=KEY from=$TOKENS}
						<tr>
							<td>[{$KEY}]</td>
							<td>{$TOKEN}</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
</div>