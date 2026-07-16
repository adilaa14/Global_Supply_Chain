<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://news.google.com/rss/search?q=logistics&hl=en-US&gl=US&ceid=US:en");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
curl_close($ch);
$xml = simplexml_load_string($output, 'SimpleXMLElement', LIBXML_NOCDATA);
echo $xml->channel->item[0]->description;
