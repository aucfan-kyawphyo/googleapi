#!/usr/bin/env php
<?php
$stdins = array();
while(true)
{
    $stdin = trim(fgets(STDIN));
    if ($stdin === '==END==')
    {
        print json_encode(array(
        	"body" => join("\n", $stdins),
        	"coediting" => true,
        	"gist" => true,
        	"private" => false,
        	"tags" => array([ 
        		"name" => "開発G",
        		"versions" => [
        		"0.0.1"
        		]
         	]),
         	"title" => date("Ymd") . "次営業日の開発G作業予定",
         	"tweet" => false
        ), JSON_PRETTY_PRINT);
        return;
    }
    $stdins[] = $stdin;
}
?>
