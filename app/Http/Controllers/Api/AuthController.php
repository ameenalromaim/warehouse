<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** توحيد رقم الهاتف للبحث (مثل الداشبورد: 0771738225 أو 967771738225 → 771738225) */
    protected function normalizePhone(string $input): string
    {
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '967')) {
            return substr($digits, 3);
        }

        return $digits;
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_BRANCH_USER,
        ]);

        $deviceName = $data['device_name'] ?? 'flutter';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'User registered',
            'token' => $token,
            'user' => ApiPresenter::userPublic($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => 'required|string|max:32',
            'password' => 'required',
            'device_name' => 'nullable|string|max:255',
        ]);

        $phone = $this->normalizePhone($data['phone']);
        $user = $phone !== '' ? User::where('phone', $phone)->first() : null;

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => [__('بيانات الدخول غير صحيحة')],
            ]);
        }

        $deviceName = $data['device_name'] ?? 'flutter';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'ok',
            'token' => $token,
            'user' => ApiPresenter::userPublic($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json(ApiPresenter::userPublic($request->user()));
    }
}
