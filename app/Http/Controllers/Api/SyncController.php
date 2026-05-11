<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * =========================
     * 1. PUSH: من التطبيق للسيرفر
     * =========================
     */
    public function push(Request $request)
    {
        $payload = $request->input('data', []);

        DB::beginTransaction();

        try {
            foreach ($payload as $table => $records) {
                foreach ($records as $record) {
                    $this->upsert($request, $table, $record);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data synced successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * =========================
     * 2. PULL: من السيرفر للتطبيق
     * =========================
     */
    public function pull(Request $request)
    {
        $lastSync = $request->input('last_sync'); // timestamp

        $tables = [
            'product',
            'purchase',
            'purchaseitem',
            'returns',
            'return_items',
            'suppliers',
            'units',
        ];

        $data = [];

        /** @var User|null $user */
        $user = $request->user();

        foreach ($tables as $table) {
            if ($user instanceof User && $user->isBranchUser() && $user->branchScopeKey() === null) {
                $data[$table] = collect();

                continue;
            }

            $branchLoc = ($user instanceof User && $user->isBranchUser()) ? $user->branchScopeKey() : null;

            $q = DB::table($table)->where(function ($w) use ($lastSync) {
                $w->where('updated_at', '>', $lastSync)
                    ->orWhereNull('synced_at');
            });

            if ($branchLoc !== null && Schema::hasColumn($table, 'type_location')) {
                $q->where('type_location', $branchLoc);
            }

            $data[$table] = $q->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'server_time' => now(),
        ]);
    }

    /**
     * =========================
     * 3. UPSERT موحد
     * =========================
     */
    private function upsert(Request $request, string $table, array $data): void
    {
        $uuid = $data['uuid'] ?? Str::uuid();

        $exists = DB::table($table)->where('uuid', $uuid)->first();

        unset($data['user_id']);

        $data['uuid'] = $uuid;
        $data['updated_at'] = now();
        $data['synced_at'] = now();

        /** @var User|null $user */
        $user = $request->user();

        if ($user instanceof User && $user->isBranchUser()) {
            $loc = $user->branchScopeKey();
            if ($loc === null) {
                throw new \RuntimeException('Branch user has no type_location');
            }
            if (Schema::hasColumn($table, 'type_location')) {
                $data['type_location'] = $loc;
            }
        }

        if ($exists) {

            // حماية من overwrite قديم
            if (isset($data['version']) &&
                $data['version'] <= $exists->version) {
                return;
            }

            $data['version'] = ($exists->version ?? 1) + 1;

            DB::table($table)
                ->where('uuid', $uuid)
                ->update($data);

        } else {
            $data['version'] = 1;
            $data['created_at'] = now();

            if ($user instanceof User && Schema::hasColumn($table, 'user_id')) {
                $data['user_id'] = $user->getKey();
            }

            DB::table($table)->insert($data);
        }
    }
}
