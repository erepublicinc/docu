<!-- moduleUsageModule.tpl -->
	<div class="ui-widget-content ui-corner-all p-10 bk_color2">
	    <h3>Pages that use this module</h3>
        
        <br clear="all">
        <div class="m-10">
            <ul class="condenced">
               {foreach $params->pages as $p}
                   <li><a href="/cms/{$p->pages_site_code}/page/{$p->pages_rev}">{$p->pages_site_code} {$p->pages_title} </a></li>
                {/foreach}
            </ul>
        </div>
    </div>  
