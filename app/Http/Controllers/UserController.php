<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
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

    public function showAll()
    {
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
    
    public function sendEmail()
    {
        Mail::raw('Este es un correo de prueba desde Laravel.', function ($message) {
            $message->to('euroreportapp@gmail.com')
                    ->subject('Correo de Prueba');
        });
    
        return 'Correo enviado!';
    }
    
}
