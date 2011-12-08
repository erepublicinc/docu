<?php
require_once('inc.basic.php');



$items      = Author::GetAuthors4User(1); 
$items->SetAlias(array('pk'=>'authors_pk', 'title'=>'authors_display_name'));

$items->SetValue('title', 'testing');

//dump($items, false);

foreach($items as $i)
{
    echo "<br>p: $i->authors_display_name  $i->title sdsds";
}

die("<br> === end === 2");
