<?php
require_once('inc.basic.php');

if(! User::Authorize('EDITOR'))
   die('unauthorized'); 
   
Query::SetAdminMode();

$test_user_pk = $_SESSION['user_pk'];

echo('<h1>TEST PAGE</h1><br> <pre>');

// this will tell function log_error NOT to die
$CONFIG->SetValue('is_test', 1);

// to show all sql statements
//$CONFIG->SetValue('show_sql',1);

$revert = array();  // array of sql to clean up our test data 

$randomId = rand(11, 99);


echo "================== session test  ==== <br>";

print_r($_SESSION);



//die("===== end of test");

echo " ================= basic Query test <br>";
$q = new Query('SELECT COUNT(*) as num FROM contents');
echo " there are " . $q->num . " content items <br>";

echo "<br>here are the first 3 content items";
foreach( new Query('SELECT * FROM contents limit 3') as $c)
{
    echo("<br>title: $c->contents_title");
}

echo "<br><br>list 3 content items and 3 users";
$ar = array('SELECT * FROM contents limit 3', 'SELECT * from users limit 3');
$results = new Query($ar);
foreach( $results as $c)
{
    echo("<br>result 1 title: $c->contents_title");
}
$results->NextResultSet();
foreach( $results as $c)
{
    echo("<br>result 2 last name: $c->users_last_name");
}




echo"<br><br> ================================================ create update and retrieve a user <br>";
$CONFIG->SetValue('show_sql',0,true);

$data = array(
    'users_first_name'=> 'First Name'.$randomId,
	'users_last_name'=>'Lname int. test'.$randomId, 
    'users_password' => '123456789',
	'users_email' => 'verylongusername@verylongdomain.com',
	'users_ad_user' => '',
	'users_active' => 1
);

$user   = new User($data);
$userpk = $user->Save();
assert_gt_zero($userspk, __LINE__); 

if($userpk)
   $revert[]= "delete from users where users_pk = $userpk";

// retrieve it into an array
$user = User::GetDetails($userpk);
$ar = $user->ToArray();
$ar['users_email']   = 'newemail@test.com';
$data['users_email'] = 'newemail@test.com';

//save it with the new data 
$user = new User($ar);
$user->Save();

// retrieve it again
$user = User::GetDetails($userpk);

unset($data['users_password']);  // GetDetails does not retrieve the password
compare_data($data, $user);


$result = User::SetRoles($userpk, array('GT_EDITOR'));


echo" ================================================ create and retrieve a page <br>";
$CONFIG->SetValue('show_sql',0,true);
$data = array('pages_title'=> 'page integration test'.$randomId,
	'pages_display_title'=>'pages_display_title', 
    'pages_is_live' => 1,
	'pages_is_preview' => 0,
    'pages_site_code' => 'GT',
    'pages_url' => 'testurl',
    'pages_no_robots' => 0,
    'pages_status' =>'READY',
 	'pages_php_class' =>'phpclass',
 	'pages_body' =>'this is the body',
    'pages_version_comment'=> ' this is a test'
);

$page = new Page($data);
$pagepk = $page->Save();
echo "created page pk : $pagepk <br>";
if($pagepk >0 )
	$revert[]= "delete from pages where pages_pk = $pagepk";
	
$the_page = Page::GetDetails($pagepk);


compare_data($data, $the_page);



echo"<br> =========================================== create and retrieve a module <br>";
$CONFIG->SetValue('show_sql',0,true);

$data = array(
    'contents_title' 		=> 'module integration test'.$randomId,
    'contents_display_title'=>'pages_display_title', 
    'contents_status' 		=>'READY',

    'contents_version_comment'=> ' this is a test',

    'modules_site_code' 	=> 'GT',
    'modules_json_params' 	=> 'json',
 	'modules_php_class' 	=>'phpclass',
 	'modules_body' 			=>'this is the body'

);
$ct = new Module($data);
$pk = $ct->Save();
Content::setLiveVersion($pk, 1 );

echo "created module pk : $pk <br>";
if($pk >0 )
	$revert[]= "delete from contents where contents_pk = $pk";
	
$the_module = Module::GetDetails($pk, 1);
compare_data($data, $the_module);




echo"<br> =========================================== link  module to a page <br>";
$CONFIG->SetValue('show_sql',0,true);

$o = new stdClass();
$o->contents_fk = $the_module->contents_pk;
$o->placement   = "DETAIL_LEFT_COLUMN";
$o->link_order  = 4;

$result = Module::LinkModules($the_page->pages_pk, array($o));

clean_sql();

$modules = Module::GetPageModules($the_page->pages_pk);
$idx=0;
foreach($modules as $m)
{
	
	$idx++;
	assert_equal($m->contents_title, $data['contents_title'], __LINE__);
}

assert_equal($idx, 1 ,  __LINE__);


echo"<br> ======================================== create and retrieve a article <br>";
$CONFIG->SetValue('show_sql',0,true);

$data = array(
    'contents_title' 		=> 'article integration test'.$randomId,
    'contents_display_title'=>'article_display_title', 
    'contents_status' 		=>'DRAFT',
    'contents_author_fk'    =>$test_user_pk,

    'contents_version_comment'=> ' this is a test',
 	'contents_article_body' 			=>'this is the body',
 	'contents_article_type'		=> 'article type'
);
$article = new Article($data);
$pk = $article->Save();
Content::setLiveVersion($pk, 1 );

echo "created article pk : $pk <br>";
if($pk >0 )
	$revert[]= "delete from contents where contents_pk = $pk";
	
$the_article = Article::GetDetails($pk);
compare_data($data, $the_article);

assert_equal($test_user_pk, $the_article->contents_author_fk, __LINE__);


echo"<br> ======================================== target an article <br>";
$CONFIG->SetValue('show_sql',0,true);

$id= $the_page->pages_id;
$pk = $the_article->contents_pk;

$data = array( 'targets_contents_fk' => $pk,
			   'targets_pages_id'    => $id,
			   'targets_live_date'	 => '2011-11-11 00:00:00',
			   'targets_dead_date'	 => '2014-12-11 00:00:00',
			   'targets_archive_date'=> '2011-11-05 00:00:00',
               'targets_pin_position'=> 2,
               'record_state'        => 'NEW');

Page::sYaasSaveTarget($data);

$targets = Page::GetTargets($id);
$idx=0;
foreach($targets as $t)
{
	$idx++;
	assert_equal($t->targets_pin_position, 2, __LINE__);
	assert_equal($t->targets_live_date, '2011-11-11 00:00:00', __LINE__);
}
assert_equal($idx, 1, __LINE__);

clean_sql();
cleanup();

die(" <br><br>================ died before cleaning up test data ======================<br>");



//===========================================================================================
//===========================================================================================
function clean_sql()
{
    global $revert;
    echo("<br><br>========================== cleanup script ===========================<br>");
    
    $ar = array_reverse($revert);
    
    foreach($ar as $sql)
    {
    	echo(" $sql <br>"); 
    }   
}

function cleanup()
{
    
    global $revert;
    echo("<br><br>========================== deleting test data ===========================<br>");
    
    
    while($sql = array_pop($revert))
    {
    	$d = new Query($sql); 
    }  
    die("<br>================ end of tests ================<br>");
}


function assert_gt_zero($v1, $line)
{
	if($v1 != $v2)
	  terror("assertion error on line $line    $v1 should be greater than 0 ");
}

function assert_equal($v1, $v2, $line)
{
    $v1 = trim($v1);
    $v2 = trim($v2);
    
    if( is_int($v1) || is_int($v2) )
    {
        if (intval($v1) != intval($v2))
            terror("assertion error on line $line    $v1 should be equal to: $v2 ");
    }
    else
    {
	  if($v1 != $v2)
	       terror("assertion error on line $line    $v1 should be equal to: $v2 ");
    }
}

/**
 * 
 * compares an array with a data object
 * @param array  $data
 * @param object $retrieved
 */
function compare_data($data, $retrieved)
{
	foreach($data as $field => $val)
	{   
		$rv = $retrieved->$field;
		if($rv != $val)
			terror(" field: $field is $rv and it should be $val");
	}
}

$num_errors = 0;
function terror($str, $severity = 0)
{
	global $num_errors;
	echo("<br><b>$str</b><br>\n");
	if($severity >3)
	    die;
	    
	if(++ $num_errors > 20)
	   die;    
}

function show_sql($sql)
{
	dump($sql, FALSE);
}
