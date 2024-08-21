<?php

namespace App\Http\Controllers;
use App\Models\Observations;
use App\Models\Images;

use Illuminate\Http\Request;

class ObservationController extends Controller
{
    public function observaciones($record_evaluation_id)
    {
        return view('admin.observations', ['record_evaluation_id' => $record_evaluation_id]);
    }

    public function guardar_observations(Request $request)
    {
        $recordEvaluationId = $request->input('record_evaluation_id');
        $observaciones = $request->input('observaciones');

        $imagePaths = [];

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
