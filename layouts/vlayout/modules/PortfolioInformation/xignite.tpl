<h2>Xignite Interactions</h2>
<input type="text" id="symbol" placeholder="Security Symbol" />&nbsp;&nbsp;<input type="button" id="GetFundamentals" value="Get Fundamentals" /><br />
<input type="button" id="sectors" value="Update Sectors" /> - Pull Sector Information from Xignite itself Only<br />
<input type="button" id="map_sectors" value="Map Sectors To CRM" /> - Map the Sector information that was given from Xignite and update our security picklists with them.  This will update the Sector, Industry, and CUSIP<br />
<input type="button" id="populate_unclassified" value="Populate Unclassified" /> - Populate all 100% unclassified positions that don't have numbers in them.  AA4PG for example would not be updated, AAPG would be<br />

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}