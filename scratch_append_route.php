<?php
$content = file_get_contents('routes/web.php');
$content .= "\n// Fallback for removed Trade Intelligence routes to prevent 404 on refresh\nRoute::get('/trade/{any?}', function () {\n    return redirect('/dashboard');\n})->where('any', '.*');\n";
file_put_contents('routes/web.php', $content);
echo "Appended redirect to web.php\n";
