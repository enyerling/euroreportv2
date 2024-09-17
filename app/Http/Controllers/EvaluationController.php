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
use App\Models\Images;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Mail\ResultadoEvaluacionMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EvaluationController extends Controller
{
    /*Esta funcion obtiene y estructurar las preguntas asociadas a los sistemas de un hotel específico, 
    según la configuración establecida en la base de datos. Luego, estas preguntas se muestran en una 
    vista de evaluación.*/

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
    
    /*Esta funcion guarda la evaluación de un hotel, registrando las respuestas a cada pregunta y actualizando 
    el estado de la evaluación. También registra la acción del usuario para fines de auditoría.*/
        
    public function saveEvaluacion(Request $request) {  
        try { 
            // Obtener datos del hotel y estado de la evaluación del request
            $hotelId = $request->input('hotel_id');
            $status = $request->input('status', 1);

            // Crear el registro de evaluación
            $recordEvaluation = RecordEvaluation::create([
                'hotel_id' => $hotelId,
                'status' => $status,
            ]);
        
            $recordId = $recordEvaluation->id;
            $isComplete = true;

            // Iterar sobre los sistemas y preguntas del request
            foreach ($request->input('sistemas', []) as $sistemaIndex => $sistema) {
                $systemId = $sistema['system_id'];
                $instance = $sistema['instance'];
                $preguntas = $sistema['preguntas'] ?? [];
        
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
            // Actualizar el estado final de la evaluación
            $recordEvaluation->status = $status;
            $recordEvaluation->save();

                // Registrar la acción del usuario para auditoría
                $user     = Auth::user();
                $audience = new Audience(array(
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'action' => 'INGRESO AL MODULO DE EVALUACION',
                ));
                $audience->save();
                
            // Retornar la respuesta en formato JSON
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                // Si la solicitud es AJAX, devuelve JSON
                return response()->json([
                    'status' => $isComplete ? 'success' : 'warning',
                    'message' => $isComplete ? 'Evaluación guardada con éxito.' : '¡Atención! Recuerda que hay preguntas por responder.',
                    'hotelId' => $hotelId
                ]);
            } else {
                // Si la solicitud no es AJAX, redirige a la página de resultados
                return redirect()->back()->with([
                    'status' => $isComplete ? 'success' : 'warning',
                    'message' => $isComplete ? 'Evaluación guardada con éxito.' : '¡Atención! Recuerda que hay preguntas por responder.',
                    'hotelId' => $hotelId
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar la solicitud.'], 500);
        }
    }
    
    /*Esta funcion calcula el puntaje de una evaluación específica, basándose en las respuestas proporcionadas 
    por el usuario,y genera una vista con los resultados detallados por sistema, incluyendo observaciones.*/

    public function calcularPuntaje($recordId) {
        // Obtener la evaluacion asociada a un record de evaluación
        $evaluations = Evaluation::where('record_evaluation_id', $recordId)
            ->with('question')
            ->get();
    
        $scoresBySystem = [];
        $totalScore = 0;

        $images = Images::where('record_evaluation_id', $recordId)->get();

        // Iterar sobre la evaluación para calcular el puntaje por sistema
        foreach ($evaluations as $evaluation) {
            $question = $evaluation->question;
            $systemId = $question->system_id;
            $expectedValue = $question->expected_value;
            
            // Verificar si la respuesta es correcta
            $isCorrect = ($evaluation->answer == $expectedValue);
    
            $system = System::find($systemId);
            $systemScore = $system->score;
            
            // Inicializar la estructura para almacenar puntajes por sistema
            if (!isset($scoresBySystem[$systemId])) {
                $scoresBySystem[$systemId] = [
                    'systemName' => $system->name,
                    'totalQuestions' => 0,
                    'correctAnswers' => 0,
                    'score' => 0,
                    'systemScore' => $systemScore
                ];
            }
            
            // Calcular el puntaje parcial del sistema
            $scoresBySystem[$systemId]['totalQuestions']++;
            if ($isCorrect) {
                $scoresBySystem[$systemId]['correctAnswers']++;
            }
            $scoresBySystem[$systemId]['score'] = round(($scoresBySystem[$systemId]['correctAnswers'] / $scoresBySystem[$systemId]['totalQuestions']) * $systemScore,2);
        }
    
        // Calcular el puntaje total sumando los puntajes de todos los sistemas
        foreach ($scoresBySystem as $systemScore) {
            $totalScore += $systemScore['score'];
        }

        // Obtener información adicional del hotel y las observaciones
        $record = RecordEvaluation::find($recordId);
        $hotel = $record->hotel; 
        $hotelName = $hotel->name;
        $managerName = $hotel->manager; 
        $issueDate = now()->format('d-m-Y');

        $observations = Observations::where('record_evaluation_id', $recordId)->get();
        
        // Retornar la vista con los resultados del puntaje y otros datos relevantes
        return view('admin.resultEvaluation', [
            'scoresBySystem' => $scoresBySystem,
            'totalScore' => $totalScore, 
            'observations' => $observations,
            'recordId' => $recordId , 
            'hotelName' => $hotelName,
            'managerName' => $managerName,
            'issueDate' => $issueDate,
            'images' => $images
            ])->render();
    }

    /*Esta funcion muestra las evaluaciones completadas para un hotel específico, 
    permitiendo filtrar por rango de fechas y estado, y genera una vista con los resultados filtrados.*/

    public function showEvalCompleted(Request $request,$hotelId)
    {
        // Obtener el hotel especificado
        $hotel = Hotel::find($hotelId);

        // Iniciar una consulta de las evaluaciones asociadas al hotel
        $query = $hotel->recordEvaluations();

        // Filtrar por rango de fechas si se especifican en la solicitud
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }
        
        // Filtrar por estado si se especifica en la solicitud
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status !== null) {
                $query->where('status', $status);
            }
        }
        
        // Obtener las evaluaciones completadas según los filtros aplicados
        $completedEvaluations = $query->get();

        // Retornar la vista con las evaluaciones y el hotel
        return view('admin.eval_completed', compact('hotel', 'completedEvaluations'));
    }

    /*Esta funcion muestra las evaluaciones asociadas a un registro específico, 
    incluyendo información sobre el hotel y su administrador, y renderiza una vista con estos datos.*/
    public function showEvaluation($recordId)
    {
        // Obtener las evaluaciones relacionadas con el registro dado
        $evaluations = Evaluation::where('record_evaluation_id', $recordId)
        ->with(['question', 'system'])
        ->get();

        // Obtener el registro de la evaluación y el hotel asociado
        $recordEvaluation = RecordEvaluation::find($recordId);
        $hotel = $recordEvaluation->hotel;

        // Obtener el nombre del hotel y el administrador
        $hotelName = $hotel->name;
        $hotelmanager= $hotel->manager;

        return view('admin.show_evaluation', ['evaluations' => $evaluations, 'hotelName' => $hotelName,'managerName' => $hotelmanager]);
    }

    /*Esta funcion permite editar una evaluación existente, obteniendo las respuestas asociadas y las preguntas 
    correspondientes a los sistemas del hotel para ser mostradas en una vista de edición.*/

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
                
            // Repetir las preguntas según la cantidad de sistemas
            for ($i = 1; $i <= $systemCantidad; $i++) {
                $preguntasPorSistema[] = [
                    'system' => $system->name . ' ' . $i,
                    'system_id' => $system->id,
                    'instance' => $i,
                    // Mapear preguntas y sus respuestas asociadas
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
                            'date' => $question->answer,
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
        // Renderizar la vista de edición de evaluación con los datos obtenidos
        return view('admin.eval_edit', compact('preguntasPorSistema', 'recordEvaluation', 'hotelId'));
    }

    /*Esta funcion se encarga de procesar y actualizar una evaluación existente en la aplicacion. 
    Permite actualizar las respuestas de las preguntas, verificar si la evaluación está completa 
    y cambiar su estado en consecuencia. */
     
    public function actualizarEvaluacion(Request $request)
    {
        // Obtener los datos de la evaluación y el hotel
        $recordEvaluationId = $request->input('evaluationId');
        $hotelId = $request->input('hotel_id');

        // Buscar la evaluación y actualizar su estado
        $recordEvaluation = RecordEvaluation::findOrFail($recordEvaluationId);
        $recordEvaluation->status = $request->input('status', $recordEvaluation->status);
        $recordEvaluation->save();

        // Obtener los sistemas del hotel y organizar por 'system_id'
        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->get()->keyBy('system_id');
        $isComplete = true; // Bandera para verificar si la evaluación está completa

        // Procesar cada sistema y sus preguntas
        foreach ($request->input('sistemas') as $sistemaIndex => $sistema) {
            $systemId = $sistema['system_id'];
            $cantidad = $hotelSystems[$systemId]->cant; 
            $numeroHabitacion = $sistema['numero_habitacion'] ?? null;

            // Procesar cada pregunta del sistema
            foreach ($sistema['preguntas'] as $preguntaId => $pregunta) {
                $respuesta = $pregunta['respuesta'] ?? null;
                $respuestaFecha = $pregunta['respuesta_fecha'] ?? null;
                $instance = $sistema['instance']; // Instancia del sistema

                // Verificar si la respuesta es nula o vacía
                if (is_null($respuesta) || $respuesta === '') {
                    $isComplete = false;
                }

                // Crear o actualizar la respuesta en la base de datos
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
        // Actualizar el estado de la evaluación 
        $recordEvaluation->status = $isComplete ? 1 : 0;
        $recordEvaluation->save();

        // Redirigir con un mensaje de éxito o advertencia
        return redirect()->back()->with([
            'status' => $isComplete ? 'success' : 'warning',
            'message' => $isComplete ? 'Evaluación editada guardada con éxito.' : '¡Atención! Recuerda que hay preguntas por responder.',
        ])->with('hotelId', $hotelId);
    }

    /*Esta funcion tiene como propósito eliminar un registro de evaluación específico basado en el ID proporcionado*/

    public function destroy($recordId)
    {
        $recordEvaluation = RecordEvaluation::findOrFail($recordId);
        // Eliminar el registro de evaluación de la base de datos
        $recordEvaluation->delete();

       return back();
    }

    /*Esta función calcular el puntaje de una evaluación, generar el contenido de la vista de los resultados, 
    y luego enviar esos resultados por correo electrónico a todos los usuarios asociados con el hotel 
    correspondiente a esa evaluación.*/

    public function enviarResultadoPorCorreo(Request $request, $recordId)
    {
        try {
            // Verificar si la evaluación existe
            $evaluation = RecordEvaluation::findOrFail($recordId);
    
            // Obtener los usuarios asociados con el hotel
            $hotelId = $evaluation->hotel_id;
            $usuariosHotel = User::where('hotel_id', $hotelId)->get();

            $usuariosAdmin = User::role('admin')->get();

            $usuarios = $usuariosHotel->merge($usuariosAdmin)->unique('email');
    
            // Obtener el PDF desde la solicitud
            $pdf = $request->file('pdf');
            $pdfData = file_get_contents($pdf->getRealPath());
    
            // Enviar el correo electrónico a cada usuario
            foreach ($usuarios as $usuario) {
                Mail::to($usuario->email)->send(new ResultadoEvaluacionMail($pdfData));
            }
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error al enviar el PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error al enviar el PDF.'], 500);
        }
    }
    
    
}
    

