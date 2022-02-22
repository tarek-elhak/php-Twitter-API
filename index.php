<?php
ini_set("display_errors",1);

include "twitter-conf.php";


// sending requests
$searchWord = "laravel";
$query = $searchWord;

// getting the recent 10k results of a search word

$recentParams = [
    "query" => $query . " lang:en -is:retweet",
    "tweet.fields" => "created_at",
    "expansions" => "author_id",
    "max_results" => 100,
];

$recentTweets = array();

// getting the top 10k results of a search word

$topParams = [
    "query" => $query . " lang:en -is:retweet",
    "tweet.fields" => "created_at,public_metrics",
    "expansions" => "author_id",
    "max_results" => 100,
    "sort_order" => "relevancy"
];

$topTweets = array();
/*
 * to get the recent 10k
 *  use the $recentParams in the request
 *  use the $recentTweets assoc array
*/

/*
 * to get the top 10k
 *  use the $topParams in the request
 *  use the $topTweets assoc array
*/

for ($counter = 0 ; $counter < 100 ; $counter++):
    $results = $connection->get("tweets/search/recent" , $recentParams);
    foreach ($results->data as $tweet):
        $recentTweets[] = [
            "tweet" => $tweet->text,
            "userId" => $tweet->author_id,
            "dateTime" => $tweet->created_at
        ];
    endforeach;
    // the next page
    if (isset($results->meta->next_token)){
        $params["next_token"] = $results->meta->next_token;
    }else{
        break;
    }
endfor;

$conn = new MongoDB\Driver\Manager("mongodb://localhost:27017");
// store the recent tweets in the mongo twitter db in recent collection
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert($recentTweets);
//$conn->executeBulkWrite("twitter.recent",$bulk);
// store the top tweets in the mongo twitter db in top collection
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert($topTweets);
$conn->executeBulkWrite("twitter.top",$bulk);
