<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** توحيد رقم الهاتف للبحث (مثال: 0771738225 أو 967771738225 → 771738225) */
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

    // عرض صفحة الدخول
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.purchases');
        }

        return view('auth.login');
    }

    // تنفيذ تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:32',
            'password' => 'required',
        ]);

        $phone = $this->normalizePhone($request->input('phone', ''));

        if ($phone === '' || ! Auth::attempt(['phone' => $phone, 'password' => $request->password])) {
            return back()->withErrors([
                'phone' => 'بيانات الدخول غير صحيحة',
            ])->onlyInput('phone');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard.purchases');
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}