<!-- channelListingPage.tpl -->

<h1 class="border-0 mb-5">{$page->pages_display_title}
    <a href=""> <img src="http://media.governing.com/designimages/gt_rss-icon-16x16.gif" class="inline-icon"> </a>
</h1>

{foreach $contents as $content}
    {if $content@first && $curPage == 1 && $page->pages_url != '/featured'}
        <div class="featured-hilight">
            <img  source="{$content->image_url}" class="mt-10 mr-10 mb-0 w-180 f-left" alt="{$content->image_alt}" />         
            <h3><a href="{$page->pages/url}/{$content.url}.html">{$content->display_title}</a></h3>
            <p><i class="nowrap" >{$content->contents_create_date|date_format:$DATE_FORMAT}</i> - {$content->contents_summary} </p>
        </div>
        <br class="clear">
        <br class="clear"> 
    {else}               
   
    
        <dt class="clear-l">
            <a href="{$page->pages_url}/{$content->contents_url}.html}"><img src="{$content->media->thumbnailImage->media_url}" class="w-80 border clear-l" alt="{$content->media->thumbnailImage->media_alt_text}"></a>
           
            <a class="inline mr-5" href="{$page->pages_url}/{$content->contents_url}.html}" >
                {$content->contents_display_title}
                {if $content->contents_type == "VIDEO_ARTICLE"}
                    <img src="{$designMediaPath}/gt_video-icon.png" alt="video" style="float: none; position: relative; top: 8px; margin-left: 8px;" />
                {elseif $photoGallery}
                    <img alt="photo gallery" src="{$designMediaPath}/gt_icon_gallery.gif" style="float: none; position: relative; top: 8px; margin-left: 8px;" />                        
                {/if}
            </a>
       </dt>
       
       {$theClass = "f-left"}
       {if $page->pages_url == '/featured' && $content->media->thumbnailImage}  
            {$theClass = "f-left w-380"} 
       {/if}
       
      
       <dd class="{$theClass}">        
           {if $page->pages_url == '/featured'}
                <i class="nowrap">{$content->contents_create_date|date_format:$DATE_FORMAT}</i> - 
           {/if}
           {$content->contents_summary}
       </dd>
       <br class="clear" />
  
    {/if}

{/foreach}

<div class='clear'></div>
{* pagination goes here *}       


<!--- CENTER COLUMN MODULES--->
{foreach $modules.center as $module}
    {include file="$module"}
{/foreach}


