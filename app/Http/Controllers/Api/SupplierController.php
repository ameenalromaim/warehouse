<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\suppliers;
use App\Support\BranchData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = suppliers::query()
            ->with('creator');
            // ->forUserBranch($request->user());

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $suppliers = $query->latest()->paginate(500);

        return response()->json(
            $suppliers->through(fn ($s) => ApiPresenter::supplier($s))
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'type_location' => 'nullable|string|max:255',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        $supplier = suppliers::create(array_merge(
            $request->only('name', 'phone', 'address', 'note'),
            [
                'uuid' => $request->uuid ?? (string) Str::uuid(),
                'type_location' => $loc,
                'user_id' => $request->user()->id,
            ]
        ));

        return response()->json([
            'message' => 'تم إضافة المورد',
            'data' => ApiPresenter::supplier($supplier->load('creator')),
        ], 201);
    }

    public function show(Request $request, suppliers $supplier)
    {
        $this->authorizeBranchRecord($supplier);

        return response()->json(
            ApiPresenter::supplier($supplier->load('creator'))
        );
    }

    public function update(Request $request, suppliers $supplier)
    {
        $this->authorizeBranchRecord($supplier);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'type_location' => 'nullable|string|max:255',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        $supplier->update(array_merge(
            $request->only('name', 'phone', 'address', 'note'),
            [
                'type_location' => $loc,
            ]
        ));

        return response()->json([
            'message' => 'تم التحديث',
            'data' => ApiPresenter::supplier(
                $supplier->fresh()->load('creator')
            ),
        ]);
    }

    public function destroy(Request $request, suppliers $supplier)
    {
        $this->authorizeBranchRecord($supplier);

        $supplier->delete();

        return response()->json([
            'message' => 'تم الحذف',
        ]);
    }

    private function assertBranchUserHasBranch(Request $request): void
    {
        $user = $request->user();

        if (
            $user &&
            $user->isBranchUser() &&
            $user->branchScopeKey() === null
        ) {
            throw ValidationException::withMessages([
                'type_location' => [__('لم يُعرَّف فرع لهذا المستخدم.')],
            ]);
        }
    }
}