
<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'settings', 'middleware' => ['auth', 'role:Admin']], function () {

    Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
        Route::get('/', [UsersController::class, 'index'])->name('users');
        Route::get('create', [UsersController::class, 'create'])->name('users.create');
        Route::post('store', [UsersController::class, 'store'])->name('users.store');
        Route::get('edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
        Route::get('show/{id}', [UsersController::class, 'show'])->name('users.show');
        Route::post('update', [UsersController::class, 'update'])->name('users.update');
        Route::post('destroy', [UsersController::class, 'destroy'])->name('users.destroy');
    });

    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions');
        Route::get('create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::get('show/{id}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::post('update', [PermissionController::class, 'update'])->name('permissions.update');
        Route::post('destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles');
        Route::get('create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::get('show/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::post('update', [RoleController::class, 'update'])->name('roles.update');
        Route::post('destroy', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('give-permissions/{id}', [RoleController::class, 'addPermissionToRole'])->name('roles.addPermissions');
        Route::post('give-permissions/{id}', [RoleController::class, 'givePermissionToRole'])->name('roles.givePermissions');
    });
});

Route::group(['prefix' => 'posts', 'middleware' => 'auth'], function () {
    Route::get('/', [PostsController::class, 'index'])->name('posts');
    Route::get('create', [PostsController::class, 'create'])->name('posts.create');
    Route::post('store', [PostsController::class, 'store'])->name('posts.store');
    Route::get('edit/{id}', [PostsController::class, 'edit'])->name('posts.edit');
    Route::get('show/{id}', [PostsController::class, 'show'])->name('posts.show');
    Route::post('update', [PostsController::class, 'update'])->name('posts.update');
    Route::post('destroy', [PostsController::class, 'destroy'])->name('posts.destroy');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
