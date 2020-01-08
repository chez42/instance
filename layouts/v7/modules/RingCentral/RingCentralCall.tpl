{strip}
	
	<link type='text/css' rel='stylesheet' href='layouts/v7/lib/todc/css/bootstrap.min.css'>
	
	
	{foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}
   
	<div class="ringcentral_details"  style="padding:10px;">
		
		<input type="hidden" name="number" value="{$NUMBER}"/>
		
		<input type="hidden" name="record" value="{$RECORD}"/>
		
		<input type="hidden" name="site_url" value="{$SITE_URL}"/>
		
		<div class = "row">
			<div class="col-md-12" style="text-align:center;">
				{if $IMAGE}
					<img src="{$SITE_URL}{$IMAGE}" style="border-radius:50%!important;width:50px!important;height:50px!important;" >
				{else}
					<img src="{$SITE_URL}layouts/v7/skins/images/summary_Contact.png" style="border-radius:50%!important;width:50px!important;height:50px!important;" >
				{/if}
							
			</div>
		</div>
		
		<div class = "row">
			<div class="col-md-12" style="font-size:15px !important;text-align:center;color:black;font-weight:600;padding-top:15px;">
				{$FULLNAME}<br>
			</div>
		</div>
		<hr/>
		<div class = "header">					
		{foreach item=FIELD_VALUE key=FIELD_NAME from=$FIELDS}
			<div class = "row" style = "font-size:13px;padding:5px;">
				<div class = "col-md-12" style = "text-align:left;">
					<label style = "font-weight:100;color:#6f6f6f;padding-right:10px;">{$FIELD_NAME}: </label>
					<span style = "color:black;">{$FIELD_VALUE}</span>
				</div>
			</div>	
		{/foreach}
		</div>
		{literal}
		<style>
			.number {
				margin: 5px;
				height: 45px;
				width: 45px;
			}
			.clear {
				margin: 5px;
				height: 45px;
				width: 45px;
			}
			.row {text-align:center;}
		</style>
		{/literal}
		<div class = "row">
			<div class="col-md-12">
			<input type="text" id="phoneScreen" />
			<br/>
			<small>Dial No and Press Enter</small>
			<br/>
			<button class="number" value="1">1</button>
			<button class="number" value="2">2</button>
			<button class="number" value="3">3</button>
			<br/>
			<button class="number" value="4">4</button>
			<button class="number" value="5">5</button>
			<button class="number" value="6">6</button>
			<br />
			<button class="number" value="7">7</button>
			<button class="number" value="8">8</button>
			<button class="number" value="9">9</button>
			<br />
			<button class="number" value="del">*</button>
			<button class="number" value="0">0</button>
			<button class="number" value="clr">#</button>
			<br />
			<button class="clear">C</button>
			</div>
		</div>
		
		<div class = "row">
			<div class="col-md-12">
				<textarea class="pull-left" style="width:80%;height:50px;font-size:12px !important;border:1px solid #DDDDDD;" placeholder="Leave notes" name="callnotes"></textarea>
				<img data-id="{$RECORD}" class = "sendnotes" style = "width:40px;margin-left:10px;" src = "{$SITE_URL}modules/RingCentral/resources/end-call.png"/>
			</div>
		</div>
	</div>
	
	<video id="remoteVideo" hidden="hidden"></video>
	<video id="localVideo" hidden="hidden" muted="muted"></video>
{/strip}

