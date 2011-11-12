<h1>{$page->pages_display_title}</h1>

<div>
by {$article->users_first_name} {$article->users_last_name}  on {$article->contents_create_date|date_format:$DATE_FORMAT}
{$article->articles_body}
</div>

{foreach $comments as $c}
    {if $c->comments_pk == $c->comments_fk}   <div>    {else}     <div style="margin-left:50px">    {/if}
        {$c->comments_commenter}  {$c->comments_date} <br>
        <b>{$c->comments_title}</b>
         {$c->comments_body}
        </div>
    
{/foreach}


leave a comment
<form method="post">
 
name<input type="text" name= 'comments_commenter' />&nbsp;&nbsp;&nbsp;&nbsp;
email<input type="text" name= 'comments_email' /><br>
title<input type="text" name= 'comments_title' /><br>
body<textarea  name= 'comments_body' ></textarea><br>
<input type="submit"/><br>
<input type="hidden" name= comments_conntents_fk' value="{$article->contents_pk}" />

</form>



