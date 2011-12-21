<?php
require_once('inc.basic.php');

if(! User::Authorize('EDITOR'))
   die('unauthorized'); 
   
Query::SetAdminMode();

$test_user_id = $_SESSION['user_id'];

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
	'users_email' => "email{$randomId}@verylongdomain.com",
	'users_ad_user' => '',
	'users_active' => 1
);

$user   = new User($data);
$user_id = $user->Save();
assert_gt_zero($user_id, __LINE__); 

if($user_id)
   $revert[]= "delete from users where users_id = $user_id";

unset($data['users_password']);  // GetDetails does not retrieve the password   
   
// retrieve it into an array
$user = User::GetDetails($user_id);
compare_data($data, $user);

//======================= now we going to change the user
$ar = $user->ToArray();
$ar['users_email']   = "newemail{$randomId}@test.com";
$data['users_email'] = "newemail{$randomId}@test.com";

//save it with the new data 
$user2 = new User($ar);
$id2 = $user2->Save();

assert_equal($user_id, $id2, __LINE__);

// retrieve it again
$user2 = User::GetDetails($id2);

compare_data($data, $user2);


$result = User::SetRoles($user_id, array('GT_EDITOR'));

echo" ================================================ create and retrieve a author <br>";

$data = array(
    'authors_name'=> 'Test author'.$randomId,
    'authors_display_name'=> 'Test author display',
	'authors_bio'=>'this is the bio', 
    'authors_public_email'=> "test{$randomId}@test.com",
    'authors_users_id'=> $user_id,
	'authors_active' => 1
);

$author =  new Author($data);
$auhors_id = $author->Save();

$revert[]= "delete from authors where authors_id = $auhors_id";

$author_rec = Author::GetDetails($auhors_id);
compare_data($data, $author_rec);

// now change it
$rec = $author_rec->ToArray();
$rec['authors_bio']  = 'the new bio';
$data['authors_bio'] = 'the new bio';

//dump($rec, false);
$author2 =  new Author($rec);
$auhors_id2 = $author2->Save();
assert_equal($auhors_id2, $auhors_id, __LINE__);

$author_rec = Author::GetDetails($auhors_id2);
compare_data($data, $author_rec);


echo" ================================================ create and retrieve a page <br>";
$CONFIG->SetValue('show_sql',0,true);
$data = array('pages_title'=> 'page integration test'.$randomId,
	'pages_display_title'=>'pages_display_title', 
    'pages_is_live' => 1,
	'pages_is_preview' => 0,
    'pages_site_code' => 'GT',
    'pages_url' => 'testurl',
    'pages_no_robots' => 0,
    'pages_version_status' =>'READY',
 	'pages_php_class' =>'phpclass',
 	'pages_body' =>'this is the body',
    'pages_version_comment'=> ' this is a test'
);

$page = new Page($data);
$pages_id = $page->Save();
echo "created page id : $pages_id <br>";
if($pages_id >0 )
	$revert[]= "delete from pages where pages_id = $pages_id";
	
$the_page = Page::GetDetails($pages_id);

compare_data($data, $the_page);

echo" ================================================ change the page <br>";
$rec = $the_page->ToArray();
$rec['pages_body']  = 'the new body';
$data['pages_body'] = 'the new body';

//dump($rec, false);
$page2 =  new Page($rec);
$id2 = $page2->Save();

$rec = Page::GetDetails($id2);

assert_equal($the_page->id, $rec->id, __LINE__);

compare_data($data, $rec);



echo"<br> =========================================== create and retrieve a module <br>";
$CONFIG->SetValue('show_sql',0,true);

$data = array(
    'contents_title' 		=> 'module integration test'.$randomId,
    'contents_display_title'=>'pages_display_title',
 
    'contents_version_status' =>'READY',
    'contents_version_comment'=> ' this is a test',

    'modules_site_code' 	=> 'GT',
    'modules_json_params' 	=> 'json',
 	'modules_php_class' 	=>'phpclass',
 	'modules_body' 			=>'this is the body'

);
$ct = new Module($data);
$id = $ct->Save();
Content::setLiveVersion($id, 1 );

echo "created module id : $id <br>";
if($id >0 )
	$revert[]= "delete from contents where contents_id = $id";
	
$the_module = Module::GetDetails($id, false);
compare_data($data, $the_module);




echo"<br> =========================================== link  module to a page <br>";
$CONFIG->SetValue('show_sql',0,true);

$o = new stdClass();
$o->contents_id = $the_module->contents_id;
$o->placement   = "DETAIL_LEFT_COLUMN";
$o->link_order  = 4;

$result = Module::LinkModules($the_page->pages_rev, array($o));



$modules = Module::GetPageModules($the_page->pages_rev);
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
    'contents_authors_id'    =>$test_user_id,
    'contents_version_status' 		=>'DRAFT',
    'contents_version_comment'=> ' this is a test',
    'contents_pub_date' => '2011-12-09 12:12:00',

 	'contents_article_body' 			=>'this is the body',
 	'contents_article_type'		=> 'article type'
);
$article = new Article($data);
$id = $article->Save();
Content::setLiveVersion($id, 1 );

echo "created article id : $id <br>";
if($id >0 )
	$revert[]= "delete from contents where contents_id = $id";
	
$the_article = Article::GetDetails($id);
compare_data($data, $the_article);

assert_equal($test_user_id, $the_article->contents_authors_id, __LINE__);

echo" ================================================ change the article <br>";
$rec = $the_article->ToArray();
$rec['contents_article_body']  = 'the new article body';
$data['contents_article_body'] = 'the new article body';

//dump($rec, false);
$art2 = new Article($rec);
$id2  = $art2->Save();
assert_equal($id2, $id, __LINE__);

$rec = Article::GetDetails($id2, LATEST_VERSION);
compare_data($data, $rec);





echo"<br> ======================================== target an article <br>";
$CONFIG->SetValue('show_sql',0,true);

$pages_id = $the_page->pages_id;
$contents_id = $the_article->contents_id;

$data = array( 'targets_contents_id' => $contents_id,
			   'targets_pages_id'    => $pages_id,
			   'targets_live_date'	 => '2011-11-11 00:00:00',
			   'targets_dead_date'	 => '2014-12-11 00:00:00',
			   'targets_archive_date'=> '2011-11-05 00:00:00',
               'targets_pin_position'=> 2,
               'record_state'        => 'NEW');

Page::sYaasSaveTarget($data);

$targets = Page::GetTargets($pages_id);
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
	if(intval($v1) <= 0)
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
