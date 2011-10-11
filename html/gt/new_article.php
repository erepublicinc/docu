<?
require_once('inc.basic.php');
User::Authorize('EDITOR'); 
Query::SetAdminMode();


if($_REQUEST['title'])
{
    $params = new stdClass();
    $params->title       = sanitize($_REQUEST['title']); 
    $params->display_title       = sanitize($_REQUEST['display_title']);     
    $params->summary     = sanitize($_REQUEST['summary']); 
    $params->user_docu   = sanitize($_REQUEST['body']);

    
    $d = new Article($params);
    
    if(! $d->Save())
    {  
        $msg =  "article creation failed, please try again";   
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
         <tr> <td>Display Title:</td><td> <input type="text" name="display_title" />   </td></tr>
        <tr> <td>Summary:</td><td> <input type="text" name="summary" />   </td></tr>
        <tr> <td>Body:</td><td> <textarea name="body">this is a test</textarea>   </td></tr>
        <tr> <td>status:</td><td> <select name="status" >
                     <option value="LIVE" selected="selected">Live</option>  
                     <option value="IN_PROGRESS" >In Progress</option>
                     
                       </select></select> </td></tr>
        <tr> <td>Primary WSS:</td><td> <input type="text" name="target" />   </td></tr> 
        <tr> <td>&nbsp;</td><td>    <input type="submit" value="Submit" />      </td></tr>
    </table>
</form>
</body>
</html>


