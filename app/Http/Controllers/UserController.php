<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
   /*Esta funcion registra la acción del usuario y recupera una lista paginada de usuarios, 
   todos los roles y todos los hoteles para mostrarlos en la vista*/

    public function showAll()
    {
        // Obtener el usuario autenticado
        $user     = Auth::user();

        // Registrar la acción del usuario en la tabla de auditoria
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE USERS',
        ));
        $audience->save();
        
        $users = User::orderBy('id','ASC')->paginate(10);

        // Obtener todos los roles
        $roles = Role::all();
        // Obtener todos los hoteles
        $hotels = Hotel::all();
        return view('admin.users', compact('users','roles','hotels'));
    }

    /*Esta funcion crea un nuevo usuario con los datos proporcionados, asigna un rol al usuario 
    y redirige a la página de usuario*/

    public function store(Request $request)
    {
        //Crea modelo de usuario 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'hotel_id' => $request->hotel_id,

        ]);

        //Asigna rol
        $user->assignRole($request->role);

        return redirect()->route('admin.users');

    }

    /*Esta funcion actualiza la información de un usuario específico, sincroniza sus roles y 
    redirige a la página de usuarios con un mensaje de éxito. */

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Actualizar los datos del usuario con la información del formulario
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->hotel_id = $request->input('hotel_id');

        // Sincronizar los roles del usuario con los roles proporcionados en el formulario
        $user->syncRoles($request->input('role'));

        // Guardar los cambios en la base de datos
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado correctamente.');
    }

    /*Esta funcion elimina el usuario*/
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Eliminar el usuario de la base de datos
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente.');
    }

    /*Esta funcion muestra la vista del perfil del usuario en el panel de administración*/
    public function profile()
    {
        //Retorna vista de perfil de usuario
        return view('admin.profile');
    }

    /*Esta funcion actualiza la información del perfil del usuario, incluyendo nombre, 
    correo electrónico y, opcionalmente, la contraseña si se proporciona la contraseña actual correcta.*/

    public function updateprofile(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        
        // Verificar si se ingresó una contraseña actual
        if ($request->filled('current_password')) {
            // Verificar si la contraseña actual es correcta
            if (Hash::check($request->input('current_password'), $user->password)) {
                // Si se ingresa una nueva contraseña, actualizarla
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
            } else {
                // Redirigir con un error si la contraseña actual es incorrecta
                return redirect()->route('profile')
                    ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }
        }
        $user->save();

        return redirect()->route('profile')->with('status', 'Perfil actualizado con éxito.');
    }
    
    
}
