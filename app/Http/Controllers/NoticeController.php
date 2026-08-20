<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::orderBy('publish_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notices,
        ]);
    }

    public function show($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $notice,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $notice = Notice::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'publish_date' => $validated['publish_date'] ?? now()->toDateString(),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice created successfully',
            'data' => $notice,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $notice->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'publish_date' => $validated['publish_date'] ?? $notice->publish_date,
            'is_active' => $validated['is_active'] ?? $notice->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice updated successfully',
            'data' => $notice,
        ]);
    }

    public function destroy($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
            ], 404);
        }

        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully',
        ]);
    }
}