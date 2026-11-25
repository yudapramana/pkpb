<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $portalItems = \App\Models\PortalItem::active()->ordered()->get();

    return view('welcome', ['portal_items' => $portalItems]);
});

Route::get('/struktur-organisasi', [App\Http\Controllers\OrganizationStructureController::class, 'index'])
    ->name('organization.index');
Route::get('/struktur-organisasi/sederhana', [App\Http\Controllers\OrganizationStructureController::class, 'simple'])
    ->name('organization.simple');
Route::get('/struktur-organisasi/pohon', [App\Http\Controllers\OrganizationStructureController::class, 'tree'])
    ->name('organization.tree');

// Registrasi publik dimatikan: akun admin hanya dibuat lewat seeder/tinker.
Auth::routes(['register' => false]);

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.employees.index');
    })->name('dashboard');

    Route::resource('unit-kerja', App\Http\Controllers\Admin\WorkUnitController::class)
        ->except('show')
        ->names('work-units')
        ->parameters(['unit-kerja' => 'workUnit']);

    Route::resource('pegawai', App\Http\Controllers\Admin\EmployeeController::class)
        ->except('show')
        ->names('employees')
        ->parameters(['pegawai' => 'employee']);

    Route::resource('portal', App\Http\Controllers\Admin\PortalItemController::class)
        ->except('show')
        ->names('portal-items')
        ->parameters(['portal' => 'portalItem']);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
