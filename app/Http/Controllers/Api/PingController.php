<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PingController extends Controller
{
    /**
     * نقطه فحص: يستدعيها التطبيق (GET) للتأكد من أن الـ baseUrl صحيح.
     */
    public function __invoke(): JsonResponse
    {
        $payload = [
            'ok' => true,
            'name' => config('app.name'),
        ];

        if (config('app.debug')) {
            $payload['api_base'] = rtrim((string) config('api.base_url'), '/');
        }

        return response()->json($payload);
    }
}
