<div class="container_24">

<ul>
{foreach $contents as $c}
<li>
 <a href="/cms/gt/article/{$c->contents_pk}"><b>{$c->contents_title}</b> {$c->contents_create_date} {$c->contents_type} {$c->contents_status} </a>
</li>
{/foreach}
</ul>





</div>
