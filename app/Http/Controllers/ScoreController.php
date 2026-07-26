<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    /**
     * Called by Unity after a quiz/exam session.
     * POST /api/save_score
     * Body: { student_id, class_id, subject, type, quarter, sequence_number, correct, total }
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|integer|exists:users,id',
            'class_id'        => 'nullable|integer',
            'subject'         => 'required|string|max:100',
            'type'            => 'required|in:quiz,exam,assessment,prototype',
            'quarter'         => 'required|integer|between:1,4',
            'sequence_number' => 'required|integer|min:1',
            'correct'         => 'required|integer|min:0',
            'total'           => 'required|integer|min:1',
        ]);

        $correct = (int) $request->correct;
        $total   = (int) $request->total;
        $percent = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

        DB::table('student_scores')->insert([
            'student_id'      => $request->student_id,
            'class_id'        => $request->class_id ?: null,
            'subject'         => strtolower(trim($request->subject)),
            'type'            => $request->type,
            'quarter'         => $request->quarter,
            'sequence_number' => $request->sequence_number,
            'correct'         => $correct,
            'total'           => $total,
            'percent'         => $percent,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully',
            'percent' => $percent,
        ]);
    }
}
