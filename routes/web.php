<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\HotelConfigController;
use app\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ObservationController;

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

    //hoteles
    Route::get('/admin/hoteles', [HotelController::class, 'hotels'])->name('admin.hoteles');
    Route::post('/admin/hotel/save', [HotelController::class, 'store'])->name('admin.hoteles.store');
    Route::put('/admin/hotel/update/{id}', [HotelController::class, 'update'])->name('admin.hoteles.update');
    Route::delete('admin/hotel/delete/{id}', [HotelController::class, 'destroy'])->name('admin.hoteles.destroy');

    //Configurar sistemas
    Route::get('/admin/config/Systems/{hotelId}', [HotelConfigController::class, 'showConfigForm'])->name('hotel_config');
    Route::post('/admin/save/Systems', [HotelConfigController::class, 'guardarConfiguracion'])->name('admin.guardar_configuracion');

    //Configurar preguntas
    Route::get('/admin/config/Questions/{hotelId}', [HotelConfigController::class, 'showQuestionsForSystems'])->name('admin.question_config');
    Route::post('/admin/save/Questions', [HotelConfigController::class, 'guardarPreguntas'])->name('guardar_preguntas');

    //Evaluacion
    Route::get('/admin/form/evaluation/{hotelId}', [EvaluationController::class, 'mostrarPreguntasEval'])->name('admin.motrar_evaluacion');
    Route::post('/admin/save/evaluation', [EvaluationController::class, 'guardarEvaluacion'])->name('admin.guardar_evaluacion');
    Route::get('/admin/results/evaluation/{evaluationId}',[EvaluationController::class, 'calcularPuntaje'])->name('admin.detalles_evaluacion');
    Route::get('/admin/showDetails/evaluations/{recordId}', [EvaluationController::class, 'showEvaluation'])->name('admin.ver_evaluacion');
    Route::get('/admin/form/edit/evaluations/{recordEvaluationId}', [EvaluationController::class, 'editarEvaluacion'])->name('admin.evaluacion_editar');
    Route::put('/admin/update/evluations/{id}', [EvaluationController::class, 'actualizarEvaluacion'])->name('admin.evaluacion_actualizar');
    

    //Observaciones
    Route::get('/admin/observations/evaluations/{record_evaluation_id}',[ObservationController::class, 'observaciones'])->name('admin.observations');
    Route::post('/admin/save/observations',[ObservationController::class, 'guardar_observations'])->name('admin.guardar_observations');

    
    Route::get('/admin/show/evaluations/{hotelId}',[EvaluationController::class, 'showEvalCompleted'])->name('admin.evaluacioneshotel');
    

});
