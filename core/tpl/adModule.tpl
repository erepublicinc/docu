{*contains the div element that the ad is going to be in , this template should be included where you want the ad to appear *}

{* this template can be included directly  for eeaxampl by the hesder template or be loaded as a module *}
{if $params != ''}
   {$position = $params->position}
   {$ad_class = $params->ad_class}
{/if}


{$ad = $ad_specs->specs.$position}

{if $dz} {*  is dz the disasterzone ??? blog *}
  {$zone = $dz}
{elseif $page->page_url == "/"}
    {if $site_code == 'GOV'}
        {$zone = "/home"}
    {else}
        {$zone = "/index"}
    {/if}
{else}
    {$zone = $page->page_url }
{/if}

{if $contents_contents_id}
    {$itemid =  $contents_contents_id}
{else}
    {$itemid =  $pages_pages_id}
{/if}
   
{if $ad_class}
    {$the_class = "$class=$ad_class" }
{/if}

<div id="ad_{$position}"  {$the_class}   style="width:={$ad.width}px; height=:{$ad.height}px;" > 
    <div class="img_nopad">
    <script type="text/javascript">
        display_ad('{$itemid}', '{$ad_specs->newsletterID}', '{$ad_specs->site}', '{$zone}', '{$position}', '{$ad.width}', '{$ad.height}', true, '{$ad_specs->iframe_ads}');
    </script>
    </div>
</div>

{if $position != "T3" &&  $position != "T3"}
<script type="text/javascript"> 
   var ad_div = document.getElementById('ad_$position');
   hideAdZone(ad_div);
</script>
{/if}


