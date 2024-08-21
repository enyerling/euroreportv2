<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\HotelConfigController;
use app\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ObservationController;
use App\HTTP\Controllers\Auth\LoginController;
use App\HTTP\Controllers\UserController;


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


Route::get('/', function(){
    return redirect()->route('login');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Auth::routes();

    Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HotelController::class, 'showAll'])->name('admin.dashboard');

    //hoteles
    Route::get('/hoteles', [HotelController::class, 'hotels'])->name('admin.hoteles');
    Route::post('save//hotel', [HotelController::class, 'store'])->name('admin.hoteles.store');
    Route::put('/update/hotel/{id}', [HotelController::class, 'update'])->name('admin.hoteles.update');
    Route::delete('/delete/hotel/{id}', [HotelController::class, 'destroy'])->name('admin.hoteles.destroy');

    //Configurar sistemas
    Route::get('/config/Systems/{hotelId}', [HotelConfigController::class, 'showConfigForm'])->name('hotel_config');
    Route::post('/save/Systems', [HotelConfigController::class, 'guardarConfiguracion'])->name('admin.guardar_configuracion');

    //Configurar preguntas
    Route::get('/config/Questions/{hotelId}', [HotelConfigController::class, 'showQuestionsForSystems'])->name('admin.question_config');
    Route::post('/save/Questions', [HotelConfigController::class, 'guardarPreguntas'])->name('guardar_preguntas');

    //Evaluacion
    Route::get('/form/evaluation/{hotelId}', [EvaluationController::class, 'mostrarPreguntasEval'])->name('admin.motrar_evaluacion');
    Route::post('/save/evaluation', [EvaluationController::class, 'guardarEvaluacion'])->name('admin.guardar_evaluacion');
    Route::get('/results/evaluation/{evaluationId}',[EvaluationController::class, 'calcularPuntaje'])->name('admin.detalles_evaluacion');
  
    Route::get('/showDetails/evaluations/{recordId}', [EvaluationController::class, 'showEvaluation'])->name('admin.ver_evaluacion');
    Route::get('/form/edit/evaluations/{evaluationId}', [EvaluationController::class, 'editarEvaluacion'])->name('admin.evaluacion_editar');
    Route::put('/update/evaluations/{evaluationId}', [EvaluationController::class, 'actualizarEvaluacion'])->name('admin.evaluacion_actualizar');
    Route::delete('/delete/evaluations/{evaluationId}', [EvaluationController::class, 'destroy'])->name('admin.eliminar_evaluacion');

    //Evaluaciones por hotel 
    Route::get('/show/evaluations/{hotelId}',[EvaluationController::class, 'showEvalCompleted'])->name('admin.evaluacioneshotel');

    //Observaciones
    Route::get('/observations/evaluations/{record_evaluation_id}',[ObservationController::class, 'observaciones'])->name('admin.observations');
    Route::post('/save/observations',[ObservationController::class, 'guardar_observations'])->name('admin.guardar_observations');

    
    

    //usuarios
    Route::get('/users', [UserController::class, 'showAll'])->name('admin.users');
    Route::post('/save/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/update/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/delete/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    



    Route::get('/enviar-resultados/{evaluationId}', [EvaluationController::class, 'enviarResultadoPorCorreo'])->name('enviar.resultados');
    });




        



