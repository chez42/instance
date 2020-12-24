<link rel="stylesheet" href="{$SITEURL}layouts/v7/modules/PortfolioInformation/css/cssTable.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}layouts/v7/modules/PortfolioInformation/Reports/css/logo.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}layouts/v7/modules/PortfolioInformation/Reports/css/coverPage.css" type="text/css" />

<div id="CoverPage">
    <div class="LogoSection">
        {$COVERPAGE->GetFormattedTitle()}
    </div>
    <div class="PreparedSection">
        {$COVERPAGE->GetFormattedPreparedFor()}
        {$COVERPAGE->GetFormattedPreparedBy()}
        {$COVERPAGE->GetFormattedPreparedDate()}
    </div>
</div>