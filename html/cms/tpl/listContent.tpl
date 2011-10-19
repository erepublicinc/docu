<div>

<ul>
{foreach $contents as $c}
<li>
 <a href="/cms/gt/articles/{$c->pk}"><b>{$c->title}</b> {$c->create_date} {$c->content_type} {$c->status} </a>
</li>
{/foreach}
</ul>





</div>
