<?php
$content = file_get_contents('routes/web.php');
$content .= "\n\n// Admin Dashboard Routes\nRoute::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {\n    Route::get('/dashboard', function () {\n        return Inertia\\Inertia::render('Admin/Dashboard', [\n            'users' => App\\Models\\User::count(),\n            'ports' => App\\Models\\Port::count(),\n            'articles' => App\\Models\\News::count(),\n        ]);\n    })->name('dashboard');\n});\n";
file_put_contents('routes/web.php', $content);
echo "Added admin routes to web.php\n";
