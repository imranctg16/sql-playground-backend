<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class StreakController extends Controller
{
    public function recordActivity(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $today = Carbon::today()->toDateString();
        
        // Try to get or create today's activity record
        $activity = DB::table('daily_activities')
            ->where('user_id', $user ? $user->id : null)
            ->where('activity_date', $today)
            ->first();

        if (!$activity) {
            DB::table('daily_activities')->insert([
                'user_id' => $user ? $user->id : null,
                'activity_date' => $today,
                'questions_attempted' => 0,
                'questions_completed' => 0,
                'points_earned' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $activity = DB::table('daily_activities')
                ->where('user_id', $user ? $user->id : null)
                ->where('activity_date', $today)
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    public function incrementAttempt(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $today = Carbon::today()->toDateString();
        
        // Ensure today's activity record exists
        DB::table('daily_activities')->updateOrInsert(
            [
                'user_id' => $user ? $user->id : null,
                'activity_date' => $today
            ],
            [
                'updated_at' => now()
            ]
        );

        // Increment attempts
        DB::table('daily_activities')
            ->where('user_id', $user ? $user->id : null)
            ->where('activity_date', $today)
            ->increment('questions_attempted');

        return response()->json(['success' => true]);
    }

    public function incrementCompletion(Request $request): JsonResponse
    {
        $request->validate([
            'points' => 'required|integer|min:0'
        ]);

        $user = auth('sanctum')->user();
        $today = Carbon::today()->toDateString();
        $points = $request->points;
        
        // Ensure today's activity record exists
        DB::table('daily_activities')->updateOrInsert(
            [
                'user_id' => $user ? $user->id : null,
                'activity_date' => $today
            ],
            [
                'updated_at' => now()
            ]
        );

        // Increment completions and points
        DB::table('daily_activities')
            ->where('user_id', $user ? $user->id : null)
            ->where('activity_date', $today)
            ->increment('questions_completed');
            
        DB::table('daily_activities')
            ->where('user_id', $user ? $user->id : null)
            ->where('activity_date', $today)
            ->increment('points_earned', $points);

        return response()->json(['success' => true]);
    }

    public function getStreak(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;
        
        // Get recent activity (last 90 days to calculate streak properly)
        $activities = DB::table('daily_activities')
            ->where('user_id', $userId)
            ->where('activity_date', '>=', Carbon::today()->subDays(90))
            ->where('questions_attempted', '>', 0) // Only count days with actual attempts
            ->orderBy('activity_date', 'desc')
            ->pluck('activity_date')
            ->toArray();

        if (empty($activities)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'last_activity_date' => null,
                    'is_active_today' => false
                ]
            ]);
        }

        $currentStreak = $this->calculateCurrentStreak($activities);
        $longestStreak = $this->calculateLongestStreak($activities);
        $today = Carbon::today()->toDateString();
        $isActiveToday = in_array($today, $activities);

        return response()->json([
            'success' => true,
            'data' => [
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'last_activity_date' => !empty($activities) ? $activities[0] : null,
                'is_active_today' => $isActiveToday
            ]
        ]);
    }

    public function getActivityCalendar(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;
        
        $year = $request->query('year', Carbon::now()->year);
        $month = $request->query('month', Carbon::now()->month);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $activities = DB::table('daily_activities')
            ->where('user_id', $userId)
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->select('activity_date', 'questions_attempted', 'questions_completed', 'points_earned')
            ->get()
            ->keyBy('activity_date');

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'month' => $month,
                'activities' => $activities
            ]
        ]);
    }

    private function calculateCurrentStreak(array $activities): int
    {
        if (empty($activities)) return 0;
        
        $streak = 0;
        $today = Carbon::today();
        $checkDate = Carbon::parse($activities[0]); // Start from most recent activity
        
        // If the most recent activity is not today or yesterday, streak is broken
        if ($checkDate->lt($today->copy()->subDay())) {
            return 0;
        }
        
        // If most recent activity is today, start checking from today
        // If most recent activity is yesterday, start checking from yesterday
        $currentCheck = $checkDate->gte($today) ? $today : $checkDate;
        
        foreach ($activities as $activityDate) {
            $activityCarbon = Carbon::parse($activityDate);
            
            if ($activityCarbon->eq($currentCheck)) {
                $streak++;
                $currentCheck = $currentCheck->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }

    public function resetProgress(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;
        
        // Delete all activity records for the user
        $deleted = DB::table('daily_activities')
            ->where('user_id', $userId)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Progress reset successfully',
            'deleted_records' => $deleted
        ]);
    }

    private function calculateLongestStreak(array $activities): int
    {
        if (empty($activities)) return 0;
        
        $longestStreak = 1;
        $currentStreak = 1;
        
        // Sort activities by date (oldest first for this calculation)
        $sortedActivities = array_reverse($activities);
        
        for ($i = 1; $i < count($sortedActivities); $i++) {
            $prevDate = Carbon::parse($sortedActivities[$i - 1]);
            $currentDate = Carbon::parse($sortedActivities[$i]);
            
            // If consecutive days
            if ($currentDate->diffInDays($prevDate) === 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }
        
        return $longestStreak;
    }
}
