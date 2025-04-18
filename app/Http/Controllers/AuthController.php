<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
       return view('login');
    }

    public function loginSubmit(Request $request)
        {
            // form validation
                $request->validate([
                    'text_username' => 'required|email',
                    'text_password' => 'required|min:6|max:16'
                ],
                // custom error messages
                [
                    'text_username.required' => 'O username é Obrigatório',
                    'text_username.email' => 'Username deve ser um email valido',
                    'text_password.required' => 'O Password é Obrigatório',
                    'text_password.min' => 'O Password deve ter no mínimo :min caracteres',
                    'text_password.max' => 'O Password deve ter no máximo :max caracteres'
                ]

            );

            // get user imput
            $username = $request->input('text_username');
            $password = $request->input('text_password');

            // check if user exists
            $user = User::where('username', $username)
                    ->where('deleted_at', null)
                    ->first();
            if (!$user) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Username ou Password inválido');
            }
            // check if user exists
                if (!password_verify($password, $user->password)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Username ou Password inválido');
                }
            // update last login
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            // login user
            session([
                'user' => [
                'id' => $user->id,
                'username' => $user->username,
                ]

            ]);
            // redirect to home
            $menssage = 'Bem Vindo(a) ' . $user->username;
            return redirect()->route('home')->with('success', $menssage);


    }
    public function logout()
    {
        // logout from apllication
        session()->forget('user');
        return redirect()
            ->route('login');
    }
}
