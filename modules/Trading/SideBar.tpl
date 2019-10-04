<div class="quickWidgetContainer">
        <div class="quickWidget">
                <div class="quickWidgetHeader">
                        <h5 class="pull-left">Look at me, In a tree!</h5>
                        <button class="btn pull-right" id="RssFeedAdd">+</button>
                        <div class="clearfix"></div>
                </div>
                <div class="quickWidgetHeader">
                        <h5 class="pull-left">I do nothing, but I'm here!</h5>
                        <button class="btn pull-right" id="RssFeedAdd">+</button>
                        <div class="clearfix"></div>
                </div>
                <div class="widgetContainer collapse in">
                        <div class="row-fluid">
                                <div class="span10">
                                        <ul class="nav nav-list">
                                                {foreach item=FEED from=$FEEDS}
                                                <li>
                                                    <a href="{$FEED->get('url')}"
                                                    data-feedurl="{$FEED->get('url')}">
                                                     {$FEED->get('title')}</a>
                                                </li>
                                                {/foreach}
                                        </ul>
                                </div>
                        </div>
                </div>
        </div>
</div>