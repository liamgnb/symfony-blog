<?php
$queryString = http_build_query([
    'access_key' => '8c3e1847b7cf41fae9caf1667b46b5a2',
    'date' => (new DateTime())->format('Y-m-d'),
    'countries' => 'fr',
    'languages' => 'fr',
]);

$ch = curl_init(sprintf('%s?%s', 'http://api.mediastack.com/v1/news', $queryString));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$json = curl_exec($ch);

curl_close($ch);

$apiResult = json_decode($json, true);

print_r($apiResult);