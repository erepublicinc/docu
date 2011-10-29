norobots:{$page->pages_no_robots}
<div class="container_24">
<form id= "id_details_form" method="POST">

<input type="hidden" name="pages_pk" value="{$page->pages_pk}" />
<input type="hidden" name="pages_id" value="{$page->pages_id}" />
<input type="hidden" name="pages_version" value="{$page->pages_version}" />
<input type="hidden" name="site_code" value="{$site_code}" />

<h2>{$site_name}</h2>
pk: {$page->pages_pk} id:{$page->pages_id} version: {$page->pages_version}<br>
title:      <input type="text" name="pages_title" class=""  value="{$page->pages_title}"> <br>
display Title:<input type="text" name="pages_display_title" class=""  value="{$page->pages_display_title}"> <br>
url         <input type="text" name="pages_url" class=""  value="{$page->pages_url}"> <br>
type:       <input type="text" name="pages_type" class=""  value="{$page->pages_type}"> <br>
password    <input type="text" name="pages_password" class=""  value="{$page->pages_password}"> <br>
status:     <input type="text" name="pages_status" class=""  value="{$page->pages_status}"> <br>
php class:  <input type="text" name="pages_php_class" class=""  value="{$page->pages_php_class}"> <br>

no robots:  <input type="checkbox" name="pages_no_robots" {if $page->pages_no_robots == 1} checked="checked"{/if}class="" /> <br>

body:       <input type="text" name="pages_body" class=""  value="{$page->pages_body}"> <br>

<br><hr />
make live:    <input type="checkbox" name="pages_is_live" class="" />  
make preview: <input type="checkbox" name="pages_is_preview" class=""/>  
create new version: <input type="checkbox" name="new_version" checked="checked" class="" /> <br>
 <br>

</form>
       <img src="/images/btn_save.png"  onclick="$('#id_details_form').submit();">
       <img src="/images/btn_cancel.png" onclick="document.location='/cms/pages' ">
      
</div>
<br>
<br>