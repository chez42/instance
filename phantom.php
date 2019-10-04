<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-04
 * Time: 11:18 AM
 */
include("includes/main/WebUI.php");
include("libraries/phantom/cPhantom.php");

$request = new Vtiger_Request($_REQUEST, $_REQUEST);
$request->set("module", "PortfolioInformation");

$phantom = new cPhantom();
if($phantom->ConfirmUserAndPassword($request->get('username'), $request->get('pword'))){
    $chart = new PortfolioInformation_GetChartValues_Action();
    $chart_data = $chart->process($request);

    if(is_array($chart_data))
        $request->set('chart_data', $chart_data);
    switch($request->get("chart_style")){
        case "pie":
            $view = new PortfolioInformation_PhantomPie_View();
            echo $view->process($request);
    }
}else{
    echo "Username and Password failure";
}