<!-- listModule.tpl -->
	<div class="ui-widget-content ui-corner-all p-10 bk_color2">
	    <h3>{$params->title}</h3>
       
        <br clear="all">
        <div class="m-10">
            <ul class="condenced">
               {foreach $params->items as $item}
                   <li><a href="{$params->path}{$item->id}">{$item->title}</a></li>
                {/foreach}
            </ul>
            <br clear="all">
            <div>
              {foreach $params->buttons as $button}
                 <button href="{$button.url}" style="float:left;" >{$button.text}</button>
              {/foreach}
            </div>
            <br clear="all">
        </div>
        
    </div>  
