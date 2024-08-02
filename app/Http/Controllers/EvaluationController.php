<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\Hotel;
use App\Models\System;
use App\Models\HotelSystem;
use App\Models\Question;
use App\Models\QuestionsHotel;
use App\Models\RecordEvaluation;
use App\Models\Evaluation;
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
        
            return view('admin.form_evaluacion', compact('preguntasPorSistema','hotelId'));
        }
        
        public function guardarEvaluacion(Request $request) {
            $hotelId = $request->input('hotel_id');
            $estatus = 0; // Por defecto, 0 sin terminar
        
            // Crear un registro en la tabla RecordEvaluation
            $recordEvaluation = RecordEvaluation::create([
                'hotel_id' => $hotelId,
                'estatus' => $estatus,
            ]);
        
            // Obtener el id del registro recién creado
            $recordId = $recordEvaluation->id;
        
            // Recorrer los sistemas y sus preguntas para guardarlas en la tabla Evaluation
            foreach ($request->input('sistemas') as $sistema) {
                $systemId = $sistema['system_id'];
                $numeroHabitacion = $sistema['numero_habitacion'] ?? null;
        
                foreach ($sistema['preguntas'] as $pregunta) {
                    $questionId = $pregunta['question_id'];
                    $respuesta = $pregunta['respuesta'];
                    $respuestaFecha = $pregunta['respuesta_fecha'] ?? null;
        
                    Evaluation::create([
                        'record_evaluation_id' => $recordId,
                        'system_id' => $systemId,
                        'question_id' => $questionId,
                        'answer' => $respuesta,
                        'date' => $respuestaFecha,
                        'room' => $numeroHabitacion,
                    ]);
                }
            }
        
            return redirect()->route('admin.hoteles');
        }
}
    

