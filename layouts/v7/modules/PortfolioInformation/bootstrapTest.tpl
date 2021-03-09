{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}?parameter=1" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{*{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

<div class="container">
    <div class="row">
        <h1 class="title pb-1 pt-1">Portfolio Summary</h1>
    </div>

    <div class="row text-center">
        <div class="col-lg-5" style="background-color:blue;">
            LEGEND HERE
        </div>
        <div class="col-lg-7" style="background-color:green;">
            PIE HERE
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <h2 class="title pb-1 pt-1">Performance (DATE HERE)</h2>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-6r"><p style="background-color:red" class="alignLeft">Account Number</p></div>
        <div class="col-6r"><p style="background-color:blue" class="alignLeft">Name</p></div>
        <div class="col-8r"><p style="background-color:yellow" class="alignRight">Beginning Balance</p></div>
        <div class="col-8r"><p class="alignRight">Flow</p></div>
        <div class="col-12r"><p class="alignRight">Income</p></div>
        <div class="col-8r"><p class="alignRight">Ending Value</p></div>
        <div class="col-8r"><p class="alignRight">Investment Return</p></div>
        <div class="col-12r"><p class="alignRight" style="background-color:purple;">TWR</p></div>
    </div>
    <div class="row">
        <div class="col-3r"><p style="background-color:red" class="alignLeft">Blended Portfolio Return</p></div>
        <div class="col-8r"><p style="background-color:yellow" class="alignRight">a</p></div>
        <div class="col-8r"><p class="alignRight">a</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
        <div class="col-8r"><p class="alignRight">a</p></div>
        <div class="col-8r"><p class="alignRight">a</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
    </div>
    <div class="row">
        <div class="col-12-2r"><p style="background-color:red" class="alignLeft">S&amp;P 500</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
    </div>
    <div class="row">
        <div class="col-12-2r"><p style="background-color:red" class="alignLeft">Barclays Aggregate Bond</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
    </div>
    <div class="row">
        <div class="col-12-2r"><p style="background-color:red" class="alignLeft">MSCI Emerging Market index</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
    </div>
    <div class="row">
        <div class="col-12-2r"><p style="background-color:red" class="alignLeft">MSCI EAFE Index</p></div>
        <div class="col-12r"><p class="alignRight">a</p></div>
    </div>
</div>