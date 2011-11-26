<?
require_once('inc.basic.php');
User::Authorize(); 
Query::SetAdminMode();


if($_REQUEST['contents_title'])
{
   
    if(! Documentation::sYaasSave($_POST))
    {  
        $msg =  "docu creation failed, please try again";   
    }  
    else {
        header("LOCATION: /index.php");
    }
}


?>

<html>
<head>
</head>
<body>

<h2> new Documentation Item</h2> <br>
<h2> <?php echo $msg ?> <h2>

<form method="POST">
    <input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect']; ?>"/>
    <table>
        <tr> <td>Title:</td><td> <input type="text" name="contents_title" />   </td></tr>
        <tr> <td>Summary:</td><td> <input type="text" name="contents_summary" />   </td></tr>
        <tr> <td>Indexing:</td><td>    <input type="text" name="specs_indexing"/></td></tr>
        <tr> <td>User documentation:</td><td> <input type="text" name="specs_user_docu" />   </td></tr>
        <tr> <td>Design notes:</td><td> <input type="text" name="specs_design_docu" />   </td></tr>
        <tr> <td>&nbsp;</td><td>    <input type="submit" value="Submit" />      </td></tr>
    </table>
</form>
</body>
</html>


