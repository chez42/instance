<div style = "float:left;margin:15px;padding:10px;border-radius:5px;background-color:white;width:100%;height:500px;">
	
	<div id="dateselection" class = "col-md-12" style = "margin-bottom:10px;">
		<div class = "col-md-8">
			<table class="dateselectiontable">
				<tr>
					<td>
						<input type="text" id="select_end_date" value="{$END_DATE}" class = "inputElement"/>
					</td>
					<td>
						<input type="button" class = "btn btn-info" id="calculate_report" value="Calculate" style = "margin-left:10px;" />
					</td>
				</tr>
			</table>
		</div>
	</div>
			
	<iframe class="viewerDownload" id='viewer' src="libraries/jquery/pdfjs/web/viewer.html" height="100%" width="100%"></iframe>
	
</div>		

	{literal}
		<script>
			function b64toBlob(b64Data, contentType='', sliceSize=512){
				const byteCharacters = atob(b64Data);
				const byteArrays = [];

				for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
					const slice = byteCharacters.slice(offset, offset + sliceSize);

					const byteNumbers = new Array(slice.length);
					for (let i = 0; i < slice.length; i++) {
						byteNumbers[i] = slice.charCodeAt(i);
					}
	
					const byteArray = new Uint8Array(byteNumbers);
					byteArrays.push(byteArray);
				}

				const blob = new Blob(byteArrays, {type: contentType});
				
				return blob;
			}
		
			const blob = b64toBlob('{/literal}{$BLOB_CONTENT}{literal}', 'application/pdf');
			
			var url = URL.createObjectURL(blob);
			
			$("#viewer").attr("src","libraries/jquery/pdfjs/web/viewer.html?zoom=page-width&file="+url+"#zoom=100");
			
			jQuery(document).ready(function($) {
				
				$("#calculate_report").click(function(e){
					e.stopImmediatePropagation();
					$("#calculate").submit();
				});
				
				$("#select_end_date").datepicker({
					format: '{/literal}{$USER_DATE_FORMAT}{literal}',
				});
			});	
		</script>
	{/literal}	