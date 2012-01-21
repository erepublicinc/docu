{include file="header.tpl"}

   
<!-- MAIN COLUMN -->
<div id="MainContent" class="grid_16">

    
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
<div id="RightColumn" class="grid_8 background">  
    {foreach $sideModules.right as $sideModule}    
        {include file=$sideModule->template params=$sideModule}
    {/foreach}
</div>

{include file="footer.tpl"}