<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserDashboardController extends Controller
{
    /**
     * قائمة الفروع للقوائم المنسدلة (ثابتة + أي فرع مسجل في النظام).
     *
     * @return list<string>
     */
    public static function branchOptions(): array
    {
        $defaults = ['فرع ذهبان', 'فرع صرف'];

        $fromDb = User::query()
            ->whereNotNull('type_location')
            ->where('type_location', '!=', '')
            ->distinct()
            ->orderBy('type_location')
            ->pluck('type_location')
            ->all();

        return array_values(array_unique(array_merge($defaults, $fromDb)));
    }

    public function index()
    {
        $users = User::query()->latest()->paginate(10);
        $branchOptions = self::branchOptions();

        return view('dashboard.users.index', compact('users', 'branchOptions'));
    }

    public function create()
    {
        $branchOptions = self::branchOptions();

        return view('dashboard.users.create', compact('branchOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', Rule::unique('users', 'phone')],
            'role' => ['required', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_BRANCH_USER])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($request->input('role') === User::ROLE_BRANCH_USER) {
            $rules['type_location'] = ['required', 'string', Rule::in(self::branchOptions())];
        } else {
            $rules['type_location'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, $this->userValidationMessages());

        $phone = trim($validated['phone']);

        User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone' => $phone,
            'role' => $validated['role'],
            'type_location' => $validated['role'] === User::ROLE_BRANCH_USER
                ? $validated['type_location']
                : null,
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('dashboard.users')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:32', Rule::unique('users', 'phone')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_BRANCH_USER])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($request->input('role') === User::ROLE_BRANCH_USER) {
            $rules['type_location'] = ['required', 'string', Rule::in(self::branchOptions())];
        } else {
            $rules['type_location'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, $this->userValidationMessages());

        $phone = trim($validated['phone']);

        $data = [
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone' => $phone,
            'role' => $validated['role'],
            'type_location' => $validated['role'] === User::ROLE_BRANCH_USER
                ? $validated['type_location']
                : null,
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
