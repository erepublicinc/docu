{include file="header.tpl" }
<div class="maincontainer_{if $sideModules.right}3{else}2{/if}col">
    <div class="topshadow"> &nbsp;</div>

    {*##################### LEFT COLUMN ########################*}
    <div id="Leftcolumn">
    {if $sideModules.left}
        {include file="module_listings.tpl" modules=$sideModules.left website=$webpage->mWebsite }
    {else}
        <div> &nbsp;</div>
    {/if}
    </div>

    {*##################### CENTER COLUMN ########################*}
    <div id="Centercolumn">
    {if $main_tpl_content}
        {$main_tpl_content}
    {elseif $main_tpl}
        {include file=$main_tpl website=$webpage->mWebsite }
    {/if}
    </div>

    {*#################### RIGHT COLUMN ########################*}
    {if $sideModules.right}
    <div id="Rightcolumn">
        {include file="module_listings.tpl" modules=$sideModules.right }
    </div>
    {/if}
</div>

{include file="footer.tpl" }

