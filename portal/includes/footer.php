<?php

/* * *******************************************************************************
 * The content of this file is subject to the MYC Vtiger Customer Portal license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is Proseguo s.l. - MakeYourCloud
 * Portions created by Proseguo s.l. - MakeYourCloud are Copyright(C) Proseguo s.l. - MakeYourCloud
 * All Rights Reserved.
 * ****************************************************************************** */
?>
			</div>
		</div>
	</div>
</div>		
	
	<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
	
	<script src="assets/js/login.js" type="text/javascript"></script>   
	<script src="metronic/js/scripts.bundle.js" type="text/javascript"></script>
	<?php 
		if(isset($_SESSION["ID"]) && !empty($_SESSION["ID"])){
	?>
			<script type="text/javascript">
				
				jQuery(document).find('#change_password').validate({
					rules : {
						new_password : {
							required: true,
							minlength : 5
						},
						confirm_password : {
							required: true,
							minlength : 5,
							equalTo : "#new_password"
						}
					}
				});

				$(document).ready(function(){
					$(document).find('#change_password').submit(function(e){
						e.preventDefault();
						var formData = $(this).serialize();
						if($(this).valid()){
							$.ajax({
								url:'includes/change_pass.php',
								data: formData,
								error: function(errorThrown) {
									console.log(errorThrown);
								},
								success: function(data) {
								   data = $.parseJSON(data);
								   if(data == true){
										var msg = "<?php echo $app_strings['MSG_PASSWORD_CHANGED'];?>";
										toastr.success(msg);
										jQuery('.modal').modal('hide');
								   }else{
										var msg = "<?php echo $app_strings['LBL_ENTER_VALID_USER'];?>";
										toastr.error(msg);
								   }
								},
								beforeSend: function() {
								}
							});
						}
					});
				});
				
			</script>
			 
			<script src="assets/global/scripts/app.js" type="text/javascript"></script>
			<script src="assets/js/layout.js" type="text/javascript"></script>
			
			<script src="assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
			<script src="assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
			<script src="assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
			<script src="assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
			<script src="assets/global/plugins/amcharts/amcharts/plugins/responsive/responsive.js"></script>
		  	<script src="metronic/js/vendors.bundle.js" type="text/javascript"></script>
            
            <script src="metronic/js/datatables.bundle.js" type="text/javascript"></script>
	<?php }
	?>
	<script	type="text/javascript">
        $(window).load(function() {
        	$(".se-pre-con").delay(2000).fadeOut("slow");;
        });
    </script>
</body>
</html>
