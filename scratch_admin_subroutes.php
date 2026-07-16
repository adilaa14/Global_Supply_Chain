<?php
$content = file_get_contents('routes/web.php');

// Replace the closing group bracket with the new routes and the closing bracket
$search = "    })->name('dashboard');\n});";
$replace = "    })->name('dashboard');\n\n" .
"    Route::get('/users', function () {\n" .
"        return Inertia\\Inertia::render('Admin/Users', [\n" .
"            'users' => App\\Models\\User::with('company')->latest()->paginate(10)\n" .
"        ]);\n" .
"    })->name('users');\n\n" .
"    Route::get('/ports', function () {\n" .
"        return Inertia\\Inertia::render('Admin/Ports', [\n" .
"            'ports' => App\\Models\\Port::with('country')->latest()->paginate(10)\n" .
"        ]);\n" .
"    })->name('ports');\n\n" .
"    Route::get('/articles', function () {\n" .
"        return Inertia\\Inertia::render('Admin/Articles', [\n" .
"            'articles' => App\\Models\\News::latest()->paginate(10)\n" .
"        ]);\n" .
"    })->name('articles');\n" .
"});";

if (strpos($content, $search) !== false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents('routes/web.php', $content);
    echo "Added admin sub-routes to web.php\n";
} else {
    echo "Could not find the target string in web.php\n";
}
