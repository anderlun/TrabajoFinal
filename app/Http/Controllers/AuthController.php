<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:estudiante,profesor,administrador', // Asegúrate de que solo acepte roles válidos
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Inicia sesión automáticamente al usuario después del registro
            Auth::login($user);

            // Redirige según el rol del usuario registrado
            switch ($user->role) {
                case 'estudiante':
                    return redirect('/dashboard/estudiante');
                case 'profesor':
                    return redirect('/dashboard/profesor');
                case 'administrador':
                    return redirect('/dashboard/administrador');
                // Agrega más casos según tus roles
            }
        } catch (\Exception $e) {
            // Manejar errores de creación de usuario
            return redirect()->back()->withErrors(['error' => 'Error en el registro. Inténtalo de nuevo.']);
        }
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Si el usuario ya está autenticado, redirigir según su rol
            switch (auth()->user()->role) {
                case 'estudiante':
                    return redirect('/dashboard/estudiante');
                case 'profesor':
                    return redirect('/dashboard/profesor');
                case 'administrador':
                    return redirect('/dashboard/administrador');
                // Agrega más casos según tus roles
            }
        }

        // Si no está autenticado, mostrar el formulario de inicio de sesión
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // ... lógica de inicio de sesión actual ...
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/')->with('success', 'Has cerrado sesión exitosamente.');
    }

    // ... otros métodos ...
}
