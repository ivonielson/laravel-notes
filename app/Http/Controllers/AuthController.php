<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use App\Services\Operations;

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
        ], [
            'text_username.required' => 'O username é Obrigatório',
            'text_username.email' => 'Username deve ser um email válido',
            'text_password.required' => 'O Password é Obrigatório',
            'text_password.min' => 'O Password deve ter no mínimo :min caracteres',
            'text_password.max' => 'O Password deve ter no máximo :max caracteres'
        ]);

        $username = $request->input('text_username');
        $password = $request->input('text_password');

        // check if user exists
        $user = User::where('username', $username)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            AuditLogger::log('login_fail', get_class($user), null, null, [
                'username' => $username,
                'status' => 'failed',
                'reason' => 'user_not_found',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('loginError', 'Username ou Password inválido');
        }

        if (!password_verify($password, $user->password)) {
            AuditLogger::log('login_fail', get_class($user), $user->id, null, [
                'status' => 'failed',
                'reason' => 'invalid_password',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('loginError', 'Username ou Password inválido');
        }

        // update last login
        $user->last_login = now();
        $user->save();

        // login user
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ]
        ]);

        // Log de login bem-sucedido
        AuditLogger::log('login', get_class($user), $user->id, null, [
            'status' => 'success',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Bem Vindo(a) ' . $user->username);
    }

    public function logout()
    {
        $userId = session('user.id') ?? null;
        $user = $userId ? User::find($userId) : null;

        AuditLogger::log('logout', $user ? get_class($user) : 'User', $userId, null, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        session()->forget('user');

        return redirect()->route('login');
    }
}
