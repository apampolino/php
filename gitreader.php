<?php
// https:/api.github.com/user
$url = 'https://api.github.com/users/apampolino/gists';

$username = '';

$token = '';

$headers = ["Authorization: Basic " . base64_encode($username . ":" . $token)];

// Get cURL resource
$ch = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERAGENT => 'curl/7.52.1',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_HTTPAUTH => CURLAUTH_ANY
));

// Send the request & save response to $resp
$res = curl_exec($ch);

$info = curl_getinfo($ch);

// Close request to clear up some resources
curl_close($ch);

$data = json_decode($res, true);

foreach ($data as $gist) {

        $file_list = $gist['files'];

        foreach ($file_list as $file) {

                $url = $file['raw_url'];
                // Download all files
                exec("wget $url");
        }
}

exit;
