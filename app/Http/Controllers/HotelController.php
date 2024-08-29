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
    /*Esta funcion muestra todos los hoteless*/
    public function hotels()
    {
        $user     = Auth::user();
        // Registrar la acción del usuario en la tabla de auditoría
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE HOTEL',
        ));
        $audience->save();

         // Obtener y paginar los hoteles
        $hotels = Hotel::orderBy('id', 'ASC')->paginate(10);
        return view('admin.hoteles', compact('hotels'));

    }

    /*Esta funcion maneja la creación y almacenamiento de un nuevo registro de hotel en la base de datos,
    incluyendo la validación de los datos del formulario y el manejo de una imagen cargada.*/

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'hotelName' => 'required|string|max:255',
            'managerName' => 'required|string|max:255',
            'hotelImage' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:width=1156,height=768',
        ], [
            // Mensaje de error personalizado para las dimensiones de la imagen
            'hotelImage.dimensions' => 'La imagen debe tener exactamente 1156 píxeles de ancho y 768 píxeles de alto.'
        ]);
        
        // Generar un nombre único para la imagen usando la marca de tiempo actual
        $imageName = time() . '.' . $request->hotelImage->extension();
        
        // Mover la imagen al directorio público
        $request->hotelImage->move(public_path('vendor/adminlte/dist/img'), $imageName);
        
        // Crear una nueva instancia del modelo Hotel
        $hotel = new Hotel();
        $hotel->name = $request->hotelName;
        $hotel->manager = $request->managerName;
        $hotel->image = 'vendor/adminlte/dist/img/' . $imageName; // Guardar la ruta de la imagen
        $hotel->save();

        return redirect()->route('admin.hoteles')->with('success', 'Hotel agregado exitosamente.');
    }

    /*Esta funcion muestra todos los hoteles incluyendo la fecha de la ultima evaluacion realizada*/
    public function showAll()
    {   
        // Obtener el usuario autenticado
        $user = Auth::user(); 
        $hotelEvaluations = [];

        // Verificar el rol del usuario para determinar qué hoteles mostrar
        if ($user->hasRole('admin') || $user->hasRole('subadmin')) {
            $hotels = Hotel::all(); 
        } elseif ($user->hasRole('user')) {
            $hotels = Hotel::where('id', $user->hotel_id)->get(); // Usuario normal solo puede ver el hotel asociado
        }

        // Obtener la última evaluación para cada hotel
        foreach ($hotels as $hotel) {
            $latestRecord = RecordEvaluation::where('hotel_id', $hotel->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Guardar la fecha de la última evaluación en el array o marcar como 'Sin evaluaciones'
            $hotelEvaluations[$hotel->id] = $latestRecord ? $latestRecord->created_at->format('d-m-Y') : 'Sin evaluaciones';
        }

        return view('admin.dashboard', compact('hotels', 'hotelEvaluations'));
    }

    /*Esta funcion actualiza la información de un hotel en la base de datos, incluyendo 
    la posibilidad de cambiar la imagen del hotel.*/

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

    /*Esta funcion elimina el hotel*/
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        
        // Eliminar el hotel de la base de datos
        $hotel->delete();

       return redirect()->route('admin.hoteles');
    }
}
