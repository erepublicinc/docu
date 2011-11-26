<!-- homePage.tpl --> 
 homepage
 <ul>
    {foreach $articles as $article}
        <li> <a href= "$article->contents_url_name" >{$article->contents_title} </a> </li>   
    {/foreach}
    </ul>
