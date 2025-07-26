<?php

use App\Http\Controllers\AboutVillageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintResponseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailSubmissionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VillageStructureController;
use App\Http\Controllers\Frontend\NewsController as FrontendNewsController;
use App\Http\Controllers\Frontend\ComplaintsController as FrontendComplaintsController;
use App\Models\News;
use App\Models\Complaint;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    // Get all published news
    $news = News::where('status', 'published')->get();
    
    // Get all active complaints (public ones)
    $complaints = Complaint::latest()->get();
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Main pages
    $mainPages = [
        ['url' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
        ['url' => url('/berita'), 'changefreq' => 'daily', 'priority' => '0.9'],
        ['url' => url('/pengaduan'), 'changefreq' => 'daily', 'priority' => '0.8'],
    ];
    
    foreach ($mainPages as $page) {
        $sitemap .= '  <url>' . "\n";
        $sitemap .= '    <loc>' . $page['url'] . '</loc>' . "\n";
        $sitemap .= '    <lastmod>' . now()->toISOString() . '</lastmod>' . "\n";
        $sitemap .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
        $sitemap .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
        $sitemap .= '  </url>' . "\n";
    }
    
    // Add news pages
    foreach ($news as $newsItem) {
        $sitemap .= '  <url>' . "\n";
        $sitemap .= '    <loc>' . route('frontend.news.show', $newsItem->id) . '</loc>' . "\n";
        $sitemap .= '    <lastmod>' . $newsItem->updated_at->toISOString() . '</lastmod>' . "\n";
        $sitemap .= '    <changefreq>weekly</changefreq>' . "\n";
        $sitemap .= '    <priority>0.7</priority>' . "\n";
        $sitemap .= '  </url>' . "\n";
    }
    
    // Add complaint pages (if they should be public)
    foreach ($complaints->take(50) as $complaint) { // Limit to 50 most recent
        $sitemap .= '  <url>' . "\n";
        $sitemap .= '    <loc>' . route('frontend.complaints.show', $complaint->id) . '</loc>' . "\n";
        $sitemap .= '    <lastmod>' . $complaint->updated_at->toISOString() . '</lastmod>' . "\n";
        $sitemap .= '    <changefreq>weekly</changefreq>' . "\n";
        $sitemap .= '    <priority>0.6</priority>' . "\n";
        $sitemap .= '  </url>' . "\n";
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap, 200)->header('Content-Type', 'application/xml');
})->name('sitemap.xml');

Route::get('/robots.txt', function () {
    $content = "# Robots.txt for Portal Parakan - Desa Parakan\n";
    $content .= "# Website: " . url('/') . "\n\n";
    
    $content .= "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Allow: /berita*\n";
    $content .= "Allow: /pengaduan*\n";
    $content .= "Allow: /assets/\n";
    $content .= "Allow: /storage/\n\n";
    
    $content .= "# Disallow admin and private areas\n";
    $content .= "Disallow: /admin*\n";
    $content .= "Disallow: /dashboard*\n";
    $content .= "Disallow: /login*\n";
    $content .= "Disallow: /register*\n";
    $content .= "Disallow: /password*\n";
    $content .= "Disallow: /profile*\n";
    $content .= "Disallow: /logout*\n";
    $content .= "Disallow: /api*\n\n";
    
    $content .= "# Disallow temporary and cache directories\n";
    $content .= "Disallow: /vendor/\n";
    $content .= "Disallow: /node_modules/\n";
    $content .= "Disallow: /.env\n";
    $content .= "Disallow: /composer.json\n";
    $content .= "Disallow: /package.json\n\n";
    
    $content .= "# Sitemap location\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n\n";
    
    $content .= "# Crawl-delay for respectful crawling\n";
    $content .= "Crawl-delay: 1\n";

    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots.txt');

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/register', [AuthController::class, 'create'])->name('register');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Static pages
Route::get('/', [DashboardController::class, 'welcome'])->name('welcome');

// Frontend routes
Route::get('/berita', [FrontendNewsController::class, 'index'])->name('frontend.news.index');
Route::get('/berita/{id}', [FrontendNewsController::class, 'show'])->name('frontend.news.show');
Route::get('/pengaduan', [FrontendComplaintsController::class, 'index'])->name('frontend.complaints.index');
Route::get('/pengaduan/{id}', [FrontendComplaintsController::class, 'show'])->name('frontend.complaints.show');
// Route::get('/', function () {
//     return view('frontend.index');
// })->name('welcome');

Route::get('/terms', function () {
    return view('static.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('static.privacy');
})->name('privacy');

Route::get('/password/reset', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard-masyarakat', [DashboardController::class, 'masyarakat'])->name('dashboard.masyarakat');

    Route::resource('dashboard', DashboardController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', ])
        ->names([
            'index' => 'dashboard.index',
            'create' => 'dashboard.create',
            'store' => 'dashboard.store',
            'show' => 'dashboard.show',
            'edit' => 'dashboard.edit',
            'update' => 'dashboard.update',
            'destroy' => 'dashboard.destroy',
        ]);
    
    Route::delete('/news/multiple-delete', [NewsController::class, 'multipleDelete'])
        ->name('news.multipleDelete');
    Route::resource('news', NewsController::class);

    Route::resource('aboutvillage', AboutVillageController::class);
    Route::resource('villagestructure', VillageStructureController::class)
        ->only(['index', 'edit', 'update']);
    
    Route::get('complaints/pending', [ComplaintController::class, 'pending'])
        ->name('complaints.pending');

    Route::resource('complaints', ComplaintController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names([
            'index' => 'complaints.index',
            'create' => 'complaints.create',
            'store' => 'complaints.store',
            'show' => 'complaints.show',
            'edit' => 'complaints.edit',
            'update' => 'complaints.update',
            'destroy' => 'complaints.destroy',
        ]);
    
    // Custom route must come before resource route
    Route::get('complaint-response/complaint/{id}', [ComplaintResponseController::class, 'getComplaintDetails'])
        ->name('complaint-response.get-complaint-details');
    Route::resource('complaint-response', ComplaintResponseController::class);

    // Mail submission custom routes
    Route::patch('mail-submissions/{mailSubmission}/status', [MailSubmissionController::class, 'updateStatus'])
        ->name('mail-submissions.update-status');
    Route::post('mail-submissions/{mailSubmission}/generate-pdf', [MailSubmissionController::class, 'generatePdf'])
        ->name('mail-submissions.generate-pdf');
    Route::get('mail-submissions/{mailSubmission}/download-pdf', [MailSubmissionController::class, 'downloadPdf'])
        ->name('mail-submissions.download-pdf');
    
    Route::resource('mail-submissions', MailSubmissionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names([
            'index' => 'mail-submissions.index',
            'create' => 'mail-submissions.create',
            'store' => 'mail-submissions.store',
            'show' => 'mail-submissions.show',
            'edit' => 'mail-submissions.edit',
            'update' => 'mail-submissions.update',
            'destroy' => 'mail-submissions.destroy',
        ]);
    
    Route::resource('users', UserController::class);
    Route::delete('/users/multiple-delete', [UserController::class, 'multipleDelete'])->name('users.multipleDelete');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});