<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * cPholdings is the class to handle all portfolio functionality
 * 
 * @author Ryan Sandnes
 */

require_once("libraries/reports/Portfolios.php");

ob_start();
//session_start();
//$_SESSION['client_organization_type_id'] = 1068;

define('DEBUG',2);
$DEBUG_BUF = '';
$LOG = '';

//require_once('exchange_types.php');
//require_once('../../core/definitions/common/coreconstants.php');
//require_once('../../core/definitions/common/autoload.php');
require_once('include/utils/utils.php');

$db = $adb;
//$db  = requireObject("MySQLDatabaseConnector");
//$dao = requireObject("CommonManager");


#@stream_wrapper_unregister('https'); 
#stream_wrapper_register('https', 'ExchangeNTLMStream') or die ("Failed");

ob_end_clean();

class cPholdings extends Portfolios {
    private $portfolio_ids;//The portfolio ID's
    private $categoryMapping;//Category mapping is the mapping from the database to the crm, used for separating items in the break down
    private $grandTotals, $subTotals, $categories, $noCategory;//The totals/subtotals and categories of the loaded household information
    private $accountinfo;//The names and numbers of the accounts used to display the totals
    public  $pricedate;
    private $projected_income;//Projected income information consists of symbol, description, and annual income as well as the total for all entities (projected_income['total'])
    private $projected_monthly_subtotals, $projected_monthly_totals;//The projected monthly totals and subtotals
    private $projected_monthly_categories;//The projected monthly categories
    private $chartSettings;//The chart settings for when it is used
    
    public function __construct() {//Constructor
        $this->categoryMapping = array('CD' => 'Fixed Income',//Map out the categories (db -> crm)
                    'Fixed' => 'Fixed Income',
                    'Cash' => 'Cash',
                    'Equity' => 'Stocks',
                    'Mutual' => 'Mutual Funds',
                    'UserDef' => 'Undefined',
                    'UnitTrust' => 'Undefined');
        $this->categories = array();
        $this->noCategory = array();//No category is the same as categories, but just holds all items without dividing them by category
        $this->portfolio_ids = array();
        $this->grandTotals = array();
        $this->subTotals = array();
        $this->pricedate = array();
        $this->accountinfo = array();
        $this->projected_income = array();
        $this->projected_monthly_subtotals = array();
        $this->projected_monthly_totals = array();
        $this->projected_monthly_categories = array();
    }
    
    public function GetValueHistory($portfolioIDs) {
        global $adb;
        $query = "SELECT 
                      date_format(interval_end_date,'%Y-%m') as date_key, 
                      date_format(interval_end_date,'%b %Y') as date_name,
                      sum(interval_end_value) AS value
                  FROM 
                      vtiger_pc_portfolio_intervals
                  WHERE 
                      portfolio_id IN ( {$portfolioIDs} )
                      AND interval_end_date >= now() - INTERVAL 13 MONTH
                      AND interval_end_date >= date_format(interval_end_date,'%Y-%m-28')
                  GROUP BY 
                      interval_end_date
                  ORDER BY 
                      interval_end_date DESC";
                      //ALTERED BELOW AND ADDED THE AND MONTH(interval.... and AND DAY(interval ...   to avoid doubling values of redundant intervals
        $query = "
            select
                date_format(interval_end_date,'%Y-%m') as date_key, 
                date_format(interval_end_date,'%b %Y') as date_name,
                sum(interval_end_value) as value
            from 
                vtiger_pc_portfolio_intervals 
            where 
                portfolio_id in ({$portfolioIDs})
                and interval_end_date >= now() - interval 13 month
                AND MONTH(interval_begin_date) != MONTH(interval_end_date)
                AND DAY(interval_end_date) >= 28
            group by 1 
            order by interval_end_date desc 
            limit 12";//This was right above "GROUP BY 1" ----    and to_days(interval_end_date) - to_days(interval_begin_date) >= 28    -----

//        echo $query . "<br />";
        $result = $adb->pquery($query, null);
//        echo $query . "<br />";
//        $data = $this->db->executeAssoc($query,array($portfolioIds));

        $valueData = array();
        foreach ($result as $k => $v) {
//            print_r($v) . "<br /><br />";
//            echo $v['date_key'] . "<br />";
            $valueData[$v['date_key']] = $v;
        }
    
        ksort($valueData);

        return $valueData;
/*
SELECT 
date_format(interval_end_date,'%Y-%m') as date_key, 
date_format(interval_end_date,'%b %Y') as date_name,
sum(interval_end_value) AS interval_end_value
FROM vtiger_pc_portfolio_intervals
WHERE portfolio_id IN ( 309, 444, 17873, 19724, 19725 )
AND interval_end_date >= now( ) - INTERVAL 13 MONTH
GROUP BY interval_end_date
ORDER BY interval_end_date DESC
 */
    }
    
    public function SetChartSettings($settings)
    {
        $this->chartSettings = $settings;
    }
    
    public function GetChartSettings()
    {
        return $this->chartSettings;
    }
    
    public function JSonEncodeChartData($type)
    {
        $firstSet = 0;
//        print_r($this->subTotals);
        foreach($this->subTotals AS $k => $v)
        {
            if($firstSet)
                $content .= ", ";
            $content .= "{title:\"{$k}\",value:{$v[$type]}}";
            $firstSet = 1;
        }
        return $content;
    }
    
    public function MakeChartData($type)
    {
/*        $content = "<chart><series>";
        $content .= "<value xid=\"143\">143</value>";
        $content .= "</series><graphs>";
        $content .= "<graph gid=\"543\" title=\"Title\">";
        $content .= "<value xid=\"111\" description=\"12\">Desc</value>";
        $content .= "</graph>";
        $content .= "</graphs></chart>";
*/
        $content = "<pie>";
        foreach($this->subTotals AS $k => $v)
        {
            $content .= "<slice title='{$k}'>{$v[$type]}</slice>";
        }
        $content .= "</pie>";
/*        $content = "<pie>";
        $content .= "<slice title='Twice a day' pull_out='true'>358</slice>";
        $content .= "<slice title='Once a day'>258</slice>";
        $content .= "<slice title='Once a week'>154</slice>";
        $content .= "</pie>";
/*
<!--
<script type="text/javascript" src="include/amcharts/amcolumn/swfobject.js"></script>
<div id="flashcontent_asset" style="height: 400px; width: 800px; margin-right: auto; margin-left: auto"></div>
<script type="text/javascript">
//
    var so = new SWFObject("include/amcharts/ampie/ampie.swf", "ampie_asset", "100%", "100%", "8", "#FFFFFF");
    so.addVariable("path", "include/amcharts/ampie/");
    so.addVariable("settings_file", escape("modules/Portfolios/ampie_settings.xml?4"));
    so.addVariable("additional_chart_settings", "<settings><legend><enabled>false</enabled></legend><pie><radius>200</radius><height>25</height><angle>45</angle></pie><data_labels><radius>60</radius></data_labels></settings>");
    ///so.addVariable("data_file", escape("ampie_data1.xml"));
    so.addVariable("chart_data", escape("<pie><slice title=\"Cash\">3839.86</slice><slice title=\"Mutual Funds\">17743.1</slice></pie>") );
    so.addVariable("preloader_color", "#999999");
    so.write("flashcontent_asset");
//
</script>
-->        
        
/*        
<pie>
  <!-- <message bg_color="#CCBB00" text_color="#FFFFFF"><![CDATA[You can broadcast any message to chart from data XML file]]></message> -->
  <slice title="Twice a day" pull_out="true">358</slice>
  <slice title="Once a day">258</slice>
  <slice title="Once a week">154</slice>
  <slice title="Never" url="http://www.interactivemaps.org" description="Click on the slice to find more information" alpha="50">114</slice>
</pie>*/
        
        return $content;
    }
    
    public function GetPortfolioAccountName($hhid)
    {
        global $adb;
        $query = "SELECT accountname 
                  FROM vtiger_account
                  WHERE accountid=?";
        $result = $adb->pquery($query,array($hhid));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, "accountname");
        else
        {
            $query = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS accountname
                      FROM vtiger_contactdetails c
                      WHERE c.contactid = ?";
            $result = $adb->pquery($query, array($hhid));
            if($adb->num_rows($result) > 0)
                return $adb->query_result($result, 0, "accountname");
        }
        
        return 0;
    }
    
    public function GetPortfolioIDsFromAccountNumber($acctnumber){
        global $adb;
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = '{$acctnumber}'";
        $result = $adb->pquery($query, array());
//        echo $query . "<br />";
        return $result;
    }
    
    function arrayUnique($array, $preserveKeys = false)  
    {  
        // Unique Array for return  
        $arrayRewrite = array();  
        // Array with the md5 hashes  
        $arrayHashes = array();  
        foreach($array as $key => $item) {  
            // Serialize the current element and create a md5 hash  
            $hash = md5(serialize($item));  
            // If the md5 didn't come up yet, add the element to  
            // to arrayRewrite, otherwise drop it  
            if (!isset($arrayHashes[$hash])) {  
                // Save the current element hash  
                $arrayHashes[$hash] = $hash;  
                // Add element to the unique Array  
                if ($preserveKeys) {  
                    $arrayRewrite[$key] = $item;  
                } else {  
                    $arrayRewrite[] = $item;  
                }  
            }  
        }  
        return $arrayRewrite;  
    }   
    
    public function GetPortfolioIDsFromContactID($id)
    {
        global $adb;
        
        $query = "select `p`.`portfolio_id` AS `portfolio_id`,
                    `vtiger_account`.`accountid` AS `account_id` 
                    FROM (((`vtiger_portfolios` `p` left join `vtiger_contactscf` `p1` on((`p`.`portfolio_tax_id` = `p1`.`ssn`))) 
                            left join `vtiger_contactdetails` on((`vtiger_contactdetails`.`contactid` = `p1`.`contactid`))) 
                            left join `vtiger_account` on((`vtiger_account`.`accountid` = `vtiger_contactdetails`.`accountid`))) 
                    WHERE ((`p`.`portfolio_tax_id` <> '') and (`p1`.`ssn` <> '')) AND vtiger_contactdetails.contactid=?";

        $result = $adb->pquery($query,array($id));
        
        $portfolio_ids = array();
        
        foreach($result AS $k => $v)
            $portfolio_ids[] = $v["portfolio_id"];
        
        return $portfolio_ids;
    }
    
    public function GetPortfolioIDsFromHHID($id)
    {
        global $adb;
/*        $query = "SELECT portfolio_id 
                  FROM v_HHtoPortfolioIDs
                  WHERE account_id=?";*/
        $query = "select `p`.`portfolio_id` AS `portfolio_id`,
                    `vtiger_account`.`accountid` AS `account_id` 
                    FROM (((`vtiger_portfolios` `p` left join `vtiger_contactscf` `p1` on((`p`.`portfolio_tax_id` = `p1`.`ssn`))) 
                            left join `vtiger_contactdetails` on((`vtiger_contactdetails`.`contactid` = `p1`.`contactid`))) 
                            left join `vtiger_account` on((`vtiger_account`.`accountid` = `vtiger_contactdetails`.`accountid`))) 
                    WHERE ((`p`.`portfolio_tax_id` <> '') and (`p1`.`ssn` <> '')) AND vtiger_account.accountid=?;";

        $result = $adb->pquery($query,array($id));
        
        $portfolio_ids = array();
        
        foreach($result AS $k => $v)
            $portfolio_ids[] = $v["portfolio_id"];
        
        return $portfolio_ids;
    }
    
    public function GetImplodedPortfolioIDsFromHHID($id)
    {
        global $adb;
        $type = GetSettypeFromID($id);
        if($type == "Contacts")
            $id = GetAccountIDFromContactID ($id);
/*        $query = "SELECT * 
                  FROM v_UserAccountPortfolioV2
                  WHERE account_id=?";*/
        $query = "select `p`.`portfolio_id` AS `portfolio_id`,
                    `vtiger_account`.`accountid` AS `account_id` 
                    FROM (((`vtiger_portfolios` `p` left join `vtiger_contactscf` `p1` on((`p`.`portfolio_tax_id` = `p1`.`ssn`))) 
                            left join `vtiger_contactdetails` on((`vtiger_contactdetails`.`contactid` = `p1`.`contactid`))) 
                            left join `vtiger_account` on((`vtiger_account`.`accountid` = `vtiger_contactdetails`.`accountid`))) 
                    WHERE ((`p`.`portfolio_tax_id` <> '') and (`p1`.`ssn` <> '')) AND vtiger_account.accountid=?;";
        $result = $adb->pquery($query,array($id));
        
        $portfolio_ids = array();
        
        foreach($result AS $k => $v)
            $portfolio_ids[] = $v["portfolio_id"];
        
        $ids = implode(", ", $portfolio_ids);//Implode the portfolio_ids for use in the query.
        return $ids;
    }
/*
 * THIS CAN ALL BE REMOVED... WAS SIMPLY USED FOR DB TRANSER.  AT THE TIME OF THIS NOTE, IT IS STILL NOT ALL DONE
create table vtigerTest.vtiger_pc_report_as_types LIKE vt530.vtiger_pc_report_as_types;
create table vtigerTest.vtiger_pc_securities LIKE vt530.vtiger_pc_securities;
create table vtigerTest.vtiger_pc_security_codes LIKE vt530.vtiger_pc_security_codes;
create table vtigerTest.vtiger_pc_security_prices LIKE vt530.vtiger_pc_security_prices;
create table vtigerTest.vtiger_pc_status_types LIKE vt530.vtiger_pc_status_types;
create table vtigerTest.vtiger_pc_transactions LIKE vt530.vtiger_pc_transactions;
create table vtigerTest.vtiger_user_portfolio_summary LIKE vt530.vtiger_user_portfolio_summary;
  
INSERT INTO vtigerTest.vtiger_pc_report_as_types SELECT * FROM vt530.vtiger_pc_report_as_types;
INSERT INTO vtigerTest.vtiger_pc_securities SELECT * FROM vt530.vtiger_pc_securities;
INSERT INTO vtigerTest.vtiger_pc_security_codes SELECT * FROM vt530.vtiger_pc_security_codes;
INSERT INTO vtigerTest.vtiger_pc_security_prices SELECT * FROM vt530.vtiger_pc_security_prices;
INSERT INTO vtigerTest.vtiger_pc_status_types SELECT * FROM vt530.vtiger_pc_status_types;
INSERT INTO vtigerTest.vtiger_pc_transactions SELECT * FROM vt530.vtiger_pc_transactions;
INSERT INTO vtigerTest.vtiger_user_portfolio_summary SELECT * FROM vt530.vtiger_user_portfolio_summary;

 */
    
    public function LoadProjectedMonthlyIncome($id, $portfolioOverride=null)
    {
        global $adb;
        if($portfolioOverride)
            $ids = SeparateArrayWithCommasAndSingleQuotes ($portfolioOverride);
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);
        //The query as created by John with a few alterations to work with vtiger
        $query ="select
                  portfolio_security_id,
                  code_description as category, 
                  security_type_name as subcategory, 
                  frequency_type_name,
                  s.security_id,
                  portfolio_security_portfolio_id as portfolio_id,
                  security_symbol, 
                  security_description,
                  security_next_dividend_date,
                  date_format(kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval),'%Y-%m') as div_month,
                  date_format(kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval),'%b') as div_month_name,
                  security_next_dividend_amount,
                 if (kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval) > now() + interval 365 day,
                    0,
                    if(security_next_dividend_date > now() or sq.i > 0,portfolio_security_quantity * security_next_dividend_amount,'[]')
                ) as payment
                from 
                  vtiger_portfolio_securities ps
                    join vtiger_securities s on portfolio_security_security_id = security_id
                        left join vtiger_security_types st on s.security_type_id = st.security_type_id
                        left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                        left join vtiger_pc_codes c on c.code_id = sc.code_id
                        left join vtiger_pc_frequency_types ft on ft.frequency_type_id = s.security_income_frequency_id
                        left join vtiger_sequence sq on sq.i <= ft.frequency_type_interval_per_year
            where portfolio_security_portfolio_id in ({$ids})
                  and portfolio_security_quantity > 0
            order by category,subcategory,security_description";
       
       $result = $adb->pquery($query, null);
       
       $categories = array();
       $subtotals = array();
       $totals = array();
       
       if($adb->num_rows($result))
       foreach($result AS $k => $v)
       {
           if (!$v['category'])//Change the category names so they group properly
               $v['category'] = $thiscategoryMapping[$v['subcategory']]; //default category for certain subcats w/o category
           if (!$v['category'])
               $v['category'] = 'NONE';
           if (!$v['subcategory'])
               $v['subcategory'] = 'NONE';
           
           $month = $v["div_month_name"];
           $symbol = $v["security_symbol"];
           if( ($v['payment'] != '[]') && ($v['payment'] != '0'))
           $categories[$v["category"]][] = array("security_symbol" => $v['security_symbol'],
                                                 "payment" => $v['payment'],
                                                 "month_name" => $v["div_month_name"],
                                                 "next_dividend_amount" => $v["security_next_dividend_amount"],
                                                 "div_month" => $v["div_month"],
                                                 "description" => $v["security_description"],
                                                 "category" => $v["category"]);
       }
       if($categories)//Get the subcategory totals/grand totals
       {
           foreach ($categories AS $k => $category)
           {
               $tmp = 0;
               foreach($category AS $key => $v)
               {
                   $subtotals[$k][$v["month_name"]] += $v['payment'];
                   $subtotals[$k]["final"] += $v["payment"];
                   $totals[$v["security_symbol"]] += $v['payment'];
                   $tmp += $subtotals[$k][$v["month_name"]];
               }
           }
       }
       
       foreach($subtotals AS $subk => $subv)
           $totals["final"] += $subv["final"];//Get the "grand total" and place it in the totals variable

       $this->projected_monthly_subtotals = $subtotals;
       $this->projected_monthly_totals = $totals;
       $this->projected_monthly_categories = $categories;
    }

    public function LoadAccountProjectedMonthlyIncome($id, $accountid, $portfolioOverride=null)
    {
        global $adb;
        if($portfolioOverride)
            $ids = SeparateArrayWithCommasAndSingleQuotes ($portfolioOverride);
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);
        
        $securities = array();
        $result = $this->GetSecuritiesByPortfolioAccountID($accountid);
        if($result)
            foreach($result AS $k => $v)
                $securities[] = $v['security_id'];
        $securities = SeparateArrayWithCommas($securities);
        
        //The query as created by John with a few alterations to work with vtiger
        $query ="select
                  portfolio_security_id,
                  code_description as category, 
                  security_type_name as subcategory, 
                  frequency_type_name,
                  s.security_id,
                  portfolio_security_portfolio_id as portfolio_id,
                  security_symbol, 
                  security_description,
                  security_next_dividend_date,
                  date_format(kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval),'%Y-%m') as div_month,
                  date_format(kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval),'%b') as div_month_name,
                  security_next_dividend_amount,
                 if (kl_date_add(security_next_dividend_date,sq.i*frequency_type_interval_multiplier,ft.frequency_type_interval) > now() + interval 365 day,
                    0,
                    if(security_next_dividend_date > now() or sq.i > 0,portfolio_security_quantity * security_next_dividend_amount,'[]')
                ) as payment
                from 
                  vtiger_portfolio_securities ps
                    join vtiger_securities s on portfolio_security_security_id = security_id
                        left join vtiger_security_types st on s.security_type_id = st.security_type_id
                        left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                        left join vtiger_pc_codes c on c.code_id = sc.code_id
                        left join vtiger_pc_frequency_types ft on ft.frequency_type_id = s.security_income_frequency_id
                        left join vtiger_sequence sq on sq.i <= ft.frequency_type_interval_per_year
            where portfolio_security_portfolio_id in ({$ids})
                  and portfolio_security_quantity > 0
                  AND s.security_id IN ({$securities})
            order by category,subcategory,security_description";
       
       $result = $adb->pquery($query, null);
       
       $categories = array();
       $subtotals = array();
       $totals = array();
       
       if($adb->num_rows($result))
       foreach($result AS $k => $v)
       {
           if (!$v['category'])//Change the category names so they group properly
               $v['category'] = $thiscategoryMapping[$v['subcategory']]; //default category for certain subcats w/o category
           if (!$v['category'])
               $v['category'] = 'NONE';
           if (!$v['subcategory'])
               $v['subcategory'] = 'NONE';
           
           $month = $v["div_month_name"];
           $symbol = $v["security_symbol"];
           if( ($v['payment'] != '[]') && ($v['payment'] != '0'))
           $categories[$v["category"]][] = array("security_symbol" => $v['security_symbol'],
                                                 "payment" => $v['payment'],
                                                 "month_name" => $v["div_month_name"],
                                                 "next_dividend_amount" => $v["security_next_dividend_amount"],
                                                 "div_month" => $v["div_month"],
                                                 "description" => $v["security_description"],
                                                 "category" => $v["category"]);
       }
       if($categories)//Get the subcategory totals/grand totals
       {
           foreach ($categories AS $k => $category)
           {
               $tmp = 0;
               foreach($category AS $key => $v)
               {
                   $subtotals[$k][$v["month_name"]] += $v['payment'];
                   $subtotals[$k]["final"] += $v["payment"];
                   $totals[$v["security_symbol"]] += $v['payment'];
                   $tmp += $subtotals[$k][$v["month_name"]];
               }
           }
       }
       
       foreach($subtotals AS $subk => $subv)
           $totals["final"] += $subv["final"];//Get the "grand total" and place it in the totals variable

       $this->projected_monthly_subtotals = $subtotals;
       $this->projected_monthly_totals = $totals;
       $this->projected_monthly_categories = $categories;
    }
    
    public function LoadProjectedIncome($id, $portfolioOverride=null)
    {
        global $adb;
        if($portfolioOverride)
            $ids = $portfolioOverride;
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);
        
        $query = "SELECT
                   portfolio_security_id,
                   portfolio_security_portfolio_id as portfolio_id,
                   security_symbol, 
                   security_description, 
                   portfolio_security_quantity * security_annual_income_rate as annual_income
                  FROM 
                   vtiger_portfolio_securities ps 
                  JOIN 
                   vtiger_securities s on portfolio_security_security_id = security_id 
                  WHERE 
                   portfolio_security_portfolio_id in ({$ids})
                  AND portfolio_security_quantity > 0 
                  AND security_next_dividend_amount > 0
                  ORDER BY security_description";
                   
        $result = $adb->pquery($query, null);
        
        foreach($result as $k => $v)
        {
            $tmpinfo = array("symbol" => $v["security_symbol"],
                             "description" => $v["security_description"],
                             "portfolio_id" => $v["portfolio_id"],
                             "annual_income" => $v["annual_income"]);
            
            $this->projected_income["info"][] = $tmpinfo;
            $this->projected_income["total"] += $tmpinfo["annual_income"];
        }
    }
    
    public function GetSecuritiesByPortfolioAccountID($paccountid)
    {
        global $adb;
        $query = "SELECT s.security_id
                  FROM vtiger_portfolios p 
                  LEFT JOIN vtiger_portfolio_securities ps ON ps.portfolio_security_portfolio_id = p.portfolio_id
                  LEFT JOIN vtiger_securities s ON s.security_id = ps.portfolio_security_security_id
                  WHERE p.portfolio_account_number = '{$paccountid}'";
                  
        $result = $adb->pquery($query, array());
        return $result;
    }
    
    public function LoadAccountProjectedIncome($id, $accountid, $portfolioOverride=null)
    {
        global $adb;
        if($portfolioOverride)
            $ids = $portfolioOverride;
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);

        $securities = array();
        $result = $this->GetSecuritiesByPortfolioAccountID($accountid);
        if($result)
            foreach($result AS $k => $v)
                $securities[] = $v['security_id'];
        
        $securities = SeparateArrayWithCommas($securities);
        $query = "SELECT
                   portfolio_security_id,
                   portfolio_security_portfolio_id as portfolio_id,
                   security_symbol, 
                   security_description, 
                   SUM(portfolio_security_quantity * security_annual_income_rate) as annual_income
                  FROM 
                   vtiger_portfolio_securities ps 
                  JOIN 
                   vtiger_securities s on portfolio_security_security_id = security_id 
                  WHERE 
                  s.security_id IN ({$securities})
                  AND portfolio_security_portfolio_id in ({$ids})
                  AND portfolio_security_quantity > 0 
                  AND security_next_dividend_amount > 0
                  GROUP BY security_symbol
                  ORDER BY security_description";

        $result = $adb->pquery($query, null);
        
        foreach($result as $k => $v)
        {
            $tmpinfo = array("symbol" => $v["security_symbol"],
                             "description" => $v["security_description"],
                             "portfolio_id" => $v["portfolio_id"],
                             "annual_income" => $v["annual_income"]);
            
            $this->projected_income["info"][] = $tmpinfo;
            $this->projected_income["total"] += $tmpinfo["annual_income"];
        }
    }
    
    public function LoadHoldingsByHHAccountID($id, $portfolioOverride=null)
    {
        global $adb;
        
        if($portfolioOverride)
            $ids = SeparateArrayWithCommasAndSingleQuotes($portfolioOverride);
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);
        
        $query = "select 
                    portfolio_security_id,
                    code_description as category, 
                    security_type_name as subcategory, 
                    s.security_id,
                    portfolio_security_portfolio_id as portfolio_id,
                    security_symbol, 
                    security_description,
                    portfolio_account_name,
                    portfolio_account_number,
                    c.code_name,
                    if(security_symbol = 'CASH','ZZZZZZZZZZZZZZZ',security_description) as sort_col, 
                    sum(portfolio_security_quantity) as portfolio_security_quantity, 
                    security_last_price / security_price_adjustment as security_last_price, 
                    sum(portfolio_security_cost_basis) as portfolio_security_cost_basis,
                    sum(portfolio_security_total_value) as portfolio_security_total_value,
                    date_format(portfolio_security_price_date,'%m/%d/%Y') as PriceDate
                  from 
                    vtiger_portfolio_securities ps 
                        join vtiger_securities s on s.security_id = ps.portfolio_security_security_id 
                            left join vtiger_security_types st on s.security_type_id = st.security_type_id 
                            left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                            left join vtiger_pc_codes c on c.code_id = sc.code_id
                            left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                  where 
                    ps.portfolio_security_portfolio_id in ($ids)
                    and portfolio_security_quantity > 0
                  GROUP BY portfolio_security_id
                  ORDER BY 
                    category, subcategory, security_description";

        $result = $adb->pquery($query, null);//Using the imploded $ids instead of $this->portfolio_ids due to (?) not working with arrays for some reason
        $this->CalculateHoldings($result);
    }
    
    public function LoadAccountHoldingsByHHAccountID($id, $accountid, $portfolioOverride=null)
    {
        global $adb;
        
        if($portfolioOverride)
            $ids = SeparateArrayWithCommasAndSingleQuotes($portfolioOverride);
        else
            $ids = $this->GetImplodedPortfolioIDsFromHHID($id);
        
        $query = "select 
                    portfolio_security_id,
                    code_description as category, 
                    security_type_name as subcategory, 
                    s.security_id,
                    portfolio_security_portfolio_id as portfolio_id,
                    security_symbol, 
                    security_description,
                    portfolio_account_name,
                    portfolio_account_number,
                    c.code_name,
                    if(security_symbol = 'CASH','ZZZZZZZZZZZZZZZ',security_description) as sort_col, 
                    sum(portfolio_security_quantity) as portfolio_security_quantity, 
                    security_last_price / security_price_adjustment as security_last_price, 
                    sum(portfolio_security_cost_basis) as portfolio_security_cost_basis,
                    sum(portfolio_security_total_value) as portfolio_security_total_value,
                    date_format(portfolio_security_price_date,'%m/%d/%Y') as PriceDate
                  from 
                    vtiger_portfolio_securities ps 
                        join vtiger_securities s on s.security_id = ps.portfolio_security_security_id 
                            left join vtiger_security_types st on s.security_type_id = st.security_type_id 
                            left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                            left join vtiger_pc_codes c on c.code_id = sc.code_id
                            left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                  where 
                    ps.portfolio_security_portfolio_id in ($ids)
                    and vtiger_portfolios.portfolio_account_number = '{$accountid}'
                    and portfolio_security_quantity > 0
                  GROUP BY portfolio_security_id
                  ORDER BY 
                    category, subcategory, security_description";

        $result = $adb->pquery($query, null);//Using the imploded $ids instead of $this->portfolio_ids due to (?) not working with arrays for some reason
        $this->CalculateHoldings($result);
    }
    
    public function LoadHoldingsBySecuritySymbols($securitySymbols, $orderby="security_symbol")
    {
        global $adb;
        
        $securitySymbols = SeparateArrayWithCommasAndSingleQuotes($securitySymbols);
        $query = "select 
                    portfolio_security_id,
                    code_description as category, 
                    security_type_name as subcategory, 
                    s.security_id,
                    portfolio_security_portfolio_id as portfolio_id,
                    security_symbol, 
                    security_description,
                    portfolio_account_name,
                    portfolio_account_number,
                    c.code_name,
                    if(security_symbol = 'CASH','ZZZZZZZZZZZZZZZ',security_description) as sort_col, 
                    sum(portfolio_security_quantity) as portfolio_security_quantity, 
                    security_last_price / security_price_adjustment as security_last_price, 
                    sum(portfolio_security_cost_basis) as portfolio_security_cost_basis,
                    sum(portfolio_security_total_value) as portfolio_security_total_value,
                    date_format(portfolio_security_price_date,'%m/%d/%Y') as PriceDate
                  from 
                    vtiger_portfolio_securities ps 
                        join vtiger_securities s on s.security_id = ps.portfolio_security_security_id 
                            left join vtiger_security_types st on s.security_type_id = st.security_type_id 
                            left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                            left join vtiger_pc_codes c on c.code_id = sc.code_id
                            left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                  where 
                    security_symbol IN ({$securitySymbols})
                    and portfolio_security_quantity > 0
                  GROUP BY security_symbol
                  ORDER BY 
                    {$orderby}";
        
        echo "<br /><br />" . $query . "<br /><br />";
        $result = $adb->pquery($query, null);//Using the imploded $ids instead of $this->portfolio_ids due to (?) not working with arrays for some reason
        $this->CalculateHoldings($result);
    }
    public function GetHoldingsCount($pids, $account="", $extraConditions=null)
    {
        global $adb;
        $pids = SeparateArrayWithCommasAndSingleQuotes($pids);
        if($account)
            $account = " AND portfolio_account_number = '{$account}' ";
            
        $query = "SELECT COUNT(*) AS count, security_symbol, security_description
                  FROM vtiger_portfolio_securities ps
                    left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                    left join vtiger_
                  WHERE
                    portfolio_security_portfolio_id in ({$pids})
                    and portfolio_security_quantity > 0 
                    {$extraConditions}
                    {$account}";
        $result = $adb->pquery($query,array());
        return $adb->query_result($result, 0, "count");
    }
    
    public function GetHoldingsByAccount($account, $searchtype="security_symbol", $searchcontent="", $direction="ASC")
    {
        global $adb;
        $query = "select 
                            portfolio_security_id,
                            code_description as category, 
                            security_type_name as subcategory, 
                            s.security_id,
                            portfolio_security_portfolio_id as portfolio_id,
                            security_symbol, 
                            security_description,
                            portfolio_account_name,
                            portfolio_account_number,
                            c.code_name,
                            if(security_symbol = 'CASH','ZZZZZZZZZZZZZZZ',security_description) as sort_col, 
                            sum(portfolio_security_quantity) as portfolio_security_quantity, 
                            security_last_price / security_price_adjustment as security_last_price, 
                            sum(portfolio_security_cost_basis) as portfolio_security_cost_basis,
                            sum(portfolio_security_total_value) as portfolio_security_total_value,
                            date_format(portfolio_security_price_date,'%m/%d/%Y') as PriceDate
                          from 
                            vtiger_portfolio_securities ps 
                                join vtiger_securities s on s.security_id = ps.portfolio_security_security_id 
                                    left join vtiger_security_types st on s.security_type_id = st.security_type_id 
                                    left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                                    left join vtiger_pc_codes c on c.code_id = sc.code_id
                                    left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                          where 
                            portfolio_security_quantity > 0 
                            AND portfolio_account_number = '{$account}' ";
        
          if($searchtype != null)
          {
            if(!$searchcontent)//Nothing was typed in for search text
                $query .= " GROUP BY security_symbol ORDER BY {$searchtype} ";
            else
                $query .= " AND {$searchtype} REGEXP '{$searchcontent}' GROUP BY security_symbol ORDER BY security_symbol ";
          }
          else
          {
              $query .= " GROUP BY {$searchtype} ORDER BY security_symbol ";
          }
          $query .= $direction;
          
        $result = $adb->pquery($query, array());
        $this->CalculateHoldings($result);
        return $adb->num_rows($result);
    }
    
    public function LoadHoldingsByPortfolioIDs($pids, $searchtype="security_symbol", $searchcontent="", $direction="ASC", $account="", $extraConditions=null, $sum=false)//Searchcontent is used for direction
    {
        global $adb;
        $pids = SeparateArrayWithCommasAndSingleQuotes($pids);
        if($sum)
        {
            $sum = "
            sum(portfolio_security_quantity) as portfolio_security_quantity, 
            security_last_price / security_price_adjustment as security_last_price, 
            sum(portfolio_security_cost_basis) as portfolio_security_cost_basis,
            sum(portfolio_security_total_value) as portfolio_security_total_value,";
        }
        else
            $sum = "";
        if($account)
            $account = " AND portfolio_account_number = '{$account}' ";
$query = "select 
                    portfolio_security_id,
                    code_description as category, 
                    security_type_name as subcategory, 
                    s.security_id,
                    portfolio_security_portfolio_id as portfolio_id,
                    security_symbol, 
                    security_description,
                    portfolio_account_name,
                    portfolio_account_number,
                    c.code_name,
                    if(security_symbol = 'CASH','ZZZZZZZZZZZZZZZ',security_description) as sort_col, 
                    {$sum}
                    date_format(portfolio_security_price_date,'%m/%d/%Y') as PriceDate
                  from 
                    vtiger_portfolio_securities ps 
                        join vtiger_securities s on s.security_id = ps.portfolio_security_security_id 
                            left join vtiger_security_types st on s.security_type_id = st.security_type_id 
                            left join vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                            left join vtiger_pc_codes c on c.code_id = sc.code_id
                            left join vtiger_portfolios ON vtiger_portfolios.portfolio_id = ps.portfolio_security_portfolio_id
                  where 
                    ps.portfolio_security_portfolio_id in ({$pids})
                    and portfolio_security_quantity > 0 
                    AND security_symbol != 'CASH'
                    {$extraConditions}
                    {$account} "; //AND portfolio_security_price_date >= '2000, 01, 01'  This was above extraConditions (RS ALTERATION)

                  if($searchtype != null)
                  {
                    if(!$searchcontent)//Nothing was typed in for search text
                        $query .= " GROUP BY security_symbol ORDER BY {$searchtype} ";
                    else
                        $query .= " AND {$searchtype} REGEXP '{$searchcontent}' GROUP BY security_symbol ORDER BY security_symbol ";
                  }
                  else
                  {
                      $query .= " GROUP BY {$searchtype} ORDER BY security_symbol ";
                  }
                  $query .= $direction;
//                  GROUP BY security_symbol
//                  ORDER BY 
//                    category, subcategory, security_description";
//                  echo $query . "<br /><br />";
        $result = $adb->pquery($query, array());//Using the imploded $ids instead of $this->portfolio_ids due to (?) not working with arrays for some reason
        $this->CalculateHoldings($result);
//        echo $query . "<br />";
        return $adb->num_rows($result);
    }
    
    public function CalculateHoldings($result)
    {
        $noCategory = array();
        if($result)
        foreach ($result as $k => $v) 
        {
//            echo "SECURITY SYMBOL: {$v['security_symbol']}, CODE NAME: {$v['code_name']}, DESCRIPTION: {$v['category']}<br />";
            if(!$this->pricedate)
                $this->pricedate = $v["PriceDate"];
            
            if (!$v['category'])
                $v['category'] = $thiscategoryMapping[$v['subcategory']]; //default category for certain subcats w/o category
            if (!$v['category'])
                $v['category'] = 'NONE';
            if (!$v['subcategory'])
                $v['subcategory'] = 'NONE';

            $gainLoss = "";
            $unrealizedGL = 0;
            
            if($v["security_symbol"] == "CASH")//If the security symbol is cash, we skip a bunch of calculations
            {
                $currentValue = $v['portfolio_security_cost_basis'];
                $portfolioSecurityQuantity = '0';
                $securityLastPrice = '0';
            }
            else
            {
                $gainLoss = 0;
                $currentValue = $v['portfolio_security_total_value'];
                #$v['CurrentValue'] = $v['security_last_price'] * $v['portfolio_security_quantity'];
                $v["unrealizedGL"] = $currentValue - $v['portfolio_security_cost_basis'];
                $unrealizedGL = $currentValue - $v['portfolio_security_cost_basis'];
                if($v['portfolio_security_cost_basis'] != 0)
                    $gainLoss = $v['unrealizedGL']/$v['portfolio_security_cost_basis']*100;
                $v["CalculatedGainLoss"] = $v['GainLoss']*100;
                $securityLastPrice = $v['security_last_price'];
                $portfolioSecurityQuantity = $v['portfolio_security_quantity'];
            }
            
            if($v['category'] == 'Fixed')
                $v["CurrentValue"] *= 100;
            
            //Set the individual category element information
            $categories[$v["category"]][] = array("security_symbol" => $v['security_symbol'],
                                                  "current_value" => $currentValue,
                                                  "cost_basis" => $v["portfolio_security_cost_basis"],
                                                  "quantity" => $portfolioSecurityQuantity,
                                                  "securityLastPrice" => $securityLastPrice,
                                                  "unrealizedGL" => $unrealizedGL,
                                                  "gain_loss" => $gainLoss,
                                                  "calculated_gain_loss" => $calculatedGainLoss,
                                                  "weight" => "",
                                                  "description" => $v["security_description"],
                                                  "account_name" => $v["portfolio_account_name"],
                                                  "account_number" => $v["portfolio_account_number"],
                                                  "pricedate" => $v["pricedate"],
                                                  "security_id" => $v["security_id"],
                                                  "portfolio_id" => $v["portfolio_id"]);
            $noCategory[] = array("security_symbol" => $v['security_symbol'],
                                                  "current_value" => $currentValue,
                                                  "cost_basis" => $v["portfolio_security_cost_basis"],
                                                  "quantity" => $portfolioSecurityQuantity,
                                                  "securityLastPrice" => $securityLastPrice,
                                                  "unrealizedGL" => $unrealizedGL,
                                                  "gain_loss" => $gainLoss,
                                                  "calculated_gain_loss" => $calculatedGainLoss,
                                                  "weight" => "",
                                                  "description" => $v["security_description"],
                                                  "account_name" => $v["portfolio_account_name"],
                                                  "account_number" => $v["portfolio_account_number"],
                                                  "pricedate" => $v["pricedate"],
                                                  "security_id" => $v["security_id"],
                                                  "portfolio_id" => $v["portfolio_id"]);
            $tmpInfo = array("account_name"=>$v["portfolio_account_name"],
                             "account_number"=>$v["portfolio_account_number"]);
            $this->accountinfo[] = $tmpInfo;
            $grandTotals["current_value"] += $currentValue;
            $grandTotals['cost_basis'] += $v['portfolio_security_cost_basis'];
        }
        
        $subtotals = array();

        $grandTotals['weight'] = 100;

        $grandTotals['unrealizedGL'] = ($grandTotals['current_value'] - $grandTotals['cost_basis']);
        if($grandTotals['cost_basis'] > 0)
            $grandTotals['gain_loss'] = ($grandTotals['current_value'] - $grandTotals['cost_basis'])/$grandTotals['cost_basis'] * 100;
        
        if($categories)
        {
            foreach ($categories AS $k => $category)
            {
                foreach($category AS $key => $v)
                {
                    if($grandTotals['current_value'] != 0)
                        $categories[$k][$key]["weight"] = $v["current_value"] / $grandTotals['current_value'] * 100;
                    $subtotals[$k]["current_value"] += $v["current_value"];
                    $subtotals[$k]["cost_basis"] += $v["cost_basis"];
                    $subtotals[$k]["unrealizedGL"] += $v["unrealizedGL"];
                    if($subtotals[$k]["cost_basis"] > 0)
                        $subtotals[$k]["gain_loss"] = ($subtotals[$k]["current_value"] - $subtotals[$k]["cost_basis"])/$subtotals[$k]["cost_basis"] * 100;
                    $subtotals[$k]["weight"] += $categories[$k][$key]["weight"];
                    if($dataTotals['CurrentValue'] > 0)
                        $subtotals[$k]['WeightPercent'] = $v['CurrentValue'] / $dataTotals['CurrentValue'];
                }
            }
        }

        $this->grandTotals = $grandTotals;
        $this->categories = $categories;
        $this->subTotals = $subtotals;
        $this->noCategory = $noCategory;
    }
    
    public function ReturnGrandTotals()
    {
        return $this->grandTotals;
    }
    
    public function ReturnCategories()
    {
        return $this->categories;
    }
    
    public function ReturnWithoutCategories()
    {
        return $this->noCategory;
    }
    
    public function ReturnSubtotals()
    {
        return $this->subTotals;
    }
    
    public function ReturnPortfolioIDs(){
        return $this->portfolio_ids;
    }
    
    public function ReturnAccountInfo(){
        return $this->accountinfo;
    }
    
    public function ReturnProjectedIncome(){
        return $this->projected_income;
    }
    
    public function ReturnMonthlySubTotals(){
        return $this->projected_monthly_subtotals;
    }
    
    public function ReturnMonthlyTotals(){
        return $this->projected_monthly_totals;
    }
    
    public function ReturnMonthlyCategories(){
        return $this->projected_monthly_categories;
    }
}

?>
