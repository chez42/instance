var Reports_Detail_Js = {

	registerDataTableEvent : function(){
		var DatatablesBasicBasic={
			init:function(){
				var e;(e=$("#responsiveReportsDataTables")).DataTable({
					"dom": "<'table-responsive't><'row'<'col-md-12 col-sm-12'<'pull-right'p>r>>",
					"pageLength": 10,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"url": "portalAjax.php",
						"type":"POST",
						"data" : function ( d ){
							return $.extend( {}, d, {
								"module" : "Reports",
								"action" : "getReportAccounts",
								"viewAccountDetail" : jQuery("#allowed_account_detail").val(),
								"show_report" : jQuery("#show_report").val(),
							});
						}
					},
					"columnDefs": [
						{ className: "text-right", "targets": [ 3,4,5 ] }
					],
					'deferRender': true,
					'footerCallback': function( tfoot, data, start, end, display ) {    
						
						var response = this.api().ajax.json();
						
						if(typeof response == 'undefined')return;
						
						if(response.footerData){
							var footerValue = response.footerData;
							var $th = $(tfoot).find('th');
							$th.eq(0).html('Totals');
							$th.eq(1).html('');
							$th.eq(2).html('');
							$th.eq(3).html("$"+footerValue['total_value']);
							$th.eq(4).html("$"+footerValue['market_value']);
							$th.eq(5).html("$"+footerValue['cash_value']);
							$th.eq(6).html('');
						}
					} 
				})
			}
		};
		jQuery(document).ready(function(){DatatablesBasicBasic.init()});
		
	},
		
	registerEvents : function(){
		this.registerDataTableEvent();
	}
};

jQuery("document").ready(function(){
	Reports_Detail_Js.registerEvents();	 
});