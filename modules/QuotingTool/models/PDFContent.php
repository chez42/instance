<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class QuotingTool_PDFContent_Model
 */
class QuotingTool_PDFContent_Model extends Vtiger_Module_Model
{
    /**
     * @param int $recordId
     * @return array
     */
    public function getLineItemsAndTotal($recordId)
    {
        global $adb;
        $sql = "SELECT\r\n                vtiger_products.product_no,\r\n                vtiger_products.productname,\r\n                vtiger_products.productcode,\r\n                vtiger_products.productcategory,\r\n                vtiger_products.manufacturer,\r\n                vtiger_products.weight,\r\n                vtiger_products.pack_size,\r\n                vtiger_products.cost_factor,\r\n                vtiger_products.commissionmethod,\r\n                vtiger_products.reorderlevel,\r\n                vtiger_products.mfr_part_no,\r\n                vtiger_products.vendor_part_no,\r\n                vtiger_products.serialno,\r\n                vtiger_products.qtyinstock,\r\n                vtiger_products.productsheet,\r\n                vtiger_products.qtyindemand,\r\n                vtiger_products.glacct,\r\n                vtiger_products.vendor_id,\r\n                vtiger_products.imagename,\r\n\r\n                vtiger_service.serviceid,\r\n                vtiger_service.service_no,\r\n                vtiger_service.servicename,\r\n                vtiger_service.servicecategory,\r\n                vtiger_service.service_usageunit," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.qty_per_unit\r\n                    ELSE vtiger_service.qty_per_unit\r\n                END AS qty_per_unit," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.unit_price\r\n                    ELSE vtiger_service.unit_price\r\n                END AS unit_price," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.sales_start_date\r\n                    ELSE vtiger_service.sales_start_date\r\n                END AS sales_start_date," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.sales_end_date\r\n                    ELSE vtiger_service.sales_end_date\r\n                END AS sales_end_date," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.start_date\r\n                    ELSE vtiger_service.start_date\r\n                END AS start_date," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.expiry_date\r\n                    ELSE vtiger_service.expiry_date\r\n                END AS expiry_date," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.discontinued\r\n                    ELSE vtiger_service.discontinued\r\n                END AS discontinued," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.website\r\n                    ELSE vtiger_service.website\r\n                END AS website," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.taxclass\r\n                    ELSE vtiger_service.taxclass\r\n                END AS taxclass," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.currency_id\r\n                    ELSE vtiger_service.currency_id\r\n                END AS currency_id," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.commissionrate\r\n                    ELSE vtiger_service.commissionrate\r\n                END AS commissionrate," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.productname\r\n                    ELSE vtiger_service.servicename\r\n                END AS productname," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.productid\r\n                    ELSE vtiger_service.serviceid\r\n                END AS psid," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.product_no\r\n                    ELSE vtiger_service.service_no\r\n                END AS psno," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN 'Products'\r\n                    ELSE 'Services'\r\n                END AS entitytype," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.unit_price\r\n                    ELSE vtiger_service.unit_price\r\n                END AS unit_price," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.usageunit\r\n                    ELSE vtiger_service.service_usageunit\r\n                END AS usageunit," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.qty_per_unit\r\n                    ELSE vtiger_service.qty_per_unit\r\n                END AS qty_per_unit," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN vtiger_products.qtyinstock\r\n                    ELSE 'NA'\r\n                END AS qtyinstock," . " CASE WHEN vtiger_products.productid != ''\r\n                    THEN c1.description\r\n                    ELSE c2.description\r\n                END AS psdescription," . " vtiger_inventoryproductrel.* " . " FROM vtiger_inventoryproductrel " . " LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid " . " LEFT JOIN vtiger_crmentity AS c1 ON c1.crmid = vtiger_products.productid " . " LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid " . " LEFT JOIN vtiger_crmentity AS c2 ON c2.crmid = vtiger_service.serviceid " . " WHERE vtiger_inventoryproductrel.id = ? ORDER BY sequence_no";
        $result = $adb->pquery($sql, array($recordId));
        $count = $adb->num_rows($result);
        $data = array();
        if ($count) {
            for ($i = 0; $row = $adb->fetch_array($result); $i++) {
                $data[$i] = array();
                foreach ($row as $k => $d) {
                    $data[$i][$k] = $d;
                }
            }
        }
        return $data;
    }
}

?>