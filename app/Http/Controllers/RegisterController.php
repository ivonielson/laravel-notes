<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Operations;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function user_list()
    {
        $currentUser = session('user');

        // Se for admin, busca todos os usuários (exceto deletados)
        if ($currentUser['role'] === 'admin') {
            $users = User::whereNull('deleted_at')
                ->orderBy('username')
                ->get();
        }
        // Se for usuário normal, busca apenas ele mesmo
        else {
            $users = User::where('id', $currentUser['id'])
                ->whereNull('deleted_at')
                ->get();
        }

        return view('users.user_list', [
            'users' => $users->toArray(),
            'isAdmin' => ($currentUser['role'] === 'admin')
        ]);
    }


    public function registerForm()
    {

        return view('users.register');
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'text_username' => 'required|email|min:3|max:60|unique:users,username',
            'text_password' => 'required|min:6|max:16',
            'text_password_confirmation' => 'required|same:text_password'
        ], [
            'text_username.required' => 'O email é obrigatório',
            'text_username.email' => 'Deve ser um email válido',
            'text_username.min' => 'O email deve ter no mínimo :min caracteres',
            'text_username.max' => 'O email deve ter no máximo :max caracteres',
            'text_username.unique' => 'Este email já está em uso',
            'text_password.required' => 'A senha é obrigatória',
            'text_password.min' => 'A senha deve ter no mínimo :min caracteres',
            'text_password.max' => 'A senha deve ter no máximo :max caracteres',
            'text_password_confirmation.required' => 'A confirmação de senha é obrigatória',
            'text_password_confirmation.same' => 'As senhas não coincidem'
        ]);

        try {
            $user = new User();
            $user->username = $request->text_username;
            $user->password = Hash::make($request->text_password);
            $user->role = 'usuario'; // Valor padrão conforme a tabela
            $user->save();


            return redirect()
                ->route('login')
                ->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', 'Houve um erro ao registrar. Tente novamente.');
        }
    }

    public function editUser($id)
    {
        try {
            $id = Operations::decryptId($id);
            $user = User::findOrFail($id);
            $currentUser = session('user');

            // Verificar permissões
            if ($currentUser['role'] !== 'admin' && $currentUser['id'] !== $user->id) {
                return redirect()
                    ->route('home')
                    ->with('error', 'Você não tem permissão para editar este usuário.');
            }

            // AuditLogger::logView($user, 'edit_form');

            return view('users.edit_user', [
                'user' => $user,
                'isAdmin' => ($currentUser['role'] === 'admin')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()
                ->view('errors.404', [], 404);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao carregar usuário: ' . $e->getMessage());
        }
    }

    public function editUserSubmit(Request $request)
    {
        $request->validate([

            'text_password' => 'nullable|min:6|max:16',
            'text_password_confirmation' => 'nullable|same:text_password'
        ], [

            'text_password.min' => 'A senha deve ter no mínimo :min caracteres',
            'text_password.max' => 'A senha deve ter no máximo :max caracteres',
            'text_password_confirmation.same' => 'As senhas não coincidem'
        ]);

        if ($request->user_id == null) {

            return redirect()->route('home');
        }

        try {
            $id = Operations::decryptId($request->user_id);
            $user = User::find($id);
            $currentUser = session('user');

            // Verificar permissões
            if ($currentUser['role'] !== 'admin' && $currentUser['id'] !== $user->id) {

                return redirect()
                    ->route('home')
                    ->with('error', 'Você não tem permissão para atualizar este usuário.');
            }

            $oldValues = $user->getAttributes();

            // Atualizar usuário


            if ($request->text_password) {
                $user->password = Hash::make($request->text_password);
            }

            // Apenas admin pode alterar o role
            if ($currentUser['role'] === 'admin' && $request->has('text_role')) {
                $user->role = $request->text_role;
            }

            $user->save();

            // Se o usuário editado for o próprio usuário logado, atualize a sessão
            if ($currentUser['id'] === $user->id) {
                session([
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                    ]
                ]);
            }


            return redirect()
                ->route('user_list')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {

            return redirect()
                ->route('user_list')
                ->with('error', 'Houve um erro ao atualizar o usuário. Tente novamente.');
        }
    }


}
