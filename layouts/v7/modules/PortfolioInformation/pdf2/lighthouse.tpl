<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/css/cssTable.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/Reports/css/logo.css" type="text/css" />
<link rel="stylesheet" href="{$SITEURL}/layouts/v7/modules/PortfolioInformation/Reports/css/coverPage.css" type="text/css" />
<style>
	.PreparedBy p {
		text-align:center !important;
	}
</style>
<div id="LHCoverPage">
    <div class="CoverPageLogo" style = "width:70%;float:none;display:inline-block;line-height:1;vertical-align:middle;">
        {$COVERPAGE->GetFormattedLogo()}
    </div>
    <div class="LHPreparedSection" style = "width:25%;float:none;display:inline-block;line-height:1;vertical-align:bottom;padding-bottom:60px;">
        {$COVERPAGE->GetFormattedTitle()}
        {$COVERPAGE->GetFormattedPreparedFor()}
        {$COVERPAGE->GetFormattedPreparedBy()}
        {$COVERPAGE->GetFormattedPreparedDate()}
    </div>
</div>
{literal}
<style>
	#TitleName {font-weight:800;}
	#PreparedForName {font-weight:800;}
	.PreparedFor {padding-top:5px;}
	.LHPreparedSection #PreparedByName {padding-top:4px;}
	.LHPreparedSection #TitleName {text-decoration:none;}
</style>
{/literal}