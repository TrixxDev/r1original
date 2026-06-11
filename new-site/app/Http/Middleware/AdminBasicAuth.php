<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Простая защита админки HTTP Basic-аутентификацией (ADMIN_USER / ADMIN_PASSWORD
 * в .env), пока не перенесены пользователи и роли (spatie/laravel-permission).
 * Без заданного пароля админка доступна только в local-окружении.
 */
class AdminBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('site.admin.user');
        $password = config('site.admin.password');

        if (! $password) {
            abort_unless(app()->isLocal(), 403, 'Admin ir atslēgts: norādiet ADMIN_PASSWORD .env failā.');

            return $next($request);
        }

        if (hash_equals((string) $user, (string) $request->getUser())
            && hash_equals((string) $password, (string) $request->getPassword())) {
            return $next($request);
        }

        return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic realm="R1 Admin"']);
    }
}
