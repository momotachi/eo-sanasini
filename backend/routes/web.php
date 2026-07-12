<?php

use Illuminate\Support\Facades\Route;

// Frontend publik di-handle Nuxt (port 3001).
// API ada di /api/* (routes/api.php). Admin di /admin (Filament).
// Route root "/" diarahkan ke /admin.
Route::redirect('/', '/admin');
