<?php
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
$json = file_get_contents('https://restcountries.com/v3.1/all', false, stream_context_create($arrContextOptions));
$allCountries = json_decode($json, true);
echo "Fetched " . count($allCountries) . "\n";
print_r(array_keys($allCountries));
print_r($allCountries[0] ?? null);
