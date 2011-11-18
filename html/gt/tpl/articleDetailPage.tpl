<h1>{$page->pages_display_title}</h1>

<div>
by {$article->users_first_name} {$article->users_last_name}  on {$article->contents_create_date|date_format:$DATE_FORMAT}
<br>
{$article->contents_article_body}
</div>

{include file="comments.tpl"}



