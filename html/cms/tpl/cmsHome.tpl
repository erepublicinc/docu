<!-- cmsHome.tpl -->


<h1>cms home</h1>
pages for {$site_code}:    (<a href="/cms/{$site_code}/new_page">new page </a>)
<ul>
{foreach $pages as $p}

   <li>{$p->pages_site_code} {$p->pages_url}</li>


{/foreach}

</ul>