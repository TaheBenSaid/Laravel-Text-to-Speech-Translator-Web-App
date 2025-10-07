<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TTSService
{
    /**
     * Generate speech audio from text
     */
    public function generateSpeech(string $text, string $language = 'en'): ?string
    {
        $apiKey = config('services.openai.api_key');
        
        if ($apiKey) {
            try {
                return $this->generateWithOpenAI($text, $language, $apiKey);
            } catch (\Exception $e) {
                Log::warning('TTS API failed', ['error' => $e->getMessage()]);
            }
        }

        // Return a special identifier for browser-based TTS
        return 'browser-tts:' . base64_encode(json_encode([
            'text' => $text,
            'language' => $language,
            'lang_code' => $this->getLanguageCode($language)
        ]));
    }

    private function generateWithOpenAI(string $text, string $language, string $apiKey): ?string
    {
        $voice = $this->getVoiceForLanguage($language);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/audio/speech', [
            'model' => 'tts-1',
            'input' => $text,
            'voice' => $voice,
            'response_format' => 'mp3'
        ]);

        if ($response->successful()) {
            $audioContent = $response->body();
            $filename = 'audio_' . uniqid() . '.mp3';
            $path = 'audio/' . $filename;
            
            Storage::disk('public')->put($path, $audioContent);
            
            return Storage::url($path);
        }

        return null;
    }

    private function generateMockAudio(string $text, string $language): string
    {
        $filename = 'mock_audio_' . uniqid() . '.html';
        $path = 'audio/' . $filename;
        
        $mockContent = '<!DOCTYPE html>
<html>
<head>
    <title>🎤 Audio: ' . htmlspecialchars($text) . '</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            padding: 20px; 
            text-align: center; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .audio-container { 
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 20px; 
            margin: 20px auto; 
            max-width: 500px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .play-button { 
            background: #4CAF50; 
            color: white; 
            padding: 15px 30px; 
            border: none; 
            border-radius: 50px; 
            cursor: pointer; 
            font-size: 18px; 
            margin: 20px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        .play-button:hover { 
            background: #45a049; 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }
        .text-display {
            font-size: 18px;
            line-height: 1.6;
            margin: 20px 0;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .language-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin: 10px 0;
        }
        .wave-animation {
            display: inline-block;
            margin: 0 2px;
            animation: wave 1.5s ease-in-out infinite;
        }
        .wave-animation:nth-child(2) { animation-delay: 0.1s; }
        .wave-animation:nth-child(3) { animation-delay: 0.2s; }
        .wave-animation:nth-child(4) { animation-delay: 0.3s; }
        .wave-animation:nth-child(5) { animation-delay: 0.4s; }
        @keyframes wave {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.5); }
        }
    </style>
</head>
<body>
    <div class="audio-container">
        <h2>🎤 Text-to-Speech Audio</h2>
        <div class="language-badge">' . htmlspecialchars($language) . '</div>
        
        <div class="text-display">
            <strong>Original:</strong><br>
            <em>' . htmlspecialchars($text) . '</em>
        </div>
        
        <div class="wave-animation">🎵</div>
        <div class="wave-animation">🎵</div>
        <div class="wave-animation">🎵</div>
        <div class="wave-animation">🎵</div>
        <div class="wave-animation">🎵</div>
        
        <br><br>
        
        <button class="play-button" onclick="playMockAudio()">
            ▶️ Play Audio
        </button>
        
        <p style="font-size: 14px; opacity: 0.8; margin-top: 20px;">
            <em>Mock audio simulation • Generated: ' . now()->format('M j, Y g:i A') . '</em>
        </p>
    </div>
    
    <script>
        function playMockAudio() {
            const button = document.querySelector(".play-button");
            const originalText = button.textContent;
            
            // Simulate audio playback
            button.textContent = "🔊 Playing...";
            button.disabled = true;
            
            // Create a simple text-to-speech simulation
            const utterance = new SpeechSynthesisUtterance("' . addslashes($text) . '");
            utterance.lang = "' . $this->getLanguageCode($language) . '";
            utterance.rate = 0.8;
            utterance.pitch = 1;
            
            if (speechSynthesis && speechSynthesis.speak) {
                speechSynthesis.speak(utterance);
                
                utterance.onend = function() {
                    button.textContent = originalText;
                    button.disabled = false;
                };
            } else {
                setTimeout(() => {
                    alert("🎤 Mock Audio: ' . addslashes($text) . '");
                    button.textContent = originalText;
                    button.disabled = false;
                }, 1000);
            }
        }
        
    </script>
</body>
</html>';
        
        Storage::disk('public')->put($path, $mockContent);
        
        return Storage::url($path);
    }

    private function getVoiceForLanguage(string $language): string
    {
        $voices = [
            'ar' => 'alloy',
            'pt' => 'nova',
            'es' => 'shimmer',
            'hi' => 'echo',
            'zh' => 'fable',
        ];

        $langCode = $this->getLanguageCode($language);
        return $voices[$langCode] ?? 'alloy';
    }

    private function getLanguageCode(string $language): string
    {
        $languageCodes = [
            'Arabic' => 'ar-SA',
            'Portuguese' => 'pt-BR',
            'Spanish' => 'es-ES',
            'Hindi' => 'hi-IN',
            'Chinese' => 'zh-CN',
        ];

        return $languageCodes[$language] ?? 'en-US';
    }

    public function cleanupOldAudioFiles(int $hoursOld = 24): void
    {
        $files = Storage::disk('public')->files('audio');
        $cutoff = now()->subHours($hoursOld);

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if ($lastModified < $cutoff->timestamp) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
