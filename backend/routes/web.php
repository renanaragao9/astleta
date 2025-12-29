<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/sitemap.xml', function () {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $sitemap .= '  <url>'."\n";
    $sitemap .= '    <loc>https://astleta.com/</loc>'."\n";
    $sitemap .= '    <lastmod>'.date('Y-m-d').'</lastmod>'."\n";
    $sitemap .= '    <changefreq>weekly</changefreq>'."\n";
    $sitemap .= '    <priority>1.0</priority>'."\n";
    $sitemap .= '  </url>'."\n";
    // Adicionar mais URLs conforme necessário
    $sitemap .= '</urlset>';

    return response($sitemap, 200)->header('Content-Type', 'application/xml');
});
