<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\Hotel;
use App\Models\System;
use App\Models\HotelSystem;
use App\Models\Question;
use App\Models\QuestionsHotel;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE EVALUACION',
        ));
        $audience->save();
    }

    /*public function mostrarPreguntasEval($hotelId) {
       
            // Obtener los sistemas para el hotel dado según la cantidad
            $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->with('system')->get();
        
            // Inicializar un array para almacenar las preguntas seleccionadas
            $preguntasSeleccionadas = [];
        
            // Obtener las preguntas seleccionadas para el hotel dado
            $questionHotels = QuestionsHotel::where('hotel_id', $hotelId)->with('question.system')->get();
        
            // Obtener todas las preguntas disponibles para el hotel dado
            $preguntasDisponibles = Question::whereHas('question_hotel', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })->with('system')->get();
        
            return view('admin.form_evaluacion', compact('hotelSystems', 'preguntasSeleccionadas', 'preguntasDisponibles', 'hotelId'));
        }*/

        public function mostrarPreguntasEval($hotelId) {
            $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->with('system')->get();
            
            $preguntasPorSistema = [];
        
            foreach ($hotelSystems as $hotelSystem) {
                $system = $hotelSystem->system;
                $cantidad = $hotelSystem->cant;
        
                if ($cantidad > 0) {
                    $preguntas = Question::whereHas('question_hotel', function ($query) use ($hotelId) {
                        $query->where('hotel_id', $hotelId);
                    })->where('system_id', $system->id)->get();
        
                    // Repetir el sistema y sus preguntas según la cantidad del sistema
                    for ($i = 1; $i <= $cantidad; $i++) {
                        $preguntasPorSistema[] = [
                            'system' => $system->name . ' ' . $i,
                            'system_id' => $system->id,
                            'preguntas' => $preguntas->toArray()
                        ];
                    }
                }
            }
        
            return view('admin.form_evaluacion', compact('preguntasPorSistema'));
        }

}
    

