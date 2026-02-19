<?php

use App\Http\Controllers\RekapanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RekapanController::class, 'index']);
Route::post('/upload-rekapan', [RekapanController::class, 'upload'])->name('upload-rekapan');
Route::get('/download-template', [RekapanController::class, 'downloadTemplate'])->name('download-template');
Route::get('/rekapan', [RekapanController::class, 'rekapanView'])->name('rekapan-view');
Route::get('/barang-detail/{code}', [RekapanController::class, 'barangDetail'])->name('barang-detail');
Route::get('/manage-kategori', [RekapanController::class, 'manageKategori'])->name('manage-kategori');
Route::post('/update-kategori', [RekapanController::class, 'updateKategori'])->name('update-kategori');
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});
