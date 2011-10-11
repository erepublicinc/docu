<?
require_once('inc.basic.php');
User::Authorize('SUPER_ADMIN'); 
Query::SetAdminMode();


if($_REQUEST['password'])
{
    $email = sanitize($_REQUEST['email']); 
    $fname = sanitize($_REQUEST['firstname']); 
    $lname = sanitize($_REQUEST['lastname']); 
    $pw    = md5($_REQUEST['password']);
    if(! User::newUser($email, $pw, $fname, $lname))
    {
    
        $msg =  "User creation failed, please try again";   
    }  
}


?>

<html>
<head>
</head>
<body>

<h2> new user </h2> <br>
<h2> <?php echo $msg ?> <h2>

<form method="POST">
    <input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect']; ?>"/>
    <table>
        <tr> <td>First Name:</td><td> <input type="text" name="firstname" />   </td></tr>
        <tr> <td>Last Name:</td><td> <input type="text" name="lastname" />   </td></tr>
        <tr> <td>Email:</td><td>    <input type="text" name="email" /></td></tr>
        <tr> <td>Password:</td><td> <input type="password" name="password" />   </td></tr>
        <tr> <td>&nbsp;</td><td>    <input type="submit" value="Submit" />      </td></tr>
    </table>
</form>
</body>
</html>


