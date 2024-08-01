<?php

namespace App\Http\Controllers;

use App\Models\Accessorie;
use Illuminate\Http\Request;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;

class AccessorieController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $audience = new Audience(array(
            'name' => $user->name,
            'email' => $user->email,
            'action' => 'INGRESO AL MODULO DE ACCESORIOS',
        ));
        $audience->save();

        return view('accessories.index');
    }

    public function accessories(){
        $accessories = Accessorie::orderBy('id', 'ASC')->paginate(10);
        return [
            'pagination' => [
                'total'        => $accessories->total(),
                'current_page' => $accessories->currentPage(),
                'per_page'     => $accessories->perPage(),
                'last_page'    => $accessories->lastPage(),
                'from'         => $accessories->firstItem(),
                'to'           => $accessories->lastItem(),
            ],
            'accessories'      => $accessories,
        ];
    }

    public function all_accessories(){
        $accessories = Accessorie::orderBy('id', 'ASC')->get();
        return [
            'accessories'      => $accessories,
        ];
    }

    public function store(Request $request)
    {
         //
        $this->validate($request, [
            'name' => 'required|min:2|unique:accessories',
        ]);

        Accessorie::create($request->all());

        return;
    }

    public function update(Request $request, Accessorie $accessorie)
    {
       //
        $data    = Accessorie::whereId($request->accessorie_id)->firstOrFail();
        $this->validate($request, [
            'name' => 'required|unique:accessories,name, '. $data->id,
        ]);


        $data->name = $request->name;

        $data->save();

        return;
    }

    public function destroy(Request $request)
    {
        $data    = Accessorie::whereId($request->accessorie_id)->firstOrFail();
        $data->delete();
        return;
    }
}
