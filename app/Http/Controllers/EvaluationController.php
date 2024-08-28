<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accessories;
use App\Models\Audience;
use App\Models\Hotel;
use App\Models\System;
use App\Models\HotelSystem;
use App\Models\Question;
use App\Models\QuestionsHotel;
use App\Models\RecordEvaluation;
use App\Models\Evaluation;
use App\Models\Observations;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class EvaluationController extends Controller
{

    public function showPreguntasEval($hotelId) {
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
                ->with('accessorie') // Eager load the accessorie relationship
                ->get();
    
            // Repetir el sistema y las preguntas según la cantidad especificada
            for ($i = 1; $i <= $systemCantidad; $i++) {
                $preguntasPorSistema[] = [
                    'system' => $system->name . ' ' . $i,
                    'system_id' => $system->id,
                    'instance' => $i, 
                    'preguntas' => $questionsForSystem->map(function ($question) use ($hotelId) {
                        $cantidad = $question->question_hotel
                            ->where('hotel_id', $hotelId)
                            ->first()
                            ->cantidad;
    
                        $accessorieName = $question->accessorie ? $question->accessorie->name : '';
    
                        return [
                            'id' => $question->id,
                            'name' => $question->name,
                            'type' => $question->type,
                            'answer' => $question->answer,
                            'cantidad' => $cantidad,
                            'accessorie_name' => $accessorieName
                        ];
                    })->toArray()
                ];
            }
        }
        
        return view('admin.form_evaluacion', compact('preguntasPorSistema', 'hotelId'));
    }
    
        
    public function saveEvaluacion(Request $request) {  
    try { 
        $hotelId = $request->input('hotel_id');
        $status = $request->input('status', 1);
        
        // Crear el registro de evaluación
        $recordEvaluation = RecordEvaluation::create([
            'hotel_id' => $hotelId,
            'status' => $status,
        ]);
    
        $recordId = $recordEvaluation->id;
        $isComplete = true;
    
        foreach ($request->input('sistemas') as $sistemaIndex => $sistema) {
            $systemId = $sistema['system_id'];
            $instance = $sistema['instance'];
            $preguntas = $sistema['preguntas'];
    
            foreach ($preguntas as $preguntaId => $pregunta) {
                $questionId = $pregunta['pregunta_id'] ?? null;
                $respuesta = $pregunta['respuesta'] ?? null;
                $respuestaFecha = $pregunta['respuesta_fecha'] ?? null;
                $instancePregunta = $pregunta['instance'] ?? $instance; 
    
                // Verificar que se haya enviado una respuesta válida
                if (is_null($respuesta) || $respuesta === '') {
                    $status = '0'; // Si alguna respuesta está vacía, el estado es incompleto
                    $isComplete = false;
                }
    
                if ($questionId) {
                    // Guardar la evaluación
                    Evaluation::create([
                        'record_evaluation_id' => $recordId,
                        'system_id' => $systemId,
                        'question_id' => $questionId,
                        'answer' => $respuesta,
                        'date' => $respuestaFecha,
                        'room' => $sistema['numero_habitacion'] ?? null,
                        'instance' => $instancePregunta, // Usar la instancia correcta
                    ]);
                }
            }
        }
    
        $recordEvaluation->status = $status;
        $recordEvaluation->save();

        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE EVALUACION',
        ));
        $audience->save();
    
        return response()->json([
            'status' => $isComplete ? 'success' : 'warning',
            'message' => $isComplete ? 'Evaluación guardada con éxito.' : '¡Atención! Recuerda que hay preguntas por responder.',
            'hotelId' => $hotelId
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar la solicitud.'], 500);
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

        $record = RecordEvaluation::find($recordId);
        $hotel = $record->hotel; 
        $hotelName = $hotel->name;
        $managerName = $hotel->manager; 
        $issueDate = now()->format('d-m-Y');

        $observations = Observations::where('record_evaluation_id', $recordId)->get();
    
        return view('admin.resultEvaluation', ['scoresBySystem' => $scoresBySystem,'totalScore' => $totalScore, 'observations' => $observations,
        'recordId' => $recordId , 'hotelName' => $hotelName,'managerName' => $managerName,'issueDate' => $issueDate])->render();
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

    public function showEvaluation($recordId)
    {
        $evaluations = Evaluation::where('record_evaluation_id', $recordId)
        ->with(['question', 'system'])
        ->get();

        $recordEvaluation = RecordEvaluation::find($recordId);
        $hotel = $recordEvaluation->hotel;

        $hotelName = $hotel->name;
        $hotelmanager= $hotel->manager;

        return view('admin.show_evaluation', ['evaluations' => $evaluations, 'hotelName' => $hotelName,'managerName' => $hotelmanager]);
    }

    public function editarEvaluacion($recordEvaluationId)
    {
        // Obtener el registro de evaluación y las respuestas asociadas
        $recordEvaluation = RecordEvaluation::findOrFail($recordEvaluationId);
        $hotelId = $recordEvaluation->hotel_id;
    
        // Obtener los sistemas y las preguntas asociadas con el hotel
        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->with('system')->get();
    
        $preguntasPorSistema = [];
        foreach ($hotelSystems as $hotelSystem) {
            $system = $hotelSystem->system;
            $systemCantidad = $hotelSystem->cant;
    
            // Obtener las preguntas asociadas con el sistema para el hotel
            $questionsForSystem = Question::where('system_id', $system->id)
                ->whereHas('question_hotel', function ($query) use ($hotelId) {
                    $query->where('hotel_id', $hotelId);
                })
                ->with('accessorie')
                ->get();
    
            for ($i = 1; $i <= $systemCantidad; $i++) {
                $preguntasPorSistema[] = [
                    'system' => $system->name . ' ' . $i,
                    'system_id' => $system->id,
                    'instance' => $i,
                    'preguntas' => $questionsForSystem->map(function ($question) use ($hotelId, $recordEvaluationId, $i) {
                        $cantidad = $question->question_hotel
                            ->where('hotel_id', $hotelId)
                            ->first()
                            ->cantidad;
    
                        // Obtener las respuestas para la instancia actual
                        $answer = Evaluation::where('record_evaluation_id', $recordEvaluationId)
                            ->where('system_id', $question->system_id)
                            ->where('question_id', $question->id)
                            ->where('instance', $i)
                            ->first();
                        
                        $accessorieName = $question->accessorie ? $question->accessorie->name : '';
    
                        return [
                            'id' => $question->id,
                            'name' => $question->name,
                            'type' => $question->type,
                            'answer' => $answer ? $answer->answer : null,
                            'respuesta_fecha' => $answer ? $answer->date : null,
                            'room' => $answer ? $answer->room : null,
                            'cantidad' => $cantidad,
                            'accessorie_name' => $accessorieName,
                        ];
                    })->toArray()
                ];
            }
        }
    
        return view('admin.eval_edit', compact('preguntasPorSistema', 'recordEvaluation', 'hotelId'));
    }
     
    public function actualizarEvaluacion(Request $request)
    {
        $recordEvaluationId = $request->input('evaluationId');
        $hotelId = $request->input('hotel_id');

        $recordEvaluation = RecordEvaluation::findOrFail($recordEvaluationId);
        $recordEvaluation->status = $request->input('status', $recordEvaluation->status);
        $recordEvaluation->save();

        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->get()->keyBy('system_id');
        $isComplete = true;

        foreach ($request->input('sistemas') as $sistemaIndex => $sistema) {
            $systemId = $sistema['system_id'];
            $cantidad = $hotelSystems[$systemId]->cant;
            $numeroHabitacion = $sistema['numero_habitacion'] ?? null;

            foreach ($sistema['preguntas'] as $preguntaId => $pregunta) {
                $respuesta = $pregunta['respuesta'] ?? null;
                $respuestaFecha = $pregunta['respuesta_fecha'] ?? null;
                $instance = $sistema['instance'];

                if (is_null($respuesta) || $respuesta === '') {
                    $isComplete = false;
                }

                Evaluation::updateOrCreate(
                    [
                        'record_evaluation_id' => $recordEvaluationId,
                        'system_id' => $systemId,
                        'question_id' => $preguntaId,
                        'instance' => $instance
                    ],
                    [
                        'answer' => $respuesta,
                        'date' => $respuestaFecha,
                        'room' => $numeroHabitacion

                    ]
                );
            }
        }
        $recordEvaluation->status = $isComplete ? 1 : 0;
        $recordEvaluation->save();

        return redirect()->back()->with([
            'status' => $isComplete ? 'success' : 'warning',
            'message' => $isComplete ? 'Evaluación editada guardada con éxito.' : '¡Atención! Recuerda que hay preguntas por responder.',
        ])->with('hotelId', $hotelId);
    }


    public function destroy($recordId)
    {
        $recordEvaluation = RecordEvaluation::findOrFail($recordId);
        $recordEvaluation->delete();

       return back();
    }

    public function enviarResultadoPorCorreo($evaluationId)
    {
        $viewContent = $this->calcularPuntaje($evaluationId);

        $hotelId = RecordEvaluation::where('id', $evaluationId)->value('hotel_id');

        $usuarios = User::where('hotel_id', $hotelId)->get();

        foreach ($usuarios as $usuario) {
            Mail::html($viewContent, function ($message) use ($usuario) {
                $message->to($usuario->email)
                        ->subject('Resultados de la Evaluación');
            });
        }
        return redirect()->route('admin.detalles_evaluacion', ['evaluationId' => $evaluationId]);
    }


}
    

