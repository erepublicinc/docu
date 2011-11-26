{include file="header.tpl"}

<!-- Left Column-->
<div id="l-col" class="w-170 f-left">
    {foreach $sideModules.left as $sideModule}
        {include file=$sideModule->template params=$sideModule }
        
    {/foreach}
</div>

   
<!-- CENTER COLUMN -->
<div id="center-col" class="base w-480 ml-10">
    
    {if $main_tpl_content}
        {$main_tpl_content}
    {elseif $main_tpl}
        {include file=$main_tpl website=$webpage->mWebsite }
    {/if}
    
    {foreach $sideModules.center as $sideModule}
        {include file=$module->template params=$sideModule}
    {/foreach}
       
</div>


<!--- RIGHT COLUMN --->
<div id="r-col" class="w-300 ml-10">     
    {foreach $sideModules.right as $sideModule}
        {include file=$module->template params=$sideModule}
    {/foreach}
</div>

{include file="footer.tpl"}