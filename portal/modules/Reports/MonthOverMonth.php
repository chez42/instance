<?php

$YEARS = $data['YEARS'];
$DOW_PRICES = $data['DOW_PRICES'];
$MOM_TABLE = $data['MOM_TABLE'];

?>

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o "style="color: #44b6ae!important;"></i>
		<?php echo Language::translate("Month Over Month"); ?>
	</h1>
</div>
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="row">
				  	<style>
					  .blue_header {
                            width: 100%;
                            background-color: #33256C;
                            text-align: center;
                            color: white;
                        }
                    </style>
                    <h2 class="blue_header padded_heading">TRAILING MONTH TO MONTH YEARLY INCOME</h2>
                    <table  class="table table-bordered hodings_summary" style="width:100%" border="1" id="month_over_month">
                        <thead>
                        <tr>
                            <th style="text-align:center;">Year</th>
                            <th style="text-align:center;">January</th>
                            <th style="text-align:center;">February</th>
                            <th style="text-align:center;">March</th>
                            <th style="text-align:center;">April</th>
                            <th style="text-align:center;">May</th>
                            <th style="text-align:center;">June</th>
                            <th style="text-align:center;">July</th>
                            <th style="text-align:center;">August</th>
                            <th style="text-align:center;">September</th>
                            <th style="text-align:center;">October</th>
                            <th style="text-align:center;">November</th>
                            <th style="text-align:center;">December</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($YEARS as $k=>$v){?>
                                <tr>
                                    <td style="font-weight:bold; border-top:2px solid black;">DOW</td>
                                    <?php for($x=1;$x<=12;$x++){?>
                                       <?php if($DOW_PRICES[$v][$x]['close'] == 0){?>
                                            <td style="text-align:center; border-top:2px solid black;">-</td>
                                        <?php }else{?>
                                            <td style="text-align:right; border-top:2px solid black;"><?php echo number_format($DOW_PRICES[$v][$x]['close'])?></td>
                                        <?php }?>
                                    <?php }?>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold"><?php echo $v ?></td>
                                    <?php for($x=1;$x<=12;$x++){
                                        $SET_TD = 0;
                                        foreach ($MOM_TABLE as $mk =>$mv){
                                            if ($mv['year'] == $v && $mv['month'] == $x){
                                                $SET_TD = 1;?>
                                                <td style="text-align:right;">$<?php echo number_format($mv['monthovermonth']) ?></td>
                                        <?php }
                                    }
                                    if ($SET_TD == 0){?>
                                                <td style="text-align:center;">-</td>
                                  <?php     }
                                        $SET_TD = 0;
                                     }?>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
				</div>
			</div>
			
		</form>
	</div>
</div>

