/**
 * Created by ryansandnes on 2017-06-23.
 */
jQuery.Class("TableEditor_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TableEditor_Js();
        return instance;
    }
},{
    registerEvents : function() {
        /*        var data = [
                    ['id', 'Tesla', 'Toyota', 'Honda'],
                    [10, 11, 12, 13],
                    [20, 11, 14, 13],
                    [30, 15, 12, 13]
                ];*/

        var container = document.getElementById('handson');
        var hot = new Handsontable(container, {
//            data: data,
            rowHeaders: true,
            colHeaders: true,
            filters: true,
            dropdownMenu: true,
            editor:false,
//            bindRowsWithHeaders: 'strict',//Needed so if rows are moved around, the ID will always stay the same with its data
            licenseKey: 'non-commercial-and-evaluation',
            afterInit: function(){
                $.post("index.php", {module:'PortfolioInformation', action:'LoadHandsOnTableJson', table:"custodian_omniscient.file_locations"}, function(response) {
                    var data = $.parseJSON(response);//Get index information
//                    console.log(data);
                    hot.updateSettings({
                        colHeaders: data.headers
                    });
                    hot.loadData(data.data);
                });
            },
            cells: function(row, col){
                var cp = {}
                if(col !== 0){
                    cp.editor = 'text';
                }
                return cp;
            },
            afterChange: function (change, source) {
                if (source === 'edit') {
                    var data = this.getDataAtRow(change[0][0]);
                    var header = this.getColHeader(change[0][1]);
                    $.post("index.php", {module:'PortfolioInformation', action:'SaveHandsOnTableJson', data:data, field_name:header, table:"custodian_omniscient.file_locations"}, function(response) {
                    });
                }
            }
        });
    },


});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
jQuery(document).ready(function($) {
    var instance = TableEditor_Js.getInstanceByView();
    instance.registerEvents();
});