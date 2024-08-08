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
        // Obtener los sistemas asociados con el hotel
        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->with('system')->get();
    
        $preguntasPorSistema = [];
    
        // Iterar sobre cada sistema del hotel
        foreach ($hotelSystems as $hotelSystem) {
            $system = $hotelSystem->system;
            $systemCantidad = $hotelSystem->cant;
    
            // Obtener las preguntas asociadas con el sistema para el hotel
            $questionsForSystem = Question::where('system_id', $system->id)
                ->whereHas('question_hotel', function ($query) use ($hotelId) {
                    $query->where('hotel_id', $hotelId);
                })
                ->get();
    
            // Repetir el sistema y las preguntas según la cantidad especificada
            for ($i = 1; $i <= $systemCantidad; $i++) {
                $preguntasPorSistema[] = [
                    'system' => $system->name . ' ' . $i,
                    'system_id' => $system->id,
                    'preguntas' => $questionsForSystem->map(function ($question) use ($hotelId) {
                        $cantidad = $question->question_hotel
                            ->where('hotel_id', $hotelId)
                            ->first()
                            ->cantidad;
                        
                        return [
                            'id' => $question->id,
                            'name' => $question->name,
                            'type' => $question->type,
                            'answer' => $question->answer,
                            'cantidad' => $cantidad
                        ];
                    })->toArray()
                ];
            }
        }
    
        return view('admin.form_evaluacion', compact('preguntasPorSistema', 'hotelId'));
    }
        
    public function guardarEvaluacion(Request $request) {
       
        $hotelId = $request->input('hotel_id');
        $status = $request->input('status',1);
        $faltanPreguntas = false;

        $recordEvaluation = RecordEvaluation::create([
            'hotel_id' => $hotelId,
            'status' => $status,
        ]);
        
        $recordId = $recordEvaluation->id;
    
        foreach ($request->input('sistemas') as $sistema) {
            $systemId = $sistema['system_id'];
            $numeroHabitacion = $sistema['numero_habitacion'] ?? null;
    
            foreach ($sistema['preguntas'] as $pregunta) {
                $questionId = $pregunta['pregunta_id'];
                $respuesta = $pregunta['respuesta'] ?? null;
                $respuestaFecha = $pregunta['respuesta_fecha'] ?? null;

                if (is_null($respuesta) || $respuesta === '') {
                    $status = '0'; // Si alguna respuesta está vacía, el estado es incompleto
                    $faltanPreguntas = true;
                }
    
                if ($questionId) {
                    
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
        }
        
        $recordEvaluation->status = $status;
        $recordEvaluation->save();

        if ($faltanPreguntas) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Evaluación guardada, pero faltaron preguntas por responder.');
        } else {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Evaluación guardada correctamente.');
        }
    }

    public function calcularPuntaje($recordId) {
        
        $evaluations = Evaluation::where('record_evaluation_id', $recordId)
            ->with('question')
            ->get();
    
        $scoresBySystem = [];
        $totalScore = 0;

        foreach ($evaluations as $evaluation) {
            $question = $evaluation->question;
            $systemId = $question->system_id;
            $expectedValue = $question->expected_value;
    
            $isCorrect = ($evaluation->answer == $expectedValue);
    
            $system = System::find($systemId);
            $systemScore = $system->score;
    
            if (!isset($scoresBySystem[$systemId])) {
                $scoresBySystem[$systemId] = [
                    'systemName' => $system->name,
                    'totalQuestions' => 0,
                    'correctAnswers' => 0,
                    'score' => 0,
                    'systemScore' => $systemScore
                ];
            }
    
            $scoresBySystem[$systemId]['totalQuestions']++;
            if ($isCorrect) {
                $scoresBySystem[$systemId]['correctAnswers']++;
            }
            $scoresBySystem[$systemId]['score'] = round(($scoresBySystem[$systemId]['correctAnswers'] / $scoresBySystem[$systemId]['totalQuestions']) * $systemScore,2);
        }
    
        // Calcular el puntaje total
        foreach ($scoresBySystem as $systemScore) {
            $totalScore += $systemScore['score'];
        }
    
        return view('admin.resultEvaluation', compact('scoresBySystem', 'totalScore'));
    }

    public function showEvalCompleted(Request $request,$hotelId)
    {

        $hotel = Hotel::find($hotelId);

        $query = $hotel->recordEvaluations();

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }
    
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status !== null) {
                $query->where('status', $status);
            }
        }
    
        $completedEvaluations = $query->get();


        return view('admin.eval_completed', compact('hotel', 'completedEvaluations'));
    }

    
        
}
    

