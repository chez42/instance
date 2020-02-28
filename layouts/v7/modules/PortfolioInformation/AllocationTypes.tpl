<div class="AllocationTypesWrapper" style="padding-left:10%; padding-right:10%;">
    <table style="width:100%;">
        <body>
        <tr>
            <td>
                <div id="sector_pie_holder" class="sector_pie_holder" style="height: 450px; width:450px;"></div>
            </td>
            <td>
                <table class="table table-bordered DynaTable table-collapse">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align:right;">Weight</th>
                        <th style="text-align:right;">Value</th>
                    </tr>
                    </thead>
                    {foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
                        <tr style="background-color:{$heading['color']}; color:white;">
                            <td>{$heading['title']}</td>
                            <td style="text-align:right;">{$heading['percentage']}%</td>
                            <td style="text-align:right;">${$heading['value']|number_format:2:".":","}</td>
                        </tr>
                    {/foreach}
                </table>
            </td>
        </tr>
        </body>
    </table>
</div>