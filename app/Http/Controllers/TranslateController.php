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
            
            // Generate downloadable audio with voice options
            $voiceOptions = [
                'voice_speed' => $request->input('voice_speed', 0.8),
                'voice_pitch' => $request->input('voice_pitch', 1.0),
            ];
            $downloadableAudioUrl = $this->ttsService->generateDownloadableAudio($translatedText, $targetLanguage, $voiceOptions);
            
            $translation = Translation::create([
                'input_text' => $text,
                'translated_text' => $translatedText,
                'target_language' => $targetLanguage,
                'audio_url' => $audioUrl,
                'downloadable_audio_url' => $downloadableAudioUrl,
            ]);

            return response()->json([
                'success' => true,
                'translation' => [
                    'id' => $translation->id,
                    'input_text' => $translation->input_text,
                    'translated_text' => $translation->translated_text,
                    'target_language' => $translation->target_language,
                    'audio_url' => $translation->audio_url,
                    'downloadable_audio_url' => $translation->downloadable_audio_url,
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
                'downloadable_audio_url' => $translation->downloadable_audio_url,
                'created_at' => $translation->created_at->format('M j, Y g:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'translations' => $translations
        ]);
    }

    public function downloadAudio(int $id, Request $request)
    {
        $translation = Translation::findOrFail($id);
        $downloadType = $request->query('type', 'json'); // Default to JSON
        
        if ($downloadType === 'json') {
            // Download JSON file (browser TTS data)
            if (!$translation->downloadable_audio_url) {
                abort(404, 'JSON file not found');
            }
            
            $audioPath = str_replace('/storage/', '', $translation->downloadable_audio_url);
            $fullPath = storage_path('app/public/' . $audioPath);
            
            if (file_exists($fullPath)) {
                return response()->download($fullPath, 'translation_' . $id . '_data.json');
            }
            
            abort(404, 'JSON file not found on disk');
        } 
        elseif ($downloadType === 'mp3') {
            // Download MP3 file (actual audio)
            if (!$translation->audio_url) {
                abort(404, 'MP3 file not found');
            }
            
            // For browser TTS, we can't generate actual MP3, so return a message
            if (str_contains($translation->audio_url, 'browser-tts:')) {
                $message = "MP3 download not available for browser TTS. Use the JSON download to get the text data, or add an OpenAI API key to generate actual MP3 files.";
                return response($message, 200)
                    ->header('Content-Type', 'text/plain')
                    ->header('Content-Disposition', 'attachment; filename="translation_' . $id . '_mp3_info.txt"');
            }
            
            // Handle actual MP3 files (from OpenAI TTS)
            $audioPath = str_replace('/storage/', '', $translation->audio_url);
            $fullPath = storage_path('app/public/' . $audioPath);

            if (!file_exists($fullPath)) {
                abort(404, 'MP3 file not found on disk');
            }

            return response()->download($fullPath, 'translation_' . $id . '.mp3');
        }
        
        abort(400, 'Invalid download type. Use ?type=json or ?type=mp3');
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
