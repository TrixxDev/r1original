<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignShopGuestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('X-Guest-Id');
        if (is_string($header) && $header !== '' && $this->isUuid($header)) {
            $guestId = $header;
        } else {
            $guestId = (string) Str::uuid();
        }

        $request->attributes->set('shop_guest_id', $guestId);

        /** @var Response $response */
        $response = $next($request);

        return $response->header('X-Guest-Id', $guestId);
    }

    private function isUuid(string $s): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $s
        );
    }
}

