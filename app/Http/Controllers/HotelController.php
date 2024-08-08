<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use App\Models\Hotel;
use App\Models\RecordEvaluation;
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
        return view('admin.hoteles', compact('hotels'));

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
        $hotels = Hotel::all(); 
        $hotelEvaluations = [];

        foreach ($hotels as $hotel) {
            $latestRecord = RecordEvaluation::where('hotel_id', $hotel->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $hotelEvaluations[$hotel->id] = $latestRecord ? $latestRecord->created_at->format('d-m-Y') : 'Sin evaluaciones';
        }

        return view('admin.dashboard', compact('hotels', 'hotelEvaluations'));
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
