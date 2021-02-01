<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/css/cssTable.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/Reports/css/logo.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/Reports/css/coverPage.css" type="text/css" />
<style>
	.PreparedBy p {
		text-align:center !important;
	}
</style>
<div id="LHCoverPage">
    <div class="CoverPageLogo">
        {$COVERPAGE->GetFormattedLogo()}
    </div>
    <div class="LHPreparedSection">
        {$COVERPAGE->GetFormattedTitle()}
        {$COVERPAGE->GetFormattedPreparedFor()}
        {$COVERPAGE->GetFormattedPreparedBy()}
        {$COVERPAGE->GetFormattedPreparedDate()}
    </div>
{*        <div class="tr">
            <div class="td LHLogoContainer">
            </div>
            <div class="td LHPreparedSection">
                {$COVERPAGE->GetFormattedTitle()}
                {$COVERPAGE->GetFormattedPreparedFor()}
                {$COVERPAGE->GetFormattedPreparedBy()}
                {$COVERPAGE->GetFormattedPreparedDate()}
            </div>
        </div>
    </div>*}
</div>





{*<table id="lighthousetitle" style="padding-top:200px;">
    <tr>
        <td style="width:70%;">
            <img src="{$SITEURL}/layouts/hardcoded_images/lhimage.jpg" width="100%" />
        </td>
        <td style="text-align:center; width:30%; padding-top:100px; font-size:12pt;">
            <h2>Portfolio Review</h2>
            <h3>{$PREPARED_FOR}</h3>
            <p><strong>Prepared By:</strong> {$PREPARED_BY}</p>
            <p>{$TODAY}</p>
        </td>
    </tr>
</table>*}