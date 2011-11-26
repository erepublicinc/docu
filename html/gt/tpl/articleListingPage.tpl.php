<? ?>
<h1>{$page->pages_display_title}</h1>

{foreach $articles as $a}
    <a href="{$a->contents_url_name}"> {$a->contents_display_title}</a>  <i>({$a->contents_create_date})</i>
{/foreach}
