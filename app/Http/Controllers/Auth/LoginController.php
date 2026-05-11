<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * توحيد رقم الهاتف للبحث في قاعدة البيانات:
     * 0771738225، +967 771 738 225، 00967…، 967771738225 → 771738225
     * 9665xxxxxxxx (السعودية) → 5xxxxxxxx
     * وتحويل الأرقام العربية (٠١٢…) إلى إنجليزية.
     */
    protected function normalizePhone(string $input): string
    {
        $input = $this->convertArabicDigitsToWestern($input);
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        while (strlen($digits) > 12 && str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '967')) {
            return substr($digits, 3);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '966')) {
            return substr($digits, 3);
        }

        return $digits;
    }

    /**
     * قائمة أشكال الرقم للمطابقة مع عمود users.phone (قد يُخزَّن بصيغ مختلفة).
     *
     * @return list<string>
     */
    protected function phoneLookupCandidates(string $rawInput): array
    {
        $rawInput = trim($this->convertArabicDigitsToWestern($rawInput));
        if ($rawInput === '') {
            return [];
        }

        $digits = preg_replace('/\D+/', '', $rawInput) ?? '';
        $normalized = $this->normalizePhone($rawInput);

        $candidates = [
            $normalized,
            $digits,
        ];

        if ($normalized !== '' && strlen($normalized) >= 9 && strlen($normalized) <= 10) {
            $candidates[] = '0'.$normalized;
        }

        return array_values(array_unique(array_filter(
            $candidates,
            static fn ($v) => is_string($v) && $v !== ''
        )));
    }

    protected function convertArabicDigitsToWestern(string $value): string
    {
        static $map = [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ];

        return strtr($value, $map);
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.purchases');
        }

        return view('auth.login');
    }

    /**
     * تسجيل الدخول برقم الهاتف فقط (بدون بريد إلكتروني).
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'password' => ['required', 'string'],
        ]);

        $rawPhone = trim($this->convertArabicDigitsToWestern((string) $request->input('phone', '')));

        if (str_contains($rawPhone, '@')) {
            return back()->withErrors([
                'phone' => 'يُسمح بتسجيل الدخول برقم الهاتف فقط.',
            ])->onlyInput('phone');
        }

        $password = (string) $request->input('password', '');
        $remember = $request->boolean('remember');

        $candidates = $this->phoneLookupCandidates($rawPhone);

        if ($candidates === []) {
            return back()->withErrors([
                'phone' => 'أدخل رقم هاتف صالحاً.',
            ])->onlyInput('phone');
        }

        $user = User::query()
            ->whereIn('phone', $candidates)
            ->first();

        if ($user === null || ! Hash::check($password, $user->getAuthPassword())) {
            return back()->withErrors([
                'phone' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
            ])->onlyInput('phone');
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.purchases'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
