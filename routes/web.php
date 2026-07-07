<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

    Route::get('/',[UserController::class,'showDataInHome'])->name('home');
    Route::get('/fullpost/{id}', [UserController::class, 'showFullPost'])->name('fullpost');
    Route::post('/post{post}/comment', [CommentController::class, 'store'])->middleware('auth')->name('comment.store');
    Route::get('/dashboard', [UserController::class, 'home'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::post('/post/{post}/react', [ReactionController::class, 'store'])->middleware('auth')->name('reaction.store');
    Route::delete('/post/{post}/react', [ReactionController::class, 'destroy'])->middleware('auth')->name('reaction.destroy');
    Route::get('/post/{post}/comments', [CommentController::class, 'fetch'])->name('comment.fetch');
    Route::post('/post/{post}/comment', [CommentController::class, 'store'])->middleware('auth')->name('comment.store');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function(){
    Route::get('/dashboard', [UserController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/addpost',[AdminController::class, 'addpost'])->name('admin.addpost');
    Route::post('/dashboard/addpost',[AdminController::class, 'createpost'])->name('admin.createpost');
    Route::get('/dashboard/allpost',[AdminController::class, 'allpost'])->name('admin.allpost');
    Route::get('/dashboard/allpost/{id}',[AdminController::class, 'updatePost'])->name('admin.update');
    Route::post('/dashboard/allpost/{id}',[AdminController::class, 'postupdate'])->name('admin.postupdate');
    Route::get('/dashboard/delete/{id}',[AdminController::class, 'deletePost'])->name('admin.deletepost');
    Route::post('/dashboard/delete/{id}',[AdminController::class, 'postDelete'])->name('admin.postdelete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
