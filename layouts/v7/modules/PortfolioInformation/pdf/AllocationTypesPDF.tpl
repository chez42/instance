<div class="AllocationTypesWrapper">
    <table style="width:100%; padding-top:50px;">
        <tbody>
        <tr>
            <td style="width:45%;">
                {$SECTOR_PIE_IMAGE}
            </td>
            <td style="width:55%;">
                <table class="table table-bordered" style="width:100%; font-size:16pt;">
                    <thead>
                    <tr>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245);">Description</th>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Weight</th>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
                        <tr style="background-color:{$heading['color']};">
                            <td style="color:white;">{$heading['title']}</td>
                            <td style="text-align:right; color:white;">{$heading['percentage']}%</td>
                            <td style="text-align:right; color:white;">${$heading['value']|number_format:2:".":","}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
{*
    <table style="width:100%; padding-top:50px;">
        <tbody>
        <tr>
            <td style="width:25%;">
                {$SECTOR_PIE_IMAGE}
            </td>
            <td style="width:75%;">
                <table class="table table-bordered" style="width:100%; font-size:16pt;">
                    <thead>
                    <tr>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245);">Description</th>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Weight</th>
                        <th style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:right;">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
                        <tr style="background-color:{$heading['color']};">
                            <td style="color:white;">{$heading['title']}</td>
                            <td style="text-align:right; color:white;">{$heading['percentage']}%</td>
                            <td style="text-align:right; color:white;">${$heading['value']|number_format:2:".":","}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
*}
</div>