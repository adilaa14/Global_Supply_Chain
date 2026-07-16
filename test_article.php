<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://news.google.com/rss/articles/CBMipwFBVV95cUxQVnZZT295V19TejRsd2ptQzU0LXlXcHhoT0MwY20yd3dCUW9vQ3lNOU8xSjRyOUIyNG5nWDNwSjBMSnNUUlhIc2ZrVkQtbEp4SVJKeE1GN2pvQ1FtbkFmdE5YcW04cHNrVmZCN1ZqSXZzOTFTNS0tLXZLMVFQemNTOXdmaGxOcXNpbXNzV2NqTnV4R2dJbWZRRFVTQzAxcFVkVVVUME4tZw?oc=5");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
curl_close($ch);
echo substr($output, 0, 500);
