<link rel="stylesheet" href="layouts/v7/modules/PortfolioInformation/css/cssTable.css" type="text/css" />
<link rel="stylesheet" href="layouts/v7/modules/PortfolioInformation/Reports/css/logo.css" type="text/css" />
<link rel="stylesheet" href="layouts/v7/modules/PortfolioInformation/Reports/css/coverPage.css" type="text/css" />
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



