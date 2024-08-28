<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\System;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function questions(){
        $user = Auth::user();
        $audience = new Audience(array(
            'name' => $user->name,
            'email' => $user->email,
            'action' => 'INGRESO AL MODULO DE PREGUNTAS',
        ));
        $audience->save();

        $questions = Question::orderBy('id', 'ASC')->paginate(10);
        $var = 0;
        $data = [];
        foreach ($questions as $question) {
            $data[$var] = array(
                    'id' => $question->id,
                    'name' => $question->name,
                    'type' => $question->type,
                    'answer' => $question->answer,
                    'system' => (isset($question->system->name)) ? $question->system->name : 'No Asignado',
                    'system_id' => (isset($question->system->id)) ? $question->system->id : 'No Asignado',
                    'accessorie' => (isset($question->accessorie->name)) ? $question->accessorie->name : 'No Asignado',
                    'accessorie_id' => (isset($question->accessorie->id)) ? $question->accessorie->id : '',
                );

            $var++;
        }

        return [
            'pagination' => [
                'total'        => $questions->total(),
                'current_page' => $questions->currentPage(),
                'per_page'     => $questions->perPage(),
                'last_page'    => $questions->lastPage(),
                'from'         => $questions->firstItem(),
                'to'           => $questions->lastItem(),
            ],
            'questions'    => collect($data),
        ];
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|min:3',
            'type' => 'required|min:3',
            'system_id' => 'required|numeric',
        ]);

        Question::create($request->all());

        return;
    }

    public function update(Request $request, Question $question)
    {
        //
        $this->validate($request, [
            'name' => 'required|min:3',
            'type' => 'required|min:3',
            'system_id' => 'required|numeric',
        ]);

        $question->name = $request->name;
        $question->type = $request->type;
        (isset($request->answer)) ? $question->answer = $request->answer : $question->answer = null;
        $question->system_id = $request->system_id;
        (isset($request->accessorie_id)) ? $question->accessorie_id = $request->accessorie_id : $question->accessorie_id = null;

        $question->save();
        return;

    }

    public function destroy(Question $question)
    {
        $question->delete();
        return;
    }
}
