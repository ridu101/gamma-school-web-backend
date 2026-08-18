<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    // =========================
    // Admin Result List
    // =========================
    public function index()
    {
        $results = Result::with(['student', 'subjects'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }


    // =========================
    // Public Result Options
    // Class + Exam List
    // =========================
    public function options()
    {
        // যেসব class-এর result আছে শুধু সেগুলো দেখাবে
        $classes = Student::whereHas('results')
            ->where('status', true)
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        // Database থেকে unique exam name
        $exams = Result::select('exam_name')
            ->distinct()
            ->orderBy('exam_name')
            ->pluck('exam_name');

        return response()->json([
            'success' => true,
            'data' => [
                'classes' => $classes,
                'exams' => $exams,
            ],
        ]);
    }


    // =========================
    // Add Result
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_name' => 'required|string|max:255',
            'gpa' => 'required|numeric|min:0|max:5',
            'passed' => 'required|boolean',

            'subjects' => 'required|array|min:1',

            'subjects.*.subject' => 'required|string|max:255',
            'subjects.*.full_marks' => 'required|integer|min:1',
            'subjects.*.obtained_marks' => 'required|integer|min:0',
            'subjects.*.grade' => 'nullable|string|max:10',
        ]);

        // Same student + same exam duplicate prevent
        $exists = Result::where(
            'student_id',
            $validated['student_id']
        )
            ->where(
                'exam_name',
                $validated['exam_name']
            )
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' =>
                    'এই শিক্ষার্থীর এই পরীক্ষার ফলাফল ইতিমধ্যে রয়েছে।',
            ], 422);
        }

        // Obtained marks যেন full marks-এর বেশি না হয়
        foreach ($validated['subjects'] as $subject) {
            if (
                $subject['obtained_marks'] >
                $subject['full_marks']
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        $subject['subject'] .
                        ' এর প্রাপ্ত নম্বর পূর্ণমানের চেয়ে বেশি হতে পারবে না।',
                ], 422);
            }
        }

        // Total calculate
        $fullMarks = collect($validated['subjects'])
            ->sum('full_marks');

        $totalMarks = collect($validated['subjects'])
            ->sum('obtained_marks');

        $result = DB::transaction(function () use (
            $validated,
            $fullMarks,
            $totalMarks
        ) {
            $result = Result::create([
                'student_id' =>
                    $validated['student_id'],

                'exam_name' =>
                    $validated['exam_name'],

                'gpa' =>
                    $validated['gpa'],

                'full_marks' =>
                    $fullMarks,

                'total_marks' =>
                    $totalMarks,

                'passed' =>
                    $validated['passed'],
            ]);

            foreach (
                $validated['subjects']
                as $subject
            ) {
                $result->subjects()->create([
                    'subject' =>
                        $subject['subject'],

                    'full_marks' =>
                        $subject['full_marks'],

                    'obtained_marks' =>
                        $subject['obtained_marks'],

                    'grade' =>
                        $subject['grade'] ?? null,
                ]);
            }

            return $result;
        });

        $result->load([
            'student',
            'subjects',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Result created successfully',
            'data' => $result,
        ], 201);
    }


    // =========================
    // Update Result
    // =========================
    public function update(
        Request $request,
        Result $result
    ) {
        $validated = $request->validate([
            'student_id' =>
                'required|exists:students,id',

            'exam_name' =>
                'required|string|max:255',

            'gpa' =>
                'required|numeric|min:0|max:5',

            'passed' =>
                'required|boolean',

            'subjects' =>
                'required|array|min:1',

            'subjects.*.subject' =>
                'required|string|max:255',

            'subjects.*.full_marks' =>
                'required|integer|min:1',

            'subjects.*.obtained_marks' =>
                'required|integer|min:0',

            'subjects.*.grade' =>
                'nullable|string|max:10',
        ]);

        // Duplicate check
        $duplicate = Result::where(
            'student_id',
            $validated['student_id']
        )
            ->where(
                'exam_name',
                $validated['exam_name']
            )
            ->where(
                'id',
                '!=',
                $result->id
            )
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' =>
                    'এই শিক্ষার্থীর এই পরীক্ষার ফলাফল ইতিমধ্যে রয়েছে।',
            ], 422);
        }

        // Marks validation
        foreach ($validated['subjects'] as $subject) {
            if (
                $subject['obtained_marks'] >
                $subject['full_marks']
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        $subject['subject'] .
                        ' এর প্রাপ্ত নম্বর পূর্ণমানের চেয়ে বেশি হতে পারবে না।',
                ], 422);
            }
        }

        $fullMarks = collect($validated['subjects'])
            ->sum('full_marks');

        $totalMarks = collect($validated['subjects'])
            ->sum('obtained_marks');

        DB::transaction(function () use (
            $validated,
            $result,
            $fullMarks,
            $totalMarks
        ) {
            $result->update([
                'student_id' =>
                    $validated['student_id'],

                'exam_name' =>
                    $validated['exam_name'],

                'gpa' =>
                    $validated['gpa'],

                'full_marks' =>
                    $fullMarks,

                'total_marks' =>
                    $totalMarks,

                'passed' =>
                    $validated['passed'],
            ]);

            // Old subject remove
            $result->subjects()->delete();

            // New subjects insert
            foreach (
                $validated['subjects']
                as $subject
            ) {
                $result->subjects()->create([
                    'subject' =>
                        $subject['subject'],

                    'full_marks' =>
                        $subject['full_marks'],

                    'obtained_marks' =>
                        $subject['obtained_marks'],

                    'grade' =>
                        $subject['grade'] ?? null,
                ]);
            }
        });

        $result->load([
            'student',
            'subjects',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Result updated successfully',
            'data' => $result,
        ]);
    }


    // =========================
    // Delete Result
    // =========================
    public function destroy(Result $result)
    {
        $result->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Result deleted successfully',
        ]);
    }


    // =========================
    // Public Student Result Search
    // =========================
    public function search(Request $request)
    {
        $validated = $request->validate([
            'class' =>
                'required|string',

            'roll' =>
                'required|string',

            'exam' =>
                'required|string',
        ]);

        $result = Result::with([
            'student',
            'subjects',
        ])
            ->where(
                'exam_name',
                $validated['exam']
            )
            ->whereHas(
                'student',
                function ($query) use ($validated) {
                    $query
                        ->where(
                            'class',
                            $validated['class']
                        )
                        ->where(
                            'roll',
                            $validated['roll']
                        )
                        ->where(
                            'status',
                            true
                        );
                }
            )
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' =>
                    'কোনো ফলাফল পাওয়া যায়নি।',
            ], 404);
        }

        // StudentPortal.jsx expected format
        return response()->json([
            'success' => true,

            'data' => [
                'student' =>
                    $result->student->name,

                'className' =>
                    $result->student->class,

                'roll' =>
                    $result->student->roll,

                'exam' =>
                    $result->exam_name,

                'gpa' =>
                    $result->gpa,

                'passed' =>
                    $result->passed,

                'fullTotal' =>
                    $result->full_marks,

                'total' =>
                    $result->total_marks,

                'rows' =>
                    $result->subjects->map(
                        function ($subject) {
                            return [
                                'subject' =>
                                    $subject->subject,

                                'full' =>
                                    $subject->full_marks,

                                'obtained' =>
                                    $subject->obtained_marks,

                                'grade' =>
                                    $subject->grade,
                            ];
                        }
                    ),
            ],
        ]);
    }
}