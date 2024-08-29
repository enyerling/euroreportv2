<?php

namespace App\Http\Controllers;
use App\Models\Observations;
use App\Models\Images;
use App\Models\User;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class ObservationController extends Controller
{
    /*Esta funcion muestra una vista de observaciones para una evaluación específica*/
    public function observaciones($record_evaluation_id)
    {
        // Obtener el usuario autenticado
        $user     = Auth::user();
        // Registrar la acción del usuario en la tabla de Audience
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE OBSERVACIONES',
        ));
        $audience->save();

        return view('admin.observations', ['record_evaluation_id' => $record_evaluation_id]);
    }

    /*Esta funcion guarda las observaciones y las imágenes asociadas a una evaluación específica*/
    public function saveObservations(Request $request)
    {
        // Obtener el ID de la evaluación y las observaciones del formulario
        $recordEvaluationId = $request->input('record_evaluation_id');
        $observaciones = $request->input('observaciones');

        $imagePaths = [];
        
        // Verificar si se han subido imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $image) {
                // Guardar imagen en el directorio público
                $path = $image->store('images', 'public');
                
                // Guardar en la base de datos
                $imageRecord = Images::create([
                    'record_evaluation_id' => $recordEvaluationId,
                    'path' => $path
                ]);
    
                $imagePaths[] = $imageRecord;
            }
        }
    
        $observation = Observations::updateOrCreate(
            ['record_evaluation_id' => $recordEvaluationId],
            ['answer' => $observaciones]
        );

        return redirect()->route('admin.detalles_evaluacion', ['evaluationId' => $recordEvaluationId]);
    }
}
