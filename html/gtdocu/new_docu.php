<?
require_once('inc.basic.php');
User::Authorize(); 
Query::SetAdminMode();


if($_REQUEST['title'])
{
    $params = new stdClass();
    $params->title       = sanitize($_REQUEST['title']); 
    $params->summary     = sanitize($_REQUEST['summary']); 
    $params->indexing    = sanitize($_REQUEST['indexing']); 
    $params->user_docu   = sanitize($_REQUEST['user_docu']);
    $params->design_docu = sanitize($_REQUEST['design_docu']);
    
    $d = new Documentation($params);
    
    if(! $d->Save())
    {  
        $msg =  "docu creation failed, please try again";   
    }  
    else {
        header("LOCATION: docu.php");
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
        <tr> <td>Title:</td><td> <input type="text" name="title" />   </td></tr>
        <tr> <td>Summary:</td><td> <input type="text" name="summary" />   </td></tr>
        <tr> <td>Indexing:</td><td>    <input type="text" name="indexing"/></td></tr>
        <tr> <td>User documentation:</td><td> <input type="text" name="user_docu" />   </td></tr>
        <tr> <td>Design notes:</td><td> <input type="text" name="design_docu" />   </td></tr>
        <tr> <td>&nbsp;</td><td>    <input type="submit" value="Submit" />      </td></tr>
    </table>
</form>
</body>
</html>


