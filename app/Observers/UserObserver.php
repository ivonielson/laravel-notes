<?php

namespace App\Observers;

use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Support\Arr;

class UserObserver
{
    private static $handledEvents = [];

    public function created(User $user)
    {
        $eventKey = 'created_'.$user->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            $attributes = $user->getAttributes();
            $safeAttributes = Arr::except($attributes, ['password']); // Remove múltiplos campos

            AuditLogger::logModelAction(
                'create',
                $user,
                null,
                $safeAttributes
            );
            self::$handledEvents[$eventKey] = true;
        }
    }


    public function updated(User $user)
    {
        $eventKey = 'updated_'.$user->id;

        if (!isset(self::$handledEvents[$eventKey])) {
            $changes = $user->getChanges();
            $original = array_intersect_key($user->getOriginal(), $changes);

            AuditLogger::logModelAction(
                'update',
                $user,
                $original,
                $changes
            );
            self::$handledEvents[$eventKey] = true;
        }
    }


}
