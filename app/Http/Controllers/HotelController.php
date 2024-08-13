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
        $request->validate([
            'hotelName' => 'required|string|max:255',
            'managerName' => 'required|string|max:255',
            'hotelImage' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:width=1156,height=768',
        ], [
            'hotelImage.dimensions' => 'La imagen debe tener exactamente 1156 píxeles de ancho y 768 píxeles de alto.'
        ]);
    
        $imageName = time() . '.' . $request->hotelImage->extension();
    
        $request->hotelImage->move(public_path('vendor/adminlte/dist/img'), $imageName);
    
        $hotel = new Hotel();
        $hotel->name = $request->hotelName;
        $hotel->manager = $request->managerName;
        $hotel->image = 'vendor/adminlte/dist/img/' . $imageName; // Guardar la ruta de la imagen
        $hotel->save();

        return redirect()->route('admin.hoteles')->with('success', 'Hotel agregado exitosamente.');
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

    public function update(Request $request, $id)
    {
        // Validar los campos del formulario
        $request->validate([
            'hotelName' => 'required|string|max:255',
            'managerName' => 'required|string|max:255',
            'hotelImage' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:width=1156,height=768',
        ], [
            'hotelImage.dimensions' => 'La imagen debe tener exactamente 1156 píxeles de ancho y 768 píxeles de alto.'
        ]);

        // Obtener el hotel a actualizar
        $hotel = Hotel::findOrFail($id);
        $hotel->name = $request->hotelName;
        $hotel->manager = $request->managerName;

        // Si se sube una nueva imagen
        if ($request->hasFile('hotelImage')) {
            // Eliminar la imagen anterior si existe
            if ($hotel->image && file_exists(public_path($hotel->image))) {
                unlink(public_path($hotel->image));
            }

            // Guardar la nueva imagen
            $imageName = time() . '.' . $request->hotelImage->extension();
            $request->hotelImage->move(public_path('vendor/adminlte/dist/img'), $imageName);
            $hotel->image = 'vendor/adminlte/dist/img/' . $imageName;
        }

        $hotel->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('admin.hoteles')->with('success', 'Hotel actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

       return redirect()->route('admin.hoteles');
    }
}
