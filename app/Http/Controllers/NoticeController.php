<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    // ==========================================
    // ALL NOTICES
    // ==========================================
    public function index()
    {
        $notices = Notice::orderBy('publish_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($notice) {
                return $this->formatNotice($notice);
            });

        return response()->json([
            'success' => true,
            'data' => $notices,
        ]);
    }

    // ==========================================
    // SINGLE NOTICE
    // ==========================================
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
            'data' => $this->formatNotice($notice),
        ]);
    }

    // ==========================================
    // CREATE NOTICE
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:পরীক্ষা,ভর্তি,সাধারণ,অন্যান্য',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request
                ->file('file')
                ->store('notices', 'public');
        }

        $notice = Notice::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'file' => $filePath,
            'publish_date' =>
                $validated['publish_date']
                ?? now()->toDateString(),
            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice created successfully',
            'data' => $this->formatNotice($notice),
        ], 201);
    }

    // ==========================================
    // UPDATE NOTICE
    // ==========================================
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
            'category' => 'required|in:পরীক্ষা,ভর্তি,সাধারণ,অন্যান্য',
            'publish_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $filePath = $notice->file;

        if ($request->hasFile('file')) {
            if (
                $notice->file &&
                Storage::disk('public')->exists($notice->file)
            ) {
                Storage::disk('public')->delete($notice->file);
            }

            $filePath = $request
                ->file('file')
                ->store('notices', 'public');
        }

        $notice->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'file' => $filePath,
            'publish_date' =>
                $validated['publish_date']
                ?? $notice->publish_date,
            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice updated successfully',
            'data' => $this->formatNotice(
                $notice->fresh()
            ),
        ]);
    }

    // ==========================================
    // DELETE NOTICE
    // ==========================================
    public function destroy($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
            ], 404);
        }

        if (
            $notice->file &&
            Storage::disk('public')->exists($notice->file)
        ) {
            Storage::disk('public')->delete($notice->file);
        }

        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully',
        ]);
    }

    // ==========================================
    // DOWNLOAD NOTICE FILE
    // ==========================================
    public function download($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
            ], 404);
        }

        if (
            !$notice->file ||
            !Storage::disk('public')->exists($notice->file)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Notice file not found',
            ], 404);
        }

        $extension = pathinfo(
            $notice->file,
            PATHINFO_EXTENSION
        );

        $fileName =
            'notice-' .
            $notice->id .
            '.' .
            $extension;

        return Storage::disk('public')->download(
            $notice->file,
            $fileName
        );
    }

    // ==========================================
    // FORMAT NOTICE RESPONSE
    // ==========================================
    private function formatNotice($notice)
    {
        return [
            'id' => $notice->id,

            'title' => $notice->title,

            'description' => $notice->description,

            'category' => $notice->category,

            'file' => $notice->file,

            'file_url' => $notice->file
                ? asset('storage/' . $notice->file)
                : null,

            'download_url' => $notice->file
                ? url('/api/notices/' . $notice->id . '/download')
                : null,

            'publish_date' => $notice->publish_date,

            'is_active' => $notice->is_active,

            'created_at' => $notice->created_at,

            'updated_at' => $notice->updated_at,
        ];
    }
}