<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Result;
use App\Models\Routine;
use App\Models\Notice;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        return response()->json([
            'success' => true,

            'data' => [
                'teachers' => Teacher::count(),
                'students' => Student::count(),
                'results' => Result::count(),
                'routines' => Routine::count(),
                'notices' => Notice::count(),
            ],
        ]);
    }
}