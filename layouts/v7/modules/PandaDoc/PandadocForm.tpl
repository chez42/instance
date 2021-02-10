<style>
    iframe {
       width: 100%; 
       height: 800px;
    }
    .overlayDetail .modal-body {
     	height: 90% !important;
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
