<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserDashboardController extends Controller
{
    /**
     * @return list<string>
     */
    public static function locationOptions(): array
    {
        return ['فرع ذهبان', 'فرع صرف'];
    }

    public function index()
    {
        $users = User::query()->latest()->paginate(10);
        $locationOptions = self::locationOptions();

        return view('dashboard.users.index', compact('users', 'locationOptions'));
    }

    public function create()
    {
        $locationOptions = self::locationOptions();

        return view('dashboard.users.create', compact('locationOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', Rule::unique('users', 'phone')],
            'type_location' => ['required', 'string', Rule::in(self::locationOptions())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->userValidationMessages());

        $phone = trim($validated['phone']);

        User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone' => $phone,
            'type_location' => $validated['type_location'],
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('dashboard.users')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:32', Rule::unique('users', 'phone')->ignore($user->id)],
            'type_location' => ['required', 'string', Rule::in(self::locationOptions())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], $this->userValidationMessages());

        $phone = trim($validated['phone']);

        $data = [
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone' => $phone,
            'type_location' => $validated['type_location'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()
            ->route('dashboard.users')
            ->with('success', 'تم تعديل بيانات المستخدم بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('dashboard.users')
                ->withErrors(['delete' => 'لا يمكن حذف المستخدم الحالي وهو مسجل الدخول.']);
        }

        $user->delete();

        return redirect()
            ->route('dashboard.users')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    /**
     * @return array<string, string>
     */
    private function userValidationMessages(): array
    {
        return [
            'phone.required' => 'من فضلك قم بإدخال رقم الهاتف',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمستخدم آخر.',
        ];
    }
}
