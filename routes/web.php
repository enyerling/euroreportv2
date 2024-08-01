<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\HotelConfigController;
use app\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

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

Auth::routes();

Route::get('/', function(){
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [HotelController::class, 'showAll'])->name('admin.dashboard');
    Route::get('/admin/hoteles', [HotelController::class, 'hotels'])->name('admin.hoteles');
    Route::get('/admin/eval_config/{hotelId}', [HotelConfigController::class, 'showConfigForm'])->name('hotel_config');
    
    Route::post('/admin/question_config', [HotelConfigController::class, 'guardarConfiguracion'])->name('admin.guardar_configuracion');
    Route::get('/admin/question_config/{hotelId}', [HotelConfigController::class, 'showQuestionsForSystems'])->name('admin.question_config');
    Route::post('/admin/hoteles', [HotelConfigController::class, 'guardarPreguntas'])->name('guardar_preguntas');
    Route::get('/admin/form_evaluacion/{hotelId}', [EvaluationController::class, 'mostrarPreguntasEval'])->name('admin.motrar_evaluacion');
});
