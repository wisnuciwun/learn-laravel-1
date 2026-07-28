<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        try {
            $today = Carbon::today()->toDateString();

            $attendance = Attendance::firstOrNew([
                'user_id' => $userData->id,
                'date' => $today,
            ]);

            if ($attendance->clock_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'sudah absen masuk hari ini',
                ], 422);
            }

            $attendance->instance_code = $userData->instance_code;
            $attendance->clock_in_at = now();
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil absen masuk',
                'data' => $attendance,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function clockOut(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        try {
            $today = Carbon::today()->toDateString();

            $attendance = Attendance::where('user_id', $userData->id)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->clock_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'belum clock in',
                ], 422);
            }

            if ($attendance->clock_out_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'sudah absen pulang',
                ], 422);
            }

            $attendance->clock_out_at = now();
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil absen pulang',
                'data' => $attendance,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function today(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        try {
            $attendance = Attendance::where('user_id', $userData->id)
                ->where('date', Carbon::today()->toDateString())
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Get today attendance successfully',
                'data' => $attendance,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function myHistory(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        try {
            $data = Attendance::where('user_id', $userData->id)
                ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereBetween('date', [$request->start_date, $request->end_date]);
                })
                ->when(!$request->start_date && !$request->end_date, function ($q) {
                    $q->whereBetween('date', [
                        Carbon::now()->firstOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                })
                ->orderByDesc('date')
                ->paginate(31);

            return response()->json([
                'success' => true,
                'message' => 'Get attendance history successfully',
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function report(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        if ($userData->is_owner != 1) {
            return response()->json([
                'success' => false,
                'message' => 'User not allowed',
            ], 403);
        }

        try {
            $data = Attendance::with('user:id,name,nickname')
                ->where('instance_code', $userData->instance_code)
                ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereBetween('date', [$request->start_date, $request->end_date]);
                })
                ->when(!$request->start_date && !$request->end_date, function ($q) {
                    $q->whereBetween('date', [
                        Carbon::now()->firstOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                })
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Get attendance report successfully',
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
