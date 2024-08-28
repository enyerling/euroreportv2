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
   
    public function showAll()
    {
        $user     = Auth::user();
        $audience = new Audience(array(
            'name'   => $user->name,
            'email'  => $user->email,
            'action' => 'INGRESO AL MODULO DE USERS',
        ));
        $audience->save();
        
        $users = User::orderBy('id','ASC')->paginate(10);
        $roles = Role::all();
        $hotels = Hotel::all();
        return view('admin.users', compact('users','roles','hotels'));
    }

    public function store(Request $request)
    {
     
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'hotel_id' => $request->hotel_id,

        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users');

    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->hotel_id = $request->input('hotel_id');
        $user->syncRoles($request->input('role'));
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente.');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateprofile(Request $request)
    {
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        
        if ($request->filled('current_password')) {
            if (Hash::check($request->input('current_password'), $user->password)) {
                // Si se ingresa una nueva contraseña, actualizarla
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
            } else {
                return redirect()->route('profile')
                    ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }
        }
        $user->save();

        return redirect()->route('profile')->with('status', 'Perfil actualizado con éxito.');
    }
    
    
}
