<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Services\TranslationService;
use App\Services\TTSService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class TranslateController extends Controller
{
    protected TranslationService $translationService;
    protected TTSService $ttsService;

    public function __construct(TranslationService $translationService, TTSService $ttsService)
    {
        $this->translationService = $translationService;
        $this->ttsService = $ttsService;
    }

    public function index(): View
    {
        $languages = $this->translationService->getAvailableLanguages();
        $recentTranslations = Translation::latest()->take(5)->get();
        
        return view('home', compact('languages', 'recentTranslations'));
    }

    public function translate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:1000',
            'target_language' => 'required|string|in:Arabic,Portuguese,Spanish,Hindi,Chinese',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $text = $request->input('text');
            $targetLanguage = $request->input('target_language');

            $translatedText = $this->translationService->translate($text, $targetLanguage);
            $audioUrl = $this->ttsService->generateSpeech($translatedText, $targetLanguage);
            $translation = Translation::create([
                'input_text' => $text,
                'translated_text' => $translatedText,
                'target_language' => $targetLanguage,
                'audio_url' => $audioUrl,
            ]);

            return response()->json([
                'success' => true,
                'translation' => [
                    'id' => $translation->id,
                    'input_text' => $translation->input_text,
                    'translated_text' => $translation->translated_text,
                    'target_language' => $translation->target_language,
                    'audio_url' => $translation->audio_url,
                    'created_at' => $translation->created_at->format('M j, Y g:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Translation failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function history(): JsonResponse
    {
        $translations = Translation::latest()->take(20)->get()->map(function ($translation) {
            return [
                'id' => $translation->id,
                'input_text' => $translation->input_text,
                'translated_text' => $translation->translated_text,
                'target_language' => $translation->target_language,
                'audio_url' => $translation->audio_url,
                'created_at' => $translation->created_at->format('M j, Y g:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'translations' => $translations
        ]);
    }

    public function downloadAudio(int $id)
    {
        $translation = Translation::findOrFail($id);
        
        if (!$translation->audio_url) {
            abort(404, 'Audio file not found');
        }

        $audioPath = str_replace('/storage/', '', $translation->audio_url);
        $fullPath = storage_path('app/public/' . $audioPath);

        if (!file_exists($fullPath)) {
            abort(404, 'Audio file not found on disk');
        }

        return response()->download($fullPath, 'translation_' . $id . '.mp3');
    }

    public function cleanup(): JsonResponse
    {
        try {
            $this->ttsService->cleanupOldAudioFiles();
            Translation::truncate();
            
            return response()->json([
                'success' => true,
                'message' => 'Cleanup completed: old audio files and translation history cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
