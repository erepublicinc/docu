<? ?>
<h1>{$page->pages_display_title}</h1>

{foreach $articles as $a}
    <a href="{$page->pages_url}/{$a->contents_url_name}"> {$a->contents_display_title}</a>  
         <i>({$a->contents_create_date|date_format:$DATE_FORMAT})</i>  <br>
{/foreach}
