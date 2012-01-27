<?php
require_once('inc.basic.php');

if(! User::Authorize('SUPER_ADMIN'))
   die('You must be SUPER_ADMIN to run this.'); 

$VARS ;  //these are the vars used for the template   

//ob_start();

$VARS['action_results'] = '';

// check if this is not a arepeat request
if($_REQUEST['commit'])
{
    // md5 the request string to avoid double posting through a reload
    $previous_md5 = $_SESSION['previous_md5'];
    $rstring      = serialize($_REQUEST);
    $o_md5        = md5($rstring);
    if($o_md5 == $previous_md5)
    {
        $repeat_request = true;
        $VARS['action_results'] = "repeat request"; // - $o_md5 - $rstring <br>\n";
    }
   // else  $VARS['action_results'] = "new: $o_md5 != old: $previous_md5";
    
    $_SESSION['previous_md5'] = $o_md5;
} 

/////////////////////////////////////// take action //////////////////////////
try
{
    if(! $repeat_request)
    {
        
    
        $VARS['action_results'] .= $_REQUEST['commit'];
        
        switch($_REQUEST['commit'])
        {
        case 'commit':
            commit();
            break;
        case 'change branch':
            change_branch();
            break;
        case 'put on proof':
            put_on_proof();
            break;
        case 'put on live':
            put_on_live();
            break;
        default:
            $VARS['action_results'] .= " unknown commit string ";            
        }    
    } 
    
    get_data();
    
}catch (Exception $e )
{
    $VARS['action_results'] .= "<br><h2>Exception</h2>". $e->getMessage();   
}
    



//ob_end_clean(); 

// ========================= start of functions ============================

function change_branch()
{
    global $VARS; // $VARS['action_results']
    $newbranch = $_REQUEST['new_branch'];
    if(! empty($newbranch))
    {
       $newbranch = trim($newbranch);
       $newbranch = str_replace(' ', '_', $newbranch);
       $VARS['action_results'] .= syscall("git branch $newbranch");
    }
    elseif(strpos($_REQUEST['branch'],'*') !== false) // we are already on the branch that is requested
    {
       return;
    }
    else 
    {
        $newbranch = $_REQUEST['branch'];
    }
   
    $VARS['action_results'] .= syscall("git checkout $newbranch");
    
}

function commit()
{
    if(strpos($_REQUEST['branch'],'*') === false)
    {
        // we have to change the branch first
        $stashid = rand();
        $VARS['action_results'] .=syscall("git stash save $stashid");
        change_branch();
        $VARS['action_results'] .=syscall("git stash apply");
        $VARS['action_results'] .=syscall("git stash clear");
    }
    
    
    $files ='';
    $msg = $_REQUEST['commit_msg'];
    
    // we cannot use the $_REQUEST array for this, it mangels the filenames
    $pieces = explode('&', $_SERVER['QUERY_STRING']);
    
    foreach($pieces as $piece)
    { 
        $start = substr($piece,0,5);   // all files start with "file "
        if($start == 'file_')
        { 
            $equal  = strpos($piece, '=');
            $files .= urldecode( substr($piece, 5, $equal - 5));
            $files .= ' ';
        }
    }
    
   
    if($files != '')
    {
        $str = "git add $files";
        echo syscall($str); 
        echo "<br><br>\n" ;
        $str = "git commit -m '$msg' ";
        echo syscall($str);
    }
    else 
      echo('nothing to commit');
}

function put_on_proof()
{
    
}

function put_on_live()
{
    
}

function get_data()
{
    global $VARS;
    // get branches
    $VARS['branches'] = array();
    $str = syscall("git branch");
    $ar = explode("\n", $str);
    foreach($ar as $l)  // put the current branch at the beginning of the array
    { 
        if(empty($l)) 
            continue; 
        if(strpos($l,"*") !== false )
            array_unshift($VARS['branches'], $l);
        else 
            array_push($VARS['branches'], $l);
    }
    
    // get status of files
    $VARS['files'] = array();
    $str = syscall("git status -s");
    $ar = explode("\n", $str);
    foreach($ar as $l)  // put the current branch at the beginning of the array
    {  
       if(empty($l)) 
            continue;  
       $status = substr($l, 0, 2); 
       $fname  = substr($l,3);
       $VARS['files'][] = array("status" => $status, "name" => $fname);
    }

}




////////////////////////////////////////////////////////////////////////////

function syscall_old($str)
{ 
    global $VARS;
    $VARS['commands'][] = $str;
    
    ob_clean();
    
    $retVal = 0;
    system("cd ../../..; ".$str, $retval);
   
    $ostr= ob_get_contents();
       
    return $ostr;
}

function syscall($command) {
    global $CONFIG;
    global $VARS;
    $VARS['commands'][] = $command;
    
    $descriptorspec = array(
		1 => array('pipe', 'w'),
		2 => array('pipe', 'w'),
	);
	$pipes = array();
	
	$resource = proc_open($command, $descriptorspec, $pipes, $CONFIG->install_path);

	$stdout = stream_get_contents($pipes[1]);
	$stderr = stream_get_contents($pipes[2]);
	foreach ($pipes as $pipe) {
		fclose($pipe);
	}

	$status = trim(proc_close($resource));
	if ($status) throw new Exception($stderr);
    $stdout .= "<br><br>\n\n";
	return $stdout;
}
    

// ========================================  start of html =========================
?>

<html>
<head>

</head>
<body>
<h1>GIT script</h1>
<?=$VARS['action_results'] ?>

<!-- ==================================== commit =================================== -->
<hr>
<form method="get">

    <p>
    branch / ticket: 
    <select name="branch">
        <?foreach($VARS['branches'] as $branch) echo "<option>$branch</option>"; ?>
    </select> 
    new: <input type="text" name="new_branch"/> 
    (select the branch you want to commit these files into)
    </p>
    
    <p>
    changed files: (select the ones you want to commit)<br>
    <? 
    foreach($VARS['files'] as $file)
    {
        echo "<input  name='file_". $file["name"] . "' type='checkbox' > ". $file["status"]. " " .$file["name"]."<br>\n" ;
    }
    ?>
    </p>
    
    <p>
    A commit msg is required. <br>
    <textarea name="commit_msg" style="width:300px;" onfocus="this.select()" onclick="this.value='';">Commit Message</textarea>
    </p>
     
    <input type="submit" name="commit" value="commit">
    <input type="submit" name="commit" value="change branch">
</form>



<!-- ==================================== proof =================================== -->
<hr>
<form method="post">



<input type="submit" name="commit" value="put on proof">
</form>


<!-- ==================================== live =================================== -->
<hr>
<form method="post">



<input type="submit" name="commit" value="put on live">
</form>

<h3>all git commands </h3>
<?
foreach( $VARS['commands'] as $command)
 echo "$command <br>\n";
?>

</body>
</html>

