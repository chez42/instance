<?php
/**
 * Written by Ryan Sandnes
 */

class cWidget{
    public $widgets = array();
    
    public function GetDetailViewWidget($tabid, $url_extension){
        global $adb;
        $widgets = array();
        $query = "SELECT * FROM vtiger_links WHERE tabid = ? 
                  AND linktype = ?
                  AND linklabel != ?
                  ORDER BY sequence";
        
        $result = $adb->pquery($query, array($tabid, "DETAILVIEWWIDGET", "DetailViewBlockCommentWidget"));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $widgets[$v['sequence']] = array(
                                'linktype' => 'DETAILVIEWWIDGET',
                                'linklabel' => $v['linklabel'],
                                'linkurl' => $v['linkurl'] . $url_extension,
                                'sequence' => $v['sequence']);
            }
        }
        $this->widgets = $widgets;
        return $widgets;
    }
}

?>
