<?php

//require_once("modules/Portfolios/classes/cReturn.php");

function GetInvestmentReturnColumn($transaction_handler, $pids, $sdate, $edate, $accounts = '')
{
$start_total = $transaction_handler->GetSymbolTotals($sdate);
$start_value = $transaction_handler->AddAllSymbolTotals($start_total);

$end_total = $transaction_handler->GetSymbolTotals($edate);
$end_value = $transaction_handler->AddAllSymbolTotals($end_total);

$short_start = $transaction_handler->GetShortValueFromSymbolTotals($start_total);
$short_end = $transaction_handler->GetShortValueFromSymbolTotals($end_total);

$cover_start = $transaction_handler->GetCoverValueFromSymbolTotals($start_total);
$cover_end = $transaction_handler->GetCoverValueFromSymbolTotals($end_total);

$end_value -= ($short_end);
$ev = $end_value;
//$calculated_short = abs($short_start) - abs($short_end);
$calculated_short = $short_start - $short_end;
$calculated_cover = $cover_start - $cover_end;

$end_value += $calculated_short + $calculated_cover;

if($accounts)
    $accounts = " AND account_number IN ({$accounts}) ";

$contributions = $transaction_handler->GetContributions(null, $sdate, $edate);
$shorts = $transaction_handler->GetShorts($null, $sdate, $edate);
$covers = $transaction_handler->GetCovers($null, $sdate, $edate);
$buys = $transaction_handler->GetBuys($null, $sdate, $edate);
$sells = $transaction_handler->GetSells($null, $sdate, $edate);
$withdrawals = $transaction_handler->GetWithdrawals(null, $sdate, $edate);
$transaction_handler->FilterTransfers($contributions, $withdrawals);
$dividends = $transaction_handler->GetDividends(null, $sdate, $edate);
$income = $transaction_handler->GetIncome(null, $sdate, $edate);
$interest = $transaction_handler->GetInterest(null, $sdate, $edate);
$expenses = $transaction_handler->GetExpenses(null, $sdate, $edate);
$management_fee = $transaction_handler->GetSummedManagementFees(null, $sdate, $edate);
$con = $transaction_handler->AddContributions($contributions);
$wit = $transaction_handler->AddWithdrawals($withdrawals);
$sho = $transaction_handler->AddShorts($shorts);
$co = $transaction_handler->AddCovers($covers);
$buy = $transaction_handler->AddBuys($buys);
$sell = $transaction_handler->AddSells($sells);

$net_contributions = $transaction_handler->GetNetContributions($con, $wit);
$net_contributions += ($buy + $sell);
$net_contributions += $sho + $co;

//$t = $con - $wit + $sho;  28925.76
//echo "CON:{$con} - {$wit} + SHO:{$sho} = " . $t . "<br />";

$inc = $transaction_handler->AddIncome($income);
$exp = $transaction_handler->AddExpenses($expenses);
$div = $transaction_handler->AddDividends($dividends);
$int = $transaction_handler->AddInterest($interest);
$net_income = $transaction_handler->GetNetIncome($inc, $exp);
$other_expenses = $exp - $management_fee;

$total_return_percentage = $transaction_handler->GetInvestmentReturnPercentage($start_value, $end_value, $inc);
$investment_return = $transaction_handler->GetInvestmentReturn($start_value, $end_value, $net_contributions);
$capital_appreciation = $transaction_handler->GetCapitalAppreciation($end_value, $start_value, $net_contributions, $net_income);

$other_income = $inc - $div - $int;
$results = array("total_return_percentage" => $total_return_percentage,
                 "investment_return" => $investment_return,
                 "start_value" => $start_value,
                 "net_contributions" => $net_contributions,
                 "net_income" => $net_income,
                 "capital_appreciation" => $capital_appreciation,
                 "end_value" => $ev,
                 "contributions" => $con,
                 "shorts" => $calculated_short,
                 "withdrawals" => $wit,
                 "expenses" => $exp,
                 "management_fee" => $management_fee,
                 "other_expenses" => $other_expenses,
                 "dividends" => $div,
                 "interest" => $int,
                 "other_income" => $other_income,
                 "income" => $inc,
                 "investment_return" => $investment_return);

return $results;
/*
echo "TOTAL INCOME: {$inc}<br />"; 
echo "INVESTMENT RETURN PERCENTAGE: {$total_return_percentage}<br />";
echo "INVESTMENT RETURN: {$investment_return}<br />";
//        $net = $transaction_handler->GetNetContributions($contributions, $withdrawals);
echo "<br /><br /><strong>SO FAR WE GET:</strong><br />";
echo "<strong>Beginning Value:</strong> {$start_value}<br />";
echo "<strong>Net Contributions:</strong> {$net_contributions}<br />";
echo "<strong>Income &amp; Expenses:</strong> {$net_income}<br />";
echo "<strong>Capital Appreciation:</strong> {$capital_appreciation}<br />";
echo "<strong>Ending Value:</strong> {$end_value}<br />";
echo "<strong>Contributions:</strong> {$con}<br />";
echo "<strong>Withdrawals:</strong> {$wit}<br />";
echo "<strong>Expenses:</strong> {$exp}<br />";
echo "<strong>Dividends:</strong> {$div}<br />";
echo "<strong>Interest:</strong> {$int}<br />";
echo "<strong>Investment Return:</strong> {$investment_return}<br />";*/

}
?>
