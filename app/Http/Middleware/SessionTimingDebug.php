<?php

namespace App\Http\Middleware;

use App\Support\AgentDebugLog;
use Closure;
use Illuminate\Http\Request;

class SessionTimingDebug
{
    public function handle(Request $request, Closure $next)
    {
        $reqT0 = (float) $request->attributes->get('_agent_debug_t0', microtime(true));
        AgentDebugLog::write('B', 'SessionTimingDebug.php:handle', 'session_loaded', [
            'path' => $request->path(),
            'to_session_ms' => (int) round((microtime(true) - $reqT0) * 1000),
        ]);

        $handlerT0 = microtime(true);
        $response = $next($request);

        AgentDebugLog::write('B', 'SessionTimingDebug.php:handle', 'handler_after_session_ms', [
            'path' => $request->path(),
            'handler_ms' => (int) round((microtime(true) - $handlerT0) * 1000),
        ]);

        return $response;
    }
}

