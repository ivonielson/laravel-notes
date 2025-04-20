<?php

namespace App\Observers;

use App\Models\Note;
use App\Helpers\AuditLogger;

class NoteObserver
{
    private static $handledEvents = [];

    public function created(Note $note)
    {
        $eventKey = 'created_'.$note->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            AuditLogger::logModelAction(
                'create',
                $note,
                null,
                $note->getAttributes()
            );
            self::$handledEvents[$eventKey] = true;
        }
    }

    public function updated(Note $note)
    {
        $eventKey = 'updated_'.$note->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            $changes = $note->getChanges();
            $original = array_intersect_key($note->getOriginal(), $changes);

            AuditLogger::logModelAction(
                'update',
                $note,
                $original,
                $changes
            );
            self::$handledEvents[$eventKey] = true;
        }
    }

    public function deleted(Note $note)
    {
        $eventKey = 'deleted_'.$note->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            AuditLogger::logModelAction(
                'delete',
                $note,
                $note->getOriginal(),
                ['deleted_at' => now()]
            );
            self::$handledEvents[$eventKey] = true;
        }
    }

    public function restored(Note $note)
    {
        $eventKey = 'restored_'.$note->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            AuditLogger::logModelAction(
                'restore',
                $note,
                null,
                ['deleted_at' => null]
            );
            self::$handledEvents[$eventKey] = true;
        }
    }
}
