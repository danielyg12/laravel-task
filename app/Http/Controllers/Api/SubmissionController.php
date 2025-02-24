<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|min:2|max:10',
            'email' => ['required', 'email', 'regex:/^[^@]+@(?!gmail\.com$)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'phone' => ['required', 'regex:/^\+\d{1,3}\d{9,}$/'],
            'message' => 'required|string|min:10',
            'street' => 'nullable|string',
            'state' => 'nullable|string',
            'zip' => 'nullable|string',
            'country' => 'nullable|string',
            'image' => 'required|file|mimes:jpg,jpeg|max:2048', // Only JPG
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Store image & PDF file in 'files' disk
        $imagePath = $request->file('image')->store('images', 'files');
        $filePath = $request->file('file')->store('files', 'files');

        $submission = Submission::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'message' => $validatedData['message'],
            'street' => $validatedData['street'] ?? null,
            'state' => $validatedData['state'] ?? null,
            'zip' => $validatedData['zip'] ?? null,
            'country' => $validatedData['country'] ?? null,
            'image' => $imagePath,
            'file' => $filePath,
        ]);

        return response()->json([
            'message' => 'Submission successful!',
            'data' => $submission,
        ], 201);
    }
}
