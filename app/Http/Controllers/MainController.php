<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        // Log de acesso à página inicial
        // AuditLogger::log('view', 'Note', null, null, [
        //     'description' => 'Acesso à lista de notas'
        // ]);

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
        // AuditLogger::log('view_form', 'Note', null, null, [
        //     'form_type' => 'create'
        // ]);

        return view('new_Note');
    }

    public function newNoteSubmit(Request $request)
    {
        $request->validate([
            'text_title' => 'required|min:3|max:200',
            'text_note'  => 'required|min:3|max:3000'
        ], [
            'text_title.required' => 'O Note Title é Obrigatório',
            'text_note.required' => ' O Note Text é Obrigatório',
            'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
            'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',
            'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
            'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
        ]);

        try {
            $note = new Note();
            $note->user_id = session('user.id');
            $note->title = $request->text_title;
            $note->text = $request->text_note;
            $note->save();

            // Log de criação
            // AuditLogger::logModelAction('create', $note);

            return redirect()
                ->route('home')
                ->with('success', 'Nota criada com sucesso!');
        } catch (\Exception $e) {
            // AuditLogger::log('error', 'Note', null, null, [
            //     'action' => 'create',
            //     'error' => $e->getMessage()
            // ]);

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

            // Log de visualização para edição
            // AuditLogger::logView($note, 'edit_form');

            return view('edit_Note', ['note' => $note]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // AuditLogger::log('error', 'Note', $id, null, [
            //     'action' => 'view_edit',
            //     'error' => 'Nota não encontrada'
            // ]);

            return response()
                ->view('errors.404', [], 404);
        } catch (\Exception $e) {
            // AuditLogger::log('error', 'Note', $id, null, [
            //     'action' => 'view_edit',
            //     'error' => $e->getMessage()
            // ]);

            return back()
                ->with('error', 'Erro ao carregar nota: ' . $e->getMessage());
        }
    }

    public function editNoteSubmit(Request $request)
    {
        $request->validate([
            'text_title' => 'required|min:3|max:200',
            'text_note'  => 'required|min:3|max:3000'
        ], [
            'text_title.required' => 'O Note Title é Obrigatório',
            'text_note.required' => ' O Note Text é Obrigatório',
            'text_title.min' => 'O Note Title deve ter no mínimo :min caracteres',
            'text_title.max' => 'O Note Title deve ter no máximo :max caracteres',
            'text_note.min' => 'O Note Title deve ter no mínimo :min caracteres',
            'text_note.max' => 'O Note Title deve ter no máximo :max caracteres'
        ]);

        if ($request->note_id == null) {
            // AuditLogger::log('error', 'Note', null, null, [
            //     'error' => 'ID da nota não fornecido'
            // ]);

            return redirect()->route('home');
        }

        try {
            $id = Operations::decryptId($request->note_id);
            $note = Note::find($id);

            // Guardar valores antigos para o log
            $oldValues = $note->getAttributes();

            // Atualizar nota
            $note->title = $request->text_title;
            $note->text = $request->text_note;
            $note->save();

            // Log de atualização
            // AuditLogger::logModelAction('update', $note, $oldValues);

            return redirect()
                ->route('home')
                ->with('success', 'Nota atualizada com sucesso!');
        } catch (\Exception $e) {
            // AuditLogger::log('error', 'Note', $id ?? null, null, [
            //     'action' => 'update',
            //     'error' => $e->getMessage()
            // ]);

            return redirect()
                ->route('home')
                ->with('error', 'Houve um erro ao atualizar a nota. Tente novamente.');
        }
    }

    public function deleteNote($id)
    {
        try {
            $id = Operations::decryptId($id);
            $note = Note::findOrFail($id);

            // Log de visualização para exclusão
            // AuditLogger::logView($note, 'delete_confirmation');

            return view('delete_note', compact('note'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // AuditLogger::log('error', 'Note', $id, null, [
            //     'action' => 'view_delete',
            //     'error' => 'Nota não encontrada'
            // ]);

            return response()
                ->view('errors.404', [], 404);
        } catch (\Exception $e) {
            // AuditLogger::log('error', 'Note', $id, null, [
            //     'action' => 'view_delete',
            //     'error' => $e->getMessage()
            // ]);

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

            // Log de exclusão
            // AuditLogger::logModelAction('delete', $note, $noteData);

            return redirect()
                ->route('home')
                ->with('success', 'Nota excluída com sucesso!');
        } catch (\Exception $e) {
            // AuditLogger::log('error', 'Note', $id, null, [
            //     'action' => 'delete',
            //     'error' => $e->getMessage()
            // ]);

            return redirect()
                ->route('home')
                ->with('error', 'Houve um erro ao excluir a nota. Tente novamente.');
        }
    }
}
