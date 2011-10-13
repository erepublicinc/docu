<?php /* Smarty version Smarty 3.1.0, created on 2011-10-10 15:43:20
         compiled from "/var/www/newgt/html/gtdocu/tpl/docu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12262497754e9374b3875d00-72386146%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '46d7c3db367ea650b86d2bb85c0924af68e9190d' => 
    array (
      0 => '/var/www/newgt/html/gtdocu/tpl/docu.tpl',
      1 => 1318286596,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12262497754e9374b3875d00-72386146',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4e9374b399bc2',
  'variables' => 
  array (
    'docu' => 0,
    'doc' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4e9374b399bc2')) {function content_4e9374b399bc2($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>

<link  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>

<script type="text/javascript" src="/common/ckeditor/ckeditor.js"></script>

<style id="styles" type="text/css">

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    margin:0;
    
}

#id_left_column{
    width:300px;  
    height:1000px; 
    padding:10px; 
    float:left; 
    background-color:#E0FFE0;
}
#id_left_column li{
  font-size:15px;
  
}
#id_left_column li:hover{
  font-weight:bold;
  cursor:pointer;
}

#id_buttonbar{
    padding: 10px;
    background-color:#E0FFE0;
}

div.editable{
    border: solid 2px Transparent;
    padding-left: 15px;
    padding-right: 15px;
}

div.editable:hover{
  /*  border-color: black; */
}

</style>




<script type="text/javascript"> 

// global vars

var thePK = 0;
var theIndexing;
var theTitle;

function YaasEncode(method, params)
{
    return JSON.stringify( [{'method':method, 'params': params, 'dsid': Math.random().toString().substr(8)}] );
}

function loadPage(pk)
{
  
    $.ajax({
       type: "POST",     
	   url: '/common/yaas2.php',
	   dataType: 'json',
       data:  'data='+ YaasEncode('Documentation.GetDetails',{'pk':pk}),
	   success: function( data ) {
		   thePK = parseInt(data[0][0].pk) +0;
           theIndexing = data[0][0].indexing;
           theTitle = data[0][0].title;
           // get the field from the first row of the first request
	       $('#id_user_doc').html(data[0][0].user_docu);    
	       $('#id_design_doc').text(data[0][0].design_docu);  
	       $('#id_doc_title').text(data[0][0].title);    
	       $('#id_indexing').text(data[0][0].indexing);   
	   }
	  
	});
}

function savePage()
{

    var t = $('#id_design_doc').text();
   // alert(t);
	  
    $.ajax({
       type: "POST",     
       url: '/common/yaas2.php',
       dataType: 'json',
       data:  'data='+ YaasEncode('Documentation.Save',{'pk':thePK, 
           'indexing':$('#id_indexing').text(), 
           'title':$('#id_doc_title').text(), 
           'design_docu':$('#id_design_doc').text(), 
           'user_docu':$('#id_user_doc').html() }),
       success: function( data ) {
                
       },
       failure: function(data){
           alert('oops, I could not save your data');
       }
      
    });
}

var editor = null;
var edited_div = null;
function closeEditor()
{
	if ( editor ){
	    editor.destroy();
        editor = null;
	}
	edited_div = null;
}

function edit( div )
{
    if(edited_div == div)
    {
	   closeEditor();
    }
	else
	{  
	   closeEditor();
	   edited_div = div;  
       editor = CKEDITOR.replace( div );      
    }
}

function save()
{
	closeEditor();
	savePage();
}

var inEditMode = null
$(document).ready(function(){
      $('.editInPlace').dblclick(function(){ 
          var val = $(this).text();
          $(this).html('<input type="text" value="'+val +'" />' );
          $e = jQuery.Event("focus");
          $(this).children().first().trigger($e);
      });

      $('.editInPlace').focusout(function(){  	 
          var newval = $(this).children().first().val()  ;
    	  $(this).html(newval);
      });  

      $('.editTextInPlace').dblclick(function(){ 
    	  if ( editor )
    	        editor.destroy();
    	    editor = CKEDITOR.replace( $(this).attr("id"));
      });
 
});




</script >




</head>
<body>

<!--  left column -->
<div id="id_left_column">

<?php  $_smarty_tpl->tpl_vars['doc'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['docu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['doc']->key => $_smarty_tpl->tpl_vars['doc']->value){
$_loop = true;
?>

   <li> <span onclick="loadPage(<?php echo $_smarty_tpl->tpl_vars['doc']->value->pk;?>
) "> <?php echo $_smarty_tpl->tpl_vars['doc']->value->indexing;?>
  <?php echo $_smarty_tpl->tpl_vars['doc']->value->title;?>
 </span> </li>
<?php } ?> 


</div>

<!--  right column -->

<div id='id_buttonbar'>
   <button onclick="showUserDocu = $('#id_user_doc_box').toggle();">User docu</button>
   <button onclick="showDesignDocu = $('#id_design_doc_box').toggle();">design docu</button> 
   <a href="new_docu"><button >add new item</button></a> 
   <button onclick="save();"  style="float:right; margin-right:40px;"; >Save</button>
</div>

<h1><span class="editInPlace" style="font-size:16px;" id="id_indexing"></span> &nbsp; <span class="editInPlace" id="id_doc_title"></span></h1>

<div id="id_user_doc_box" style="margin:10px; width:600px; height:100%; float:left;">
 <span ondblclick="edit('id_user_doc');"><b> User Documentation</b></span><br/>
 <hr/>
 <div id="id_user_doc"  class="editable editTextInPlace" ></div>

</div>

<div id="id_design_doc_box" style="margin:10px;width:600px; height:100%; float:left;">
  <span ondblclick="edit('id_design_doc');"> <b> Design Documentation</b><span><br/>
  <hr/>
 <div id="id_design_doc"  class="editable editTextInPlace" ></div>

</div>


</body>
</html>
<?php }} ?>