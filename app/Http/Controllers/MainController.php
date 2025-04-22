<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use App\Helpers\AuditLogger;
use App\Http\Requests\notes\NoteRequest;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class MainController extends Controller
{
    public function index()
    {

        // load user notes
        $id = session('user.id');
        $notes = User::find($id)
            ->notes()
            ->whereNull('deleted_at')
            ->get();

        // Log da visualização da lista de notas
        AuditLogger::logCollectionView(
            Note::class,
            $notes->count(),
            request()->all() // Opcional: registrar filtros se houver
        );
        return view('home', [
            'notes' => $notes->toArray()
        ]);
    }

    public function newNote()
    {

        return view('note.new_Note');
    }

    public function newNoteSubmit(NoteRequest $request): RedirectResponse
    {
        // $request->validate([
        //     'text_title' => 'required|min:3|max:200',
        //     'text_note'  => 'required|min:3|max:3000'
        // ], [
        //     'text_title.required' => 'O Note Title é Obrigatório',
        //     'text_note.required' => ' O Note Text é Obrigatório',
        //     'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
        //     'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',
        //     'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
        //     'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
        // ]);

        try {
            $note = new Note();
            $note->user_id = session('user.id');
            $note->title = $request->text_title;
            $note->text = $request->text_note;
            $note->save();

            return redirect()
                ->route('home')
                ->with('success', 'Nota criada com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('home')
                ->with('error', 'Houve um erro ao criar a nota. Tente novamente.');
        }
    }

    public function editNote($id)
    {
        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);

            if ($note->user_id !== session('user.id')) {
                abort(403, 'Você não tem permissão para acessar esta nota.');
            }
            return view('note.edit_Note', ['note' => $note]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()
                ->view('errors.404', [], 404);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao carregar nota: ' . $e->getMessage());
        }
    }

    public function editNoteSubmit(NoteRequest $request): RedirectResponse
    {
        // $request->validate([
        //     'text_title' => 'required|min:3|max:200',
        //     'text_note'  => 'required|min:3|max:3000'
        // ], [
        //     'text_title.required' => 'O Note Title é Obrigatório',
        //     'text_note.required' => ' O Note Text é Obrigatório',
        //     'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
        //     'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',
        //     'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
        //     'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
        // ]);

        if ($request->note_id == null) {
            return redirect()->route('home');
        }

        try {
            $id = Operations::decryptId($request->note_id);
            $note = Note::findOrFail($id);

            //  Verifica se o usuário logado é o dono da nota
            if ($note->user_id !== session('user.id')) {
                abort(403, 'Você não tem permissão para editar esta nota.');
            }

            $oldValues = $note->getAttributes();
            $note->title = $request->text_title;
            $note->text = $request->text_note;
            $note->save();

            return redirect()->route('home')->with('success', 'Nota atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Houve um erro ao atualizar a nota. Tente novamente.');
        }

    }

    public function deleteNote($id)
    {
        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);
            return view('note.delete_note', compact('note'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()
                ->view('errors.404', [], 404);
        } catch (\Exception $e) {

            return back()
                ->with('error', 'Erro ao carregar nota: ' . $e->getMessage());
        }
    }

    public function deleteNoteConfirm($id)
    {
        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);

            // Guardar dados antes da exclusão
            $noteData = $note->getAttributes();

            // Soft delete
            $note->delete();
            return redirect()
                ->route('home')
                ->with('success', 'Nota excluída com sucesso!');
        } catch (\Exception $e) {

            return redirect()
                ->route('home')
                ->with('error', 'Houve um erro ao excluir a nota. Tente novamente.');
        }
    }
}
