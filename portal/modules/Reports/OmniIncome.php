<?php


$START_MONTH = $data['START_MONTH'];
$END_MONTH = $data['END_MONTH'];
$DYNAMIC_GRAPH = $data['DYNAMIC_GRAPH'];
$MONTHLY_TOTALS = $data['MONTHLY_TOTALS'];
$COMBINED_SYMBOLS = $data['COMBINED_SYMBOLS'];
$YEAR_END_TOTALS = $data['YEAR_END_TOTALS'];
$GRAND_TOTAL = $data['GRAND_TOTAL'];

echo "<input type='hidden' value='".$DYNAMIC_GRAPH."' id='estimate_graph_values' />";
    
?>
<style>	
	#gh2report_chart_table .borderless td, 
	#gh2report_chart_table .borderless>thead>tr>th {
		border: 0;
	}
</style>
<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		Omni Income
	</h1>
</div>

<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="row">
					<h1 style="text-align:center;"><?php echo $START_MONTH.' - '.$END_MONTH;?></h1>
                   <div class="col-md-1"></div>
					<div class="col-md-10">
						<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="height: 350px;"></div>
					</div>	
					<div class="col-md-1"></div>	
				</div>
				<div class="row">
					<table id="income_combined" class="collap_income table table-bordered">
                        <thead>
                        <tr>
                            <th style="font-weight:bold;">Symbol</th>
                            <th style="font-weight:bold;">Name</th>
                            <?php foreach($MONTHLY_TOTALS as $v){?>
                                <th style="font-weight:bold; text-align:center;"><?php echo $v->month;?><br /><?php echo $v->year;?></th>
                            <?php }?>
                            <th style="font-weight:bold; text-align:center;">Total</th>
                        </tr>
                        <thead>
                        <tbody>
                        <?php foreach($COMBINED_SYMBOLS as $symbol=>$symbol_values){?>
                            <tr>
                                <td style="width:10%;"><?php echo $symbol;?></td>
                                <td style="width:10%;"><?php echo $symbol_values[0]->security_name;?></td>
                                    <?php foreach ($MONTHLY_TOTALS as $ym){?>
                                        <td style="text-align:right;">
                                        <?php     $found = 0;
                                        foreach($symbol_values as $v){
                                            if ($ym->year == $v->year && $ym->month == $v->month){
                                                echo '$'.number_format($v->amount,0,".",",");
                                                $found = 1;
                                            }
                                        }
                                        if ($found != 1){
                                            echo '-';
                                        }?>
                                        </td>
                                    <?php }?>
                                <td style="text-align:right;">$<?php echo number_format($YEAR_END_TOTALS[$symbol],0,".",",");?></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        <?php foreach($MONTHLY_TOTALS as $v){?>
                            <td style="font-weight:bold; text-align:right;">$<?php echo number_format($v->monthly_total,0,".",",");?></td>
                        <?php }?>
                            <td style="font-weight:bold; text-align:right;">$<?php echo number_format($GRAND_TOTAL,0,".",",");?></td>
                        </tr>
                        </tbody>
                    </table>
				</div>
			</div>
		</form>
	</div>
</div>
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
