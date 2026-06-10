<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // Без web middleware — быстрее, меньше шанс 500 от сессии/debug
            Route::get('/_migrate-sessions/{token}', function (string $token) {
                try {
                    if (! class_exists(\App\Support\FileSessionMigrator::class)) {
                        return response("ОШИБКА: залейте app/Support/FileSessionMigrator.php\n", 500)
                            ->header('Content-Type', 'text/plain; charset=UTF-8');
                    }

                    $expected = (string) config('session.migrate_token', '');
                    if ($expected === '' || ! hash_equals($expected, $token)) {
                        abort(404);
                    }

                    $dry = isset($_GET['dry']);
                    $delete = isset($_GET['delete']);

                    $result = \App\Support\FileSessionMigrator::run($dry, $delete);
                    $output = \App\Support\FileSessionMigrator::formatResult($result);

                    if (! $dry && ($result['ok'] ?? false)) {
                        $output .= "\n\nГотово. Теперь в .env: SESSION_DRIVER=database\n";
                    }

                    return response($output, ($result['ok'] ?? false) ? 200 : 500)
                        ->header('Content-Type', 'text/plain; charset=UTF-8');
                } catch (\Throwable $e) {
                    return response('FATAL: '.$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n", 500)
                        ->header('Content-Type', 'text/plain; charset=UTF-8');
                }
            });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
