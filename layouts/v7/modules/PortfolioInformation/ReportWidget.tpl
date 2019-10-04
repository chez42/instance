{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<h1>All {$MODULE_TITLE} Portfolios</h1>
<input type="hidden" name="MODULE" value="{$MODULE}" />
<input type="hidden" name="RECORD" value="{$RECORD}" />

<ul>
    <li><a href="#" class="load_report" onclick="return false;">Portfolio Holdings</a></li>
    <li><a href="#" class="load_report" onclick="return false;">Monthly Income</a></li>
    <li><a href="#" class="load_report" onclick="return false;">Portfolio Performance</a></li>
    <li><a href="#" class="load_report" onclick="return false;">Portfolio Overview</a></li>
</ul>