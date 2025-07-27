<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth('sanctum')->user();
        
        $questions = DB::table('questions')
            ->select('id', 'title', 'description', 'difficulty', 'category', 'points', 'hint', 'hint_penalty')
            ->orderBy('difficulty')
            ->orderBy('points')
            ->get();

        // If user is authenticated, add completion status
        if ($user) {
            $completedQuestions = DB::table('user_progress')
                ->where('user_id', $user->id)
                ->pluck('question_id')
                ->toArray();

            $questions = $questions->map(function ($question) use ($completedQuestions) {
                $question->completed = in_array($question->id, $completedQuestions);
                return $question;
            });
        } else {
            // Add completed = false for all questions when not authenticated
            $questions = $questions->map(function ($question) {
                $question->completed = false;
                return $question;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    public function show($id): JsonResponse
    {
        $question = DB::table('questions')->find($id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $question
        ]);
    }

    public function getByDifficulty($difficulty): JsonResponse
    {
        $allowedDifficulties = ['easy', 'medium', 'hard'];
        
        if (!in_array($difficulty, $allowedDifficulties)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid difficulty level'
            ], 400);
        }

        $questions = DB::table('questions')
            ->where('difficulty', $difficulty)
            ->select('id', 'title', 'description', 'difficulty', 'category', 'points', 'hint', 'hint_penalty')
            ->orderBy('points')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    public function getCategories(): JsonResponse
    {
        $categories = DB::table('questions')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function evaluateQuery(Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'user_sql' => 'required|string',
            'query_type' => 'required|in:sql,laravel',
            'hints_used' => 'nullable|integer|min:0',
            'viewed_solution' => 'nullable|boolean'
        ]);

        $questionId = $request->question_id;
        $userQuery = trim($request->user_sql);
        $queryType = $request->query_type;

        // Get the question and expected result
        $question = DB::table('questions')->find($questionId);
        
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found'
            ], 404);
        }

        try {
            // Execute the expected query to get the correct result
            $expectedResult = $this->executeQuery($question->expected_sql);
            
            // Execute user's query
            if ($queryType === 'sql') {
                $userResult = $this->executeQuery($userQuery);
            } else {
                // For Laravel queries, we need to convert them to SQL and execute
                // This is a simplified approach - in a real app, you might want to evaluate Laravel code directly
                return response()->json([
                    'success' => false,
                    'message' => 'Laravel query evaluation not fully implemented in this demo. Please use SQL for now.'
                ], 400);
            }

            // Compare results
            $isCorrect = $this->compareResults($expectedResult, $userResult);

            // Calculate final points after penalties
            $hintsUsed = $request->input('hints_used', 0);
            $viewedSolution = $request->input('viewed_solution', false);
            
            $basePoints = $question->points;
            $hintPenalty = $hintsUsed * ($question->hint_penalty ?? 2);
            $solutionPenalty = $viewedSolution ? $basePoints : 0; // No points if solution was viewed
            
            $finalPoints = max(0, $basePoints - $hintPenalty);
            if ($viewedSolution) {
                $finalPoints = 0; // No points if solution was viewed
            }

            // Save progress if user is authenticated and answer is correct
            $user = auth('sanctum')->user();
            $pointsEarned = 0;
            $isNewCompletion = false;
            
            if ($user && $isCorrect) {
                // Check if user has already completed this question
                $existingProgress = DB::table('user_progress')
                    ->where('user_id', $user->id)
                    ->where('question_id', $questionId)
                    ->first();
                
                if (!$existingProgress) {
                    // Save new progress with calculated points
                    DB::table('user_progress')->insert([
                        'user_id' => $user->id,
                        'question_id' => $questionId,
                        'points_earned' => $finalPoints,
                        'completed_at' => now()
                    ]);
                    $pointsEarned = $finalPoints;
                    $isNewCompletion = true;
                } else {
                    $pointsEarned = $finalPoints; // Show calculated point value
                    $isNewCompletion = false;
                }
            } else if ($isCorrect) {
                // Not authenticated but answer is correct
                $pointsEarned = $finalPoints;
            }

            // Update streak activity for any query attempt
            $this->recordActivityAttempt();
            
            // If answer is correct, update completion streak
            if ($isCorrect) {
                $this->recordActivityCompletion($pointsEarned);
            }

            $message = $isCorrect ? 
                ($isNewCompletion ? 'Correct! Well done!' : 'Correct! You\'ve already completed this question.') 
                : 'Not quite right. Check your query and try again.';
            
            // Add penalty information to message if applicable
            if ($isCorrect && $hintsUsed > 0) {
                $message .= " (Points reduced by {$hintPenalty} for using hints)";
            }
            if ($isCorrect && $viewedSolution) {
                $message .= " (No points awarded - solution was viewed)";
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_correct' => $isCorrect,
                    'expected_result' => $expectedResult,
                    'user_result' => $userResult,
                    'points_earned' => $pointsEarned,
                    'is_new_completion' => $isNewCompletion,
                    'base_points' => $basePoints,
                    'hint_penalty' => $hintPenalty,
                    'viewed_solution' => $viewedSolution,
                    'message' => $message
                ]
            ]);

        } catch (\Exception $e) {
            // Extract more detailed error information
            $errorDetails = [
                'query' => $userQuery,
                'sql_error' => $e->getMessage()
            ];

            // Check if it's a database-specific error
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorDetails['sql_error'] = $e->getMessage();
                $errorDetails['error_code'] = $e->getCode();
            }

            return response()->json([
                'success' => false,
                'message' => 'Query execution failed: ' . $e->getMessage(),
                'error_details' => $errorDetails
            ], 400);
        }
    }

    private function executeQuery(string $query): array
    {
        // Remove semicolon if present and trim
        $query = rtrim(trim($query), ';');
        
        // Basic security check - only allow SELECT statements
        if (!preg_match('/^\s*SELECT\s+/i', $query)) {
            throw new \Exception('Only SELECT statements are allowed for security reasons');
        }

        try {
            // Execute the query
            $results = DB::select($query);
            
            // Convert to array for easier comparison
            return json_decode(json_encode($results), true);
        } catch (\Illuminate\Database\QueryException $e) {
            // Re-throw with more context
            throw new \Exception('SQL Error: ' . $e->getMessage() . ' (Query: ' . $query . ')');
        }
    }

    private function compareResults(array $expected, array $user): bool
    {
        // If different number of rows, definitely not correct
        if (count($expected) !== count($user)) {
            return false;
        }

        // Sort both arrays to handle order differences (for some queries)
        // Note: This might not be appropriate for all query types
        $sortedExpected = $expected;
        $sortedUser = $user;
        
        // For each row, compare all fields
        for ($i = 0; $i < count($sortedExpected); $i++) {
            $expectedRow = $sortedExpected[$i];
            $userRow = $sortedUser[$i];

            // Convert to same type for comparison
            $expectedRow = array_map(function($value) {
                return is_numeric($value) ? (float)$value : (string)$value;
            }, $expectedRow);
            
            $userRow = array_map(function($value) {
                return is_numeric($value) ? (float)$value : (string)$value;
            }, $userRow);

            if ($expectedRow !== $userRow) {
                return false;
            }
        }

        return true;
    }

    public function getTableSchema(): JsonResponse
    {
        try {
            $tables = ['categories', 'products', 'orders', 'order_items'];
            $schema = [];

            foreach ($tables as $table) {
                $columns = DB::select("DESCRIBE {$table}");
                $schema[$table] = $columns;
            }

            return response()->json([
                'success' => true,
                'data' => $schema
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get table schema: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSampleData(Request $request): JsonResponse
    {
        $table = $request->query('table', 'products');
        $limit = min($request->query('limit', 10), 50); // Max 50 rows

        $allowedTables = ['categories', 'products', 'orders', 'order_items'];
        
        if (!in_array($table, $allowedTables)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid table name'
            ], 400);
        }

        try {
            $data = DB::table($table)->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sample data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserProgress(): JsonResponse
    {
        $user = auth('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $progress = DB::table('user_progress')
            ->join('questions', 'user_progress.question_id', '=', 'questions.id')
            ->where('user_progress.user_id', $user->id)
            ->select(
                'user_progress.question_id',
                'user_progress.points_earned',
                'user_progress.completed_at',
                'questions.title',
                'questions.difficulty',
                'questions.category'
            )
            ->orderBy('user_progress.completed_at', 'desc')
            ->get();

        $totalPoints = DB::table('user_progress')
            ->where('user_id', $user->id)
            ->sum('points_earned');

        return response()->json([
            'success' => true,
            'data' => [
                'progress' => $progress,
                'total_points' => $totalPoints,
                'total_completed' => count($progress)
            ]
        ]);
    }

    public function getSolution($id): JsonResponse
    {
        $question = DB::table('questions')->find($id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'question_id' => $question->id,
                'title' => $question->title,
                'sql_solution' => $question->expected_sql,
                'laravel_solution' => $question->expected_laravel
            ]
        ]);
    }

    /**
     * Record activity attempt for streak tracking
     */
    private function recordActivityAttempt()
    {
        try {
            $streakController = new \App\Http\Controllers\StreakController();
            $streakController->incrementAttempt(request());
        } catch (\Exception $e) {
            // Silently handle streak recording errors to not break query evaluation
            \Log::warning('Failed to record activity attempt: ' . $e->getMessage());
        }
    }

    /**
     * Record activity completion for streak tracking
     */
    private function recordActivityCompletion($points)
    {
        try {
            $streakController = new \App\Http\Controllers\StreakController();
            $request = new \Illuminate\Http\Request(['points' => $points]);
            $streakController->incrementCompletion($request);
        } catch (\Exception $e) {
            // Silently handle streak recording errors to not break query evaluation
            \Log::warning('Failed to record activity completion: ' . $e->getMessage());
        }
    }
}