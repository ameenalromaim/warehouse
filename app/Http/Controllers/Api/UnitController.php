<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\units;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * عرض جميع الوحدات
     */
    public function index()
    {
        $rows = units::query()->with('creator')->orderBy('name')->get();

        return response()->json(
            $rows->map(fn ($u) => ApiPresenter::unit($u))->values()
        );
    }

    /**
     * إضافة وحدة جديدة
     */
 public function store(Request $request)
{
    $this->authorizeSuperAdmin($request);

    $data = $request->validate([
        'uuid' => 'required|uuid|unique:units,uuid',
        'name' => 'required|string|max:255|unique:units,name',
    ]);

    $unit = units::create([
        'uuid' => $data['uuid'],
        'name' => trim($data['name']),
        'user_id' => $request->user()->id,
    ]);

    return response()->json([
        'message' => 'تم إنشاء الوحدة بنجاح',
        'data' => ApiPresenter::unit($unit->load('creator')),
    ], 201);
}

    /**
     * عرض وحدة واحدة
     */
    public function show(units $unit)
    {
        return response()->json(ApiPresenter::unit($unit->load('creator')));
    }

    /**
     * تعديل وحدة
     */
    public function update(Request $request, units $unit)
    {
        $this->authorizeSuperAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('units', 'name')->ignore($unit->uuid, 'uuid')],
        ]);

        $unit->update([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'message' => 'تم تعديل الوحدة بنجاح',
            'data' => ApiPresenter::unit($unit->fresh()->load('creator')),
        ]);
    }

    /**
     * حذف وحدة
     */
    public function destroy(Request $request, units $unit)
    {
        $this->authorizeSuperAdmin($request);

        $unit->delete();

        return response()->json([
            'message' => 'تم حذف الوحدة بنجاح',
        ]);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        if (! $request->user()?->isSuperAdmin()) {
            abort(403, __('تعديل الوحدات متاح لسوبر الأدمن فقط.'));
        }
    }
//     private function authorizeSuperAdmin(Request $request): void
// {
//     if (
//         ! $request->user()?->isSuperAdmin() &&
//         ! $request->user()?->isBranchUser()
//     ) {
//         abort(403, __('غير مصرح لك.'));
//     }
// }
}
