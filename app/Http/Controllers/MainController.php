<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Dotenv\Parser\Value;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\Operations;

class MainController extends Controller
{
    public function index()
    {
        // load user notes
        $id = session('user.id');
        $notes = User::find($id)
            ->notes()
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        // show home view
        return view('home', [
            'notes' => $notes
        ]);
    }

    public function newNote()
    {
        return view('new_Note');
    }
    public function newNoteSubmit(Request $request)
    {
        // validate request
        $request->validate(
            [
                'text_title' => 'required|min:3|max:200',
                'text_note'  => 'required|min:3|max:3000'
            ],
            // custom error messages
            [
                'text_title.required' => 'O Note Title é Obrigatório',

                'text_note.required' => ' O Note Text é Obrigatório',

                'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
                'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',

                'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
                'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
            ]

        );
        try {
            // get user id
            $id = session('user.id');
            // create note
            $note = new Note();
            $note->user_id = $id;
            $note->title = $request->text_title;
            $note->text = $request->text_note;
            $note->save();

            // Redirecionar com mensagem de sucesso
            return redirect()->route('home')->with('success', 'Nota criada com sucesso!');
        } catch (\Exception $e) {
            // Redirecionar com mensagem de erro caso algo dê errado
            return redirect()->route('home')->with('error', 'Houve um erro ao criar a nota. Tente novamente.');
        }
    }

    public function editNote($id)
    {

        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);

            return view('edit_Note', ['note' => $note]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->view('errors.404', [], 404);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao carregar nota: ' . $e->getMessage());
        }
    }
    public function editNoteSubmit(Request $request)
    {
        // validate request
        $request->validate(
            [
                'text_title' => 'required|min:3|max:200',
                'text_note'  => 'required|min:3|max:3000'
            ],
            // custom error messages
            [
                'text_title.required' => 'O Note Title é Obrigatório',

                'text_note.required' => ' O Note Text é Obrigatório',

                'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
                'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',

                'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
                'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
            ]

        );

        //  check id note_id exists
        if ($request->note_id == null) {
            return redirect()->route('home');
        }
        try {
        // decrypt id
        $id = Operations::decryptId($request->note_id);
        // load note

        $note = Note::find($id);
        // update note
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();
            // Redirecionar com mensagem de sucesso
            return redirect()->route('home')->with('success', 'Nota atualizada com sucesso!');
        } catch (\Exception $e) {
            // Redirecionar com mensagem de erro caso algo dê errado
            return redirect()->route('home')->with('error', 'Houve um erro ao atualizar a nota. Tente novamente.');
        }
    }
    public function deleteNote($id)
    {
        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);

            return view('delete_note', compact('note'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->view('errors.404', [], 404);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao carregar nota: ' . $e->getMessage());
        }
    }
    public function deleteNoteConfirm($id)
    {

        // check id note_id exists
        $id = Operations::decryptId($id);

        // load note
        $note = Note::findOrFail($id);
        if (!$note) {
            return response()->view('404', [], 404);
        }
        //  1. hard delete
        // $note->delete();

        // 2. soft delete de maneira simples
        // $note->deleted_at = date('Y-m-d H:i:s');
        // $note->save();

        // 3. soft delete properly model (recommended)
        $note->delete();

        // 4. hard delete properly model (force delete)
        //  $note->forceDelete();

            // Redirecionar com mensagem de sucesso
            return redirect()->route('home')->with('success', 'Nota excluida com sucesso!');

    }
}
