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


class HotelConfigController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE CONFIGURACION',
        ));
        $audience->save();

        //return view('hoteles');
        
    }

    public function showConfigForm($hotelId)
    {
    $hotel = Hotel::find($hotelId);
    $sistemas = System::all(); 
    return view('admin.eval_config', ['hotel' => $hotel, 'sistemas' => $sistemas]);
    }

    public function guardarConfiguracion(Request $request) {
        $hotelId = $request->input('hotel_id');
        $sistemas = $request->input('sistemas');
        
        if (!is_null($sistemas)) {
            foreach ($sistemas as $sistemaId => $data) {
                $cantidad = $data['cantidad'];
        
                // Guardar en la base de datos
                HotelSystem::create([
                    'hotel_id' => $hotelId,
                    'system_id' => $sistemaId,
                    'cant' => $cantidad,
                ]);
            }
        
            return redirect()->route('admin.question_config', ['hotelId' => $hotelId]);
        } else {
            return redirect()->route('admin.hoteles')->with('error', 'No se han recibido datos de sistemas para configurar');
        }
    }

    public function showQuestionsForSystems($hotelId) {
        $hotelSystems = HotelSystem::where('hotel_id', $hotelId)->with('system.questions')->where('cant', '>', 0)->get();
    
        // Recuperar todos los IDs de sistemas guardados para el hotel
        $systemIds = $hotelSystems->pluck('system_id')->toArray();
    
        // Recuperar las preguntas asociadas a los sistemas guardados
        $questions = Question::whereIn('system_id', $systemIds)->get();
    
        return view('admin.question_config', ['hotelId' => $hotelId,'hotelSystems' => $hotelSystems, 'questions' => $questions]);
    }

    public function guardarPreguntas(Request $request)
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
        return redirect()->route('admin.dashboard')->with('status', 'success');
    }
}
