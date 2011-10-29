<div class="container_24">
<h2>pages for {$site_name}</h2>
<ul>
{foreach $pages as $p}
    <li>
     <a href="/cms/{$site_code}/page/{$p->pages_pk}"><b>{$p->pages_title}</b> {$p->pages_create_date} {$p->pages_version} {$p->pages_status} </a>
    </li>
{/foreach}
</ul>





</div>

