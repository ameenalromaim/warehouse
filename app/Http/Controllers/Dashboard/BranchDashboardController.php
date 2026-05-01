<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BranchDashboardController extends Controller
{
    public function index()
    {
        $branches = User::query()
            ->whereNotNull('type_location')
            ->where('type_location', '!=', '')
            ->latest()
            ->paginate(10);

        return view('dashboard.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('dashboard.branches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($validated['branch_name']);

        User::create([
            'name' => $name,
            'type_location' => $name,
            'email' => 'branch-'.Str::uuid().'@branches.local',
            'password' => Hash::make(Str::password()),
        ]);

        return redirect()
            ->route('dashboard.branches')
            ->with('success', 'تم إضافة الفرع بنجاح.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->assertBranchUser($user);

        $validated = $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($validated['branch_name']);

        $user->update([
            'name' => $name,
            'type_location' => $name,
        ]);

        return redirect()
            ->route('dashboard.branches')
            ->with('success', 'تم تعديل بيانات الفرع بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->assertBranchUser($user);

        $user->delete();

        return redirect()
            ->route('dashboard.branches')
            ->with('success', 'تم حذف الفرع بنجاح.');
    }

    private function assertBranchUser(User $user): void
    {
        if (trim((string) $user->type_location) === '') {
            abort(404);
        }
    }
}
