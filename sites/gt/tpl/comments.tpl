<!-- comments.tpl -->
{foreach $comments as $c}
    {if $c->comments_pk == $c->comments_fk}   <div>    {else}     <div style="margin-left:50px">    {/if}
        {$c->comments_commenter} &nbsp;&nbsp;&nbsp; {$c->comments_date|date_format:$DATE_FORMAT} <br>
        <b>{$c->comments_title}</b><br>
         {$c->comments_body}
        </div>
    <br clear="all"/>
{/foreach}


leave a comment
<form method="post" action="/common/post_comment.php">
    <input type="hidden" name= "redirect_url" value="{$redirect_url}" /> 
    <input type="hidden" name= "comments_contents_fk" value="{$article->contents_pk}" /> 
    <input type="hidden" name= "comments_fk" value="" /> 
    <table>
        <tr><td>name</td><td><input type="text" name= 'comments_commenter' />
        email<input type="text" name= 'comments_email' /></td></tr>
        <tr><td>title</td><td><input type="text" name= 'comments_title' /></td></tr>
        <tr><td>body</td><td><textarea  name= 'comments_body' ></textarea></td></tr>
        <tr><td> </td><td><input type="submit"/></td></tr>
    </table>
</form>

