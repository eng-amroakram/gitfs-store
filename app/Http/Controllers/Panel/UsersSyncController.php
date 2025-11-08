<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersSyncController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Sync users on server to api mobile app
    public function syncUsers(Request $request)
    {
        $users = $this->userService->allUnsynced([], ['*']);
        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function confirmSync(Request $request)
    {
        try {
            DB::beginTransaction();

            $ids = $request->input('ids', []); // array of IDs or UUIDs

            if (empty($ids)) {
                return response()->json(['message' => 'No IDs provided'], 400);
            }

            $this->userService->confirmSync($ids);

            DB::commit();

            return response()->json([
                'message' => 'Users synced_at updated successfully',
                'count' => count($ids)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to confirm sync', 'error' => $e->getMessage()], 500);
        }
    }
}
