<!DOCTYPE html>
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



{literal}
<script type="text/javascript"> 

// global vars

var thePK = 0;
var theIndexing;
var theTitle;

function YaasEncode(method, params)
{
    return JSON.stringify( [{'method':method, 'params': params, 'dsid': Math.random().toString().substr(8)}] );
}

function loadPage(id)
{
  
    $.ajax({
       type: "POST",     
	   url: '/common/yaas2.php',
	   dataType: 'json',
       data:  'data='+ YaasEncode('Documentation.GetDetails',{'id':id}),
	   success: function( data ) {
		   thePK = parseInt(data[0][0].contents_id) +0;
           theIndexing = data[0][0].indexing;
           theTitle = data[0][0].title;
           // get the field from the first row of the first request
	       $('#id_user_doc').html(data[0][0].specs_user_docu);    
	       $('#id_design_doc').text(data[0][0].specs_design_docu);  
	       $('#id_doc_title').text(data[0][0].contents_title);    
	       $('#id_indexing').text(data[0][0].specs_indexing);   
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
       data:  'data='+ YaasEncode('Documentation.Save',{
           'contents_id':thePK, 
           'specs_indexing':$('#id_indexing').text(), 
           'contents_title':$('#id_doc_title').text(), 
           'specs_design_docu':$('#id_design_doc').text(), 
           'specs_user_docu':$('#id_user_doc').html() }),
       success: function( data ) {
                
       },
       failure: function(data){
           alert('oops, I could not save your data');
       }
      
    });
}
var ckConfig = {toolbar :
    [['Source', '-','Undo','Redo','PasteFromWord'],
     ['Find','Replace','-','SelectAll','RemoveFormat'],
     ['Link', 'Unlink', 'Image'],           
     ['Bold', 'Italic','Underline','TextColor','Blockquote', 'SpecialChar','NumberedList','BulletedList']
 ]};
var editor = null;
var edited_div = null;
function closeEditor()
{
	if (editor){
		editor.updateElement();
	    editor.destroy();
	}
	edited_div = null;
}

function edit( div, origin )
{
    alert('edited_div '+edited_div+'   new div: '+div+ ' origin: '+origin);	
    if(edited_div == div)
    {
    	closeEditor();
    }
    else
	{  
    	closeEditor();
	   edited_div = div;  
       editor = CKEDITOR.replace(div, ckConfig);

     //  var writer = editor.dataProcessor.writer;
        // The character sequence to use for every indentation step.
    //    writer.indentationChars = ' '; //'\t';
        // The character sequence to be used for line breaks.
    //    writer.lineBreakChars = ' ' ; //'\n';      
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
/*
      $('.editTextInPlace').dblclick(function(){ 
          var val = $(this).text();
          $(this).html('<textarea rows="50" colls="100">'+val +'</textarea>' );
          $e = jQuery.Event("focus");
          $(this).children().first().trigger($e);
      });
*/      
      $('.editInPlace').focusout(function(){  	 
          var newval = $(this).children().first().val()  ;
    	  $(this).html(newval);
      });  

      $('.editTextInPlace').dblclick(function(){ 
    	 // if ( editor )
    	 //       editor.destroy();
    	  edit( $(this).attr("id") )
    	    
      });
 
});




</script >
{/literal}



</head>
<body>

<!--  left column -->
<div id="id_left_column">

{foreach $docu as $doc}

   <li> <span onclick="loadPage({$doc->contents_id}) "> {$doc->specs_indexing}  {$doc->contents_title} </span> </li>
{/foreach} 


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
