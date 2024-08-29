<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\Hotel;
use App\Models\System;
use App\Models\HotelSystem;
use App\Models\Question;
use App\Models\QuestionsHotel;
use App\Models\Accessories;
use Illuminate\Support\Facades\Auth;


class HotelConfigController extends Controller
{
    /*Esta funcion tiene el propósito de mostrar el formulario de configuración para un hotel específico.*/
    public function showConfigForm($hotelId)
    {
        // Registrar la entrada del usuario al módulo de configuración
        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE CONFIGURACION',
        ));
        $audience->save();

        // Obtener la información del hotel especificado
        $hotel = Hotel::find($hotelId);

        // Obtener todos los sistemas disponibles
        $sistemas = System::all(); 
        return view('admin.eval_config', ['hotel' => $hotel, 'sistemas' => $sistemas]);
    }

    /*Esta funcion tiene el propósito de guardar la configuración de sistemas para un hotel específico, 
    basándose en los datos proporcionados en la solicitud*/

    public function saveConfiguracion(Request $request) {
        // Obtener el ID del hotel y los datos de sistemas desde la solicitud
        $hotelId = $request->input('hotel_id');
        $sistemas = $request->input('sistemas');
        
        // Verificar que se han proporcionado datos de sistemas     
        if (!is_null($sistemas)) {
            // Iterar sobre cada sistema y sus datos asociados
            foreach ($sistemas as $sistemaId => $data) {
                $cantidad = $data['cantidad'];
        
                // Guardar o actualizar la configuración en la base de datos
                HotelSystem::create([
                    'hotel_id' => $hotelId,
                    'system_id' => $sistemaId,
                    'cant' => $cantidad,
                ]);
            }
        
            return response()->json(['success' => true, 'message' => 'Configuración guardada con éxito'], 200);
        } 
        return response()->json(['success' => false, 'message' => 'No se han proporcionado sistemas para configurar'], 400);
    }

    /*Esta funcion está diseñada para recuperar y mostrar preguntas agrupadas para los sistemas de un hotel específico.*/
    public function showQuestionsForSystems($hotelId) {

        // Obtener los sistemas del hotel, incluyendo las preguntas asociadas
        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)
            ->with('system.questions')
            ->where('cant', '>', 0)
            ->get();
    
        // Inicializar un array para almacenar las preguntas agrupadas
        $questionsGrouped = [];
    
        foreach ($hotelSystems as $hotelSystem) {
            if ($hotelSystem->system) {
                // Verificar si el sistema tiene un ID específico (por ejemplo, ID 12 para habitaciones)
                if ($hotelSystem->system->id === 12) { 
                    // Obtener preguntas del sistema y agruparlas por accesorio
                    $questionsGrouped[$hotelSystem->system->id] = Question::where('system_id', $hotelSystem->system->id)
                        ->orderBy('accessorie_id')
                        ->get()
                        ->groupBy('accessorie_id');
                } else {
                    // Para otros sistemas, simplemente asignar las preguntas asociadas
                    $questionsGrouped[$hotelSystem->system->id] = $hotelSystem->system->questions;
                }
            }
        }
        // Obtener todos los accesorios con sus nombres y IDs
        $accessories = Accessories::all()->pluck('name', 'id')->toArray();
    
        return view('admin.question_config', [
            'hotelId' => $hotelId,
            'hotelSystems' => $hotelSystems,
            'questionsGrouped' => $questionsGrouped,
            'accessories' => $accessories
        ]);
    }

    /*Esta funcion está diseñada para guardar la configuración de preguntas asociadas a un hotel, 
    utilizando los datos enviados desde un formulario. */
    
    public function savePreguntas(Request $request)
    {
        // Obtener el ID del hotel desde el formulario
        $hotelId = $request->input('hotel_id');

        // Obtener las preguntas seleccionadas desde el formulario
        $selectedQuestions = $request->input('selected_questions');

        // Verificar si $selectedQuestions no es nulo y es un array
        if ($selectedQuestions && is_array($selectedQuestions)) {
            foreach ($selectedQuestions as $questionId => $questionData) {
                // Verificar si 'question_id' y 'cantidad' existen en $questionData
                if (isset($questionData['question_id'], $questionData['cantidad'])) {
                    $questionsHotel = new QuestionsHotel();
                    $questionsHotel->hotel_id = $hotelId;
                    $questionsHotel->question_id = $questionData['question_id'];
                    $questionsHotel->cantidad = $questionData['cantidad'];
                    $questionsHotel->save();
                }
            }
        }

        // Redirigir o mostrar un mensaje de éxito
        return response()->json(['success' => true, 'message' => 'Configuración guardada con éxito'], 200);
    }
}
