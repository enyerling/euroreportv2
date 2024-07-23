<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;

class HotelController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE HOTEL',
        ));
        $audience->save();

        //return view('hotels.index');
        
    }

    // ALL HOTELS
    public function hotels()
    {
        $hotels = Hotel::orderBy('id', 'ASC')->paginate(10);
        return [
            'pagination' => [
                'total'        => $hotels->total(),
                'current_page' => $hotels->currentPage(),
                'per_page'     => $hotels->perPage(),
                'last_page'    => $hotels->lastPage(),
                'from'         => $hotels->firstItem(),
                'to'           => $hotels->lastItem(),
            ],
            'hotels'     => $hotels,
        ];

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required|min:5|unique:hotels',
            'manager' => 'required|min:5',
            'image'   => 'required|image64:jpeg,jpg,png',
        ]);

        $hotel = new Hotel;

        $hotel->name = $request->name;
        $hotel->manager = $request->manager;
        $hotel->image = $request->image;

        $hotel->save();

        return;
    }

    public function showAll()
    {
        $hotels = Hotel::all();// Obtener todos los hoteles
        return view('admin.dashboard', compact('hotels'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $hotel->name    = $request->name;
        $hotel->manager = $request->manager;
        if ($request->image != null){
            $hotel->image   = $request->get('image');
        }
        $hotel->save();

        return;
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return;
    }
}
