{*{foreach key=index item=jsModel from=$EXTRA_SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}
<link type='text/css' rel='stylesheet' href='layouts/v7/modules/ModSecurities/css/EODPopup.css' />

<div id="price_wrapper" style="padding:5px; font-size:16px;">
    <p>
        {$BOND->symbol}
    </p>
    <p><strong><span style="color:yellow; font-size:18px;">${$BOND->price|number_format:2:".":","}</span></strong>
    </p>
    <p>Bond Info</p>
    <div class="eod_table">
        <div class="eod_tr">
            <div class="eod_td">{$BOND->name}</div>
            <div class="eod_td">{$BOND->type}</div>
        </div>
        <div class="eod_tr">
            <div class="eod_td">Maturity</div>
            <div class="eod_td right">{$BOND->maturity}</div>
        </div>
        <div class="eod_tr">
            <div class="eod_td">Issuer</div>
            <div class="eod_td right">{$BOND->issuer}</div>
        </div>
    </div>
    <p style="font-size:10px;">{$BOND->description}</p>
    <p style="font-size:10px;"><strong>As of: {$BOND->as_of}</strong></p>
</div>

{*
stdClass Object
(
[ISIN] => US00206RDC34
[CUSIP] => 00206RDC3
[Name] => AT&T INC 4.45% 01Apr2024
[UpdateDate] => 2020-07-16
[WKN] => A18ZK9
[Sedol] => BD20MG8
[FIGI] => BBG00C9Y0SL4
[Currency] => USD
[Coupon] => 4.450
[Price] => 112.12
[LastTradeDate] => 2020-07-16
[Maturity_Date] => 2024-04-01
[YieldToMaturity] => 0.874
[Callable] => Yes
[NextCallDate] => 2024-01-01
[MinimumSettlementAmount] => 1000 USD
[ParIntegralMultiple] => 1000 USD
[ClassificationData] => stdClass Object (
[BondType] => Corporate bonds world rest
[DebtType] => Senior Unsecured Note
[IndustryGroup] => Industrial
[IndustrySubGroup] => Telephone
[SubProductAsset] => CORP
[SubProductAssetType] => Corporate Bond )
[Rating] => stdClass Object (
[MoodyRating] => Baa2
[MoodyRatingUpdateDate] => 2020-07-16
[SPRating] => BBB
[SPRatingUpdateDate] => 2018-06-15 )
[IssueData] => stdClass Object (
[IssueDate] => 2015-10-01
[OfferingDate] => 2016-03-11
[FirstCouponDate] => 2016-04-01
[FirstTradingDay] => 2015-10-01
[CouponPaymentFrequency] =>
[Issuer] => AT & T Inc.
[IssuerDescription] => AT&T is a communications holding company. The Company, through its subsidiaries and affiliates, provides local and long-distance phone service, wireless and data communications, Internet access and messaging, IP-based and satellite television, security services, telecommunications equipment, and directory advertising and publishing.
[IssuerCountry] => USA
IssuerURL] => http://www.att.com/ ) )
*}