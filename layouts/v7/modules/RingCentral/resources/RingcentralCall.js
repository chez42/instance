jQuery.Class("RingcentralCall_Js", {

	webPhone: null,
	
	session: null,
	
	from_no : '',
	
	app_key : '',
	
	connected: false,

}, {
   
	init : function(){
		
		var thisInstance = this;
	
		$.getScript('modules/RingCentral/resources/sip.js', function(){
			
			$.getScript('modules/RingCentral/resources/ringcentral-web-phone.js', function(){
				
				thisInstance.ValidateTokenAndGetSIP();
				
			});
			
		});
		
		$(document).on('click', '.number', function(){
			$("#phoneScreen").val($("#phoneScreen").val() + $(this).attr("value"));
			$("#phoneScreen").focus();
		});
		
		$(document).on('keyup',"#phoneScreen", function(e){ 
			var code = e.which;
			if(code==13){
				if($("#phoneScreen").val().trim() != ''){
					thisInstance.Class.session.dtmf( $("#phoneScreen").val().trim() );
				}
			}
		});
		
		$(document).on('click',".clear", function(e){ 
			$("#phoneScreen").val('');
		});
		
		$(document).on('click','.sendnotes',function(){
			
			var recordId = $(this).data('id');
			
			var comment = $(document).find('[name="callnotes"]').val();
			
			if(comment){
				
				var url = 'index.php?module=RingCentral&record='+recordId+'&action=GetRecordDetails&mode=addComment&comment='+comment;
				
				$.ajax({
					url: url,
					success: function(result){
						if (result.success) {
							thisInstance.hangUp();
							thisInstance.Class.webPhone.userAgent.unregister();
						}
					}
				});
				
			} else {
				thisInstance.hangUp();
				thisInstance.Class.webPhone.userAgent.unregister();
			}
			
		});
	
	},
	
	hangUp: function(){
		this.Class.session.terminate();
	},
	
	ValidateTokenAndGetSIP: function(){
		
		var thisInstance = this;
		
		var url = 'index.php?module=RingCentral&action=ValidateTokenAndGetSIP';
		
		$.ajax({
			url: url,
			success: function(result){
				var data = result.result;
				if(result.success) {
					
					thisInstance.Class.connected = true;
					thisInstance.Class.from_no = data.from_no;
					thisInstance.Class.app_key = data.client_id;
					
					if(data.sip){
						thisInstance.initializeSIP(data.sip);
					}
					
				} else {
					thisInstance.Class.connected = false;	
				}
				
			}
		});
		
	},
	
	initializeSIP: function(sipInfo){
		
		var thisInstance = this;
		
		var remoteVideoElement = document.getElementById('remoteVideo');
		
		var localVideoElement = document.getElementById('localVideo');
			
		var site_url = $(document).find('[name="site_url"]').val();
		
		thisInstance.Class.webPhone = new RingCentral.WebPhone(sipInfo, {
			
			appKey: thisInstance.Class.app_key,
			
			logLevel: parseInt(3, 10),
			
			audioHelper: {
				enabled: true,
				incoming: 'modules/RingCentral/resources/incoming.ogg',
				outgoing: 'modules/RingCentral/resources/outgoing.ogg'
			},
			
			media: {
                remote: remoteVideoElement,
                local: localVideoElement
            },
            
		});
					
		thisInstance.Class.webPhone.userAgent.audioHelper.setVolume(0.5);
		
		thisInstance.Class.webPhone.userAgent.on('unregistered', function() {
            window.close();
        });
			
		var number = $(document).find('[name="number"]').val();
		
		if(number.length > 10){
			number = '+' + number;
		}
		
		thisInstance.Class.session = thisInstance.Class.webPhone.userAgent.invite(number, {
			fromNumber: thisInstance.Class.from_no,
			homeCountryId: 1
		});
		
	},
	
});

jQuery(document).ready(function(e) {
   new RingcentralCall_Js();
})