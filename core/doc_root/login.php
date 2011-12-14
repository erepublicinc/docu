<?
require_once('inc.basic.php');

if($_REQUEST['redirect'])
    $redir = $_REQUEST['redirect'];
else   
      $redir = '/';


if($_REQUEST['password'])
{
    $email = sanitize($_REQUEST['email']); 
    $pw    = md5($_REQUEST['password']);
    if(User::login($email, $pw))
    {
        header("LOCATION: $redir");
    }  
    else 
    {
        $msg =  User::$errorMessage;   
    }  
}


?>

<html>
<head>
</head>
<body>

<h2> Please login to get access to the eRepublic cms </h2> <br>
<h2> <?php echo $msg ?> <h2>

<form method="POST">
    <input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect']; ?>"/>
    <table>
        <tr> <td>Email:</td><td>    <input type="text" name="email" value="<?php echo $_REQUEST['email']; ?>"/></td></tr>
        <tr> <td>Password:</td><td> <input type="password" name="password" />   </td></tr>
        <tr> <td>&nbsp;</td><td>    <input type="submit" value="Submit" />      </td></tr>
    </table>
</form>
</body>
</html>

