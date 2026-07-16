<?php
$img = '6169052';
$url = "https://images.pexels.com/photos/{$img}/pexels-photo-{$img}.jpeg?auto=compress&cs=tinysrgb&w=800";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Pexels ($img): $code\n";
