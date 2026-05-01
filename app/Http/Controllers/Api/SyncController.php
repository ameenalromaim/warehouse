<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    $this->upsert($table, $record);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data synced successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
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
            'units'
        ];

        $data = [];

        foreach ($tables as $table) {
            $data[$table] = DB::table($table)
                ->where('updated_at', '>', $lastSync)
                ->orWhere('synced_at', null)
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'server_time' => now()
        ]);
    }

    /**
     * =========================
     * 3. UPSERT موحد
     * =========================
     */
    private function upsert(string $table, array $data)
    {
        $uuid = $data['uuid'] ?? Str::uuid();

        $exists = DB::table($table)->where('uuid', $uuid)->first();

        $data['uuid'] = $uuid;
        $data['updated_at'] = now();
        $data['synced_at'] = now();

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

            DB::table($table)->insert($data);
        }
    }
}