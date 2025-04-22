<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Registra uma ação genérica no sistema
     *
     * @param string $action Ação realizada (ex: 'login', 'search')
     * @param string|null $model Nome do modelo relacionado (opcional)
     * @param int|null $modelId ID do modelo (opcional)
     * @param array|null $oldValues Valores antigos (para updates)
     * @param array|null $newValues Valores novos
     * @param array $metadata Metadados adicionais
     * @return void
     */
    private static $processedActions = [];

    /**
     * Registra uma ação genérica no sistema com verificação de duplicação
     */
    public static function log(
        string $action,
        ?string $model = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ) {
        $actionKey = self::generateActionKey($action, $model, $modelId);

        if (isset(self::$processedActions[$actionKey])) {
            return;
        }

        try {
            $data = [
                'user_id' => self::getCurrentUserId(),
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'ip_address' => self::getClientIp(),
                'user_agent' => request()->header('User-Agent'),
                'old_values' => $oldValues ? self::safeJsonEncode($oldValues) : null,
                'new_values' => $newValues ? self::safeJsonEncode(array_merge($newValues, $metadata)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            AuditLog::create($data);
            self::$processedActions[$actionKey] = true;
        } catch (\Exception $e) {
            logger()->error('Audit log error: ' . $e->getMessage());
        }
    }

    /**
     * Gera uma chave única para a ação
     */
    protected static function generateActionKey(
        string $action,
        ?string $model,
        ?int $modelId
    ): string {
        return md5($action . '|' . $model . '|' . $modelId . '|' . now()->format('Y-m-d H:i'));
    }

    /**
     * Registra uma ação específica em um modelo
     *
     * @param string $action
     * @param Model $model
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param array $metadata
     * @return void
     */
    public static function logModelAction(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ) {
        self::log(
            $action,
            get_class($model),
            $model->getKey(),
            $oldValues,
            $newValues,
            $metadata
        );
    }

    /**
     * Registra a visualização de um modelo
     *
     * @param Model $model
     * @param string|null $viewType Tipo de visualização (ex: 'detail', 'edit')
     * @param array $metadata
     * @return void
     */
    public static function logView(
        Model $model,
        ?string $viewType = null,
        array $metadata = []
    ) {
        self::log(
            'view',
            get_class($model),
            $model->getKey(),
            null,
            array_merge(['view_type' => $viewType], $metadata)
        );
    }

    /**
     * Registra uma visualização de lista/coleção
     *
     * @param string $model
     * @param int $count
     * @param array $filters
     * @return void
     */
    public static function logListView(
        string $model,
        int $count = 0,
        array $filters = []
    ) {
        self::log(
            'view',
            $model,
            null,
            null,
            [
                'item_count' => $count,
                'filters' => $filters
            ]
        );
    }

    /**
     * Registra uma tentativa de acesso não autorizado
     *
     * @param string $action
     * @param Model|null $model
     * @param string $reason
     * @return void
     */
    public static function logUnauthorizedAttempt(
        string $action,
        ?Model $model = null,
        string $reason = 'Unauthorized'
    ) {
        $modelClass = $model ? get_class($model) : null;
        $modelId = $model ? $model->getKey() : null;

        self::log(
            'unauthorized',
            $modelClass,
            $modelId,
            null,
            [
                'attempted_action' => $action,
                'reason' => $reason
            ]
        );
    }

    /**
     * Obtém o IP real do cliente, considerando proxies
     *
     * @return string
     */
    protected static function getClientIp(): string
    {
        // request()->getClientIp(); atualizar a função para usar o pronto do laravel.


        // $request = Request::instance();

        // foreach (
        //     [
        //         'HTTP_CLIENT_IP',
        //         'HTTP_X_FORWARDED_FOR',
        //         'HTTP_X_FORWARDED',
        //         'HTTP_X_CLUSTER_CLIENT_IP',
        //         'HTTP_FORWARDED_FOR',
        //         'HTTP_FORWARDED',
        //         'REMOTE_ADDR'
        //     ] as $key
        // ) {
        //     if ($request->server->has($key)) {
        //         foreach (explode(',', $request->server->get($key)) as $ip) {
        //             $ip = trim($ip);
        //             if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
        //                 return $ip;
        //             }
        //         }
        //     }
        // }

        // return $request->getClientIp() ?? '0.0.0.0';
        return request()->getClientIp() ?? '0.0.0.0';

    }

    /**
     * Obtém o ID do usuário atual
     *
     * @return int|null
     */
    protected static function getCurrentUserId(): ?int
    {
        if (Auth::check()) {
            return Auth::id();
        }

        if (session()->has('user.id')) {
            return session('user.id');
        }

        return null;
    }

    /**
     * Codifica dados para JSON de forma segura
     *
     * @param mixed $data
     * @return string|null
     */
    protected static function safeJsonEncode($data): ?string
    {
        try {
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            logger()->error('Failed to encode audit data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Registra a visualização de um item individual
     */
    public static function logItemView(
        Model $model,
        string $viewType,
        array $metadata = []
    ) {
        self::log(
            'view_item',
            get_class($model),
            $model->getKey(),
            null,
            array_merge([
                'view_type' => $viewType,
                'viewed_at' => now()->toDateTimeString()
            ], $metadata)
        );
    }

    /**
     * Registra a visualização de uma lista/coleção
     */
    public static function logCollectionView(
        string $modelClass,
        int $itemCount = 0,
        array $filters = [],
        array $metadata = []
    ) {
        self::log(
            'view',
            $modelClass,
            null,
            null,
            array_merge([
                'item_count' => $itemCount,
                'filters' => $filters,
                'viewed_at' => now()->toDateTimeString()
            ], $metadata)
        );
    }
}
