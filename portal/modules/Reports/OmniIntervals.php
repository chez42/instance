<?php

echo "<input type='hidden' name='account_numbers' id='account_numbers' value='".$data['ACCOUNT_NUMBERS']."' />";

?>

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		Omni Intervals
	</h1>
</div>

<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
    <div class="m-portlet__padding-x">
    </div>
	<div class="m-wizard__head m-portlet__padding-x">	
		<div class="m-wizard__nav">
			<div class="m-wizard__steps">
				<div class="m-wizard__step m-wizard__step--current" m-wizard-target="m_wizard_form_step_1">
				</div> 
				<div class="m-wizard__step" m-wizard-target="m_wizard_form_step_2">
				</div>
			</div>
		</div>
	</div>
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
					<div class="row">
                       <div class="col-md-1"></div>
						<div class="col-md-10">
							<div id="chartdiv" style="width:100%; height: 500px; font-size: 11px;"></div>
						</div>	
						<div class="col-md-1"></div>	
					</div>
				</div>
				<div class="m-wizard__form-step" id="m_wizard_form_step_2">
					<div class="row">
						<?php if(count($data['INTERVALS']) > 0){?>
                            <p><strong>Disclaimer: </strong>This page is currently in alpha testing and values may not have an accurate representation of the account</p>
                            <table style="width:100%;">
                                <tr style="width:100%">
                                    <td style="width:80%">
                                        <table id="IntervalTable" border="1px solid black;" style="width:100%;">
                                            <thead>
                                            <tr>
                                                <td style="text-align:center; padding:2px;">Account Number</td>
                                                <td style="text-align:center; padding:2px;">Begin Date</td>
                                                <td style="text-align:center; padding:2px;">End Date</td>
                                                <td style="text-align:center; padding:2px;">Begin Value</td>
                                                <td style="text-align:center; padding:2px;">Deposits / Withdrawals</td>
                                                <td style="text-align:center; padding:2px;">Investment Return</td>
                                                <td style="text-align:center; padding:2px;" class="end_value">End Value</td>
                                                <td style="text-align:center; padding:2px;">Period Return %</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach( $data['INTERVALS'] as $v ){?>
                                                <tr>
                                                    <td style="padding:2px;"><?php echo $v['account_number'];?></td>
                                                    <td style="padding:2px;"><?php echo $v['begin_date'];?></td>
                                                    <td style="padding:2px;" class="end_date"><?php echo $v['end_date'];?></td>
                                                    <td style="text-align:right; padding:2px;">$<?php echo number_format($v['begin_value'],2,".",",");?></td>
                                                    <td style="text-align:right; padding:2px;">$<?php echo number_format($v['net_flow'],2,".",",");?></td>
                                                    <td style="text-align:right; padding:2px;">$<?php echo number_format($v['investment_return'],2,".",",");?></td>
                                                    <td style="text-align:right; padding:2px;">$<?php echo number_format($v['end_value'],2,".",",");?></td>
                        
                                                    <td style="text-align:right; padding:2px;" class="period_return" data-period_return='<?php echo number_format($v['period_return'],2,".",",");?>'><?php echo number_format($v['period_return'],2,".",",");?>%</td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width:100%; vertical-align:top; text-align:center;">
                                        <h2 style="font-size:12px;">Calculated Return</h2>
                                        <p style="font-weight:bold; font-size:16px;" class="calculated_return"></p>
                                    </td>
                                </tr>
                            </table>
                        <?php }else{?>
                            <h2>Sorry, there are no Intervals available currently</h2>
                        <?php }?>
					</div>
				</div>
				
			</div>
			<div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
				<div class="m-form__actions m-form__actions">
					<div class="row">
						<div class="col-lg-12 m--align-right">
							<a href="javascript:;" class="btn btn-lg default previous" style="background-color:#e1e5ec;" data-wizard-action="prev">
                				<i class="la la-arrow-circle-left " style="color: white;font-size: 2.5rem;"></i>
                			</a>
                			<a href="javascript:;" class="btn btn-lg green next" style="background-color:#32c5d2;" data-wizard-action="next">
                				<i class="la la-arrow-circle-right " style="color: white;font-size: 2.5rem;"></i>
                			</a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<link href="assets/global/plugins/amcharts/amstockcharts/plugins/export/export.css" rel="stylesheet" type="text/css" />
<link href="assets/global/plugins/shield/css/shield_all.min.css" rel="stylesheet" type="text/css" />

<script src="assets/global/plugins/amcharts/amstockcharts/amcharts.js" type="text/javascript"></script>
<script src="assets/global/plugins/amcharts/amstockcharts/serial.js" type="text/javascript"></script>
<script src="assets/global/plugins/amcharts/amstockcharts/themes/light.js" type="text/javascript"></script>
<script src="assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
<script src="assets/global/plugins/amcharts/amstockcharts/plugins/dataloader/dataloader.js" type="text/javascript"></script>
<script src="assets/global/plugins/amcharts/amstockcharts/plugins/export/export.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/shield/shieldui-all.min.js" type="text/javascript"></script>

<script src="js/Reports/Intervals.js" type="text/javascript"></script>
<script type="text/javascript">
    var WizardDemo=function(){
    	jQuery(document).find("#m_wizard");
    	var e,r,i=jQuery(document).find("#m_form");
    	return{
    		init:function(){
    			var n;jQuery(document).find("#m_wizard"),
    			i=jQuery(document).find("#m_form"),
    			(r=new mWizard("m_wizard",{startStep:1})).on("beforeNext",function(r){}),
    			r.on("change",function(e){mUtil.scrollTop()}),
    			r.on("change",function(e){1===e.getStep()})
    		}
    	}
    }();
    jQuery(document).ready(function(){WizardDemo.init()});
</script>
