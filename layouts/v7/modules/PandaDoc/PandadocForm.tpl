<style>
    iframe {
       width: 100%; 
       height: 500px;
    }
</style>

<div class='fc-overlay-modal overlayDetail'>
	<div class = "modal-content">
		{assign var=TITLE value="{vtranslate('PandaDoc Document',$MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
        <div class='modal-body'>
			<div id="pandadoc-sdk" class="pandadoc"></div>
		</div>
	</div>
</div>
