<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $audience = new Audience(array(
            'name' => $user->name,
            'email' => $user->email,
            'action' => 'INGRESO AL MODULO DE SISTEMAS',
        ));
        $audience->save();

        return view('system.index');
    }

    public function systems(){
        
        $systems = System::orderBy('id', 'ASC')->paginate(10);
        return [
            'pagination' => [
                'total'        => $systems->total(),
                'current_page' => $systems->currentPage(),
                'per_page'     => $systems->perPage(),
                'last_page'    => $systems->lastPage(),
                'from'         => $systems->firstItem(),
                'to'           => $systems->lastItem(),
            ],
            'systems'    => $systems,
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|unique:systems',
            'score' => 'required|numeric',
        ]);

        System::create($request->all());

        return;
    }

    public function update(Request $request, System $system)
    {
        $this->validate($request, [
            'name' => 'required|min:3|unique:systems,name, '. $system->id,
            'score' => 'required|numeric',
        ]);

        $system->name = $request->name;
        $system->score = $request->score;

        $system->save();

        return;
    }

    public function destroy(System $system)
    {
        //
        $system->delete();
        return;
    }
}
