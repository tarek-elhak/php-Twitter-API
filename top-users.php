<?php

ini_set("display_errors",1);
include "twitter-conf.php";
include "fireDB-conf.php";

// get the top 20 users on Twitter List
$list_id = "1483822213157597185"; // example for a list id
$params = [
    "user.fields" => "public_metrics,created_at,description"
];

$result = $connection->get("lists/$list_id/members" , $params);



$members = sortListMembers($result->data);

$topMembers = array_slice($members,0,20);

$top20Members = array();

foreach($topMembers as $member):
    $top20Members [] = [
        "id" => $member->id,
        "name" => $member->name,
        "user_name" => $member->username,
        "description" => $member->description,
        "created_at" => $member->created_at,
        "followers_count" => $member->public_metrics->followers_count,
        "following_count" => $member->public_metrics->following_count,
        "tweet_count" => $member->public_metrics->tweet_count,
        "listed_count" => $member->public_metrics->listed_count
    ];
endforeach;

// store users' info in firebase
$reference->push($top20Members);
// helper function to sort the members Desc based on the followers count
function sortListMembers($members): array
{
    $sorted_members = array();

    for($counter=0 ; $counter<count($members) ; $counter++):
        if ($counter == 0 ){
            $sorted_members [] = $members[$counter];
            continue;
        }
        $shift = false;
        for ($y = count($sorted_members)-1 ; $y >= 0 ; $y--):
            if ($members[$counter]->public_metrics->followers_count > $sorted_members[$y]->public_metrics->followers_count){
                $sorted_members[$y+1] = $sorted_members[$y];
                $sorted_members[$y] = $members[$counter];
                $shift = true;
            }
        endfor;
        if (!$shift):
            $sorted_members[] = $members[$counter];
        endif;
    endfor;

    return $sorted_members;
}

