@extends('layout')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Translate & Speak Any Text
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Enter your English text, select a target language, and get instant translation with speech audio.
        </p>
    </div>

    <!-- Main Translation Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-8 transition-colors duration-300">
            <form id="translationForm" class="space-y-6">
                @csrf
                
                <!-- Text Input -->
                <div>
                    <label for="inputText" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Enter English Text
                    </label>
                    <textarea 
                        id="inputText" 
                        name="text" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Type your English text here..."
                        maxlength="1000"
                        required
                    ></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Maximum 1000 characters</span>
                        <span id="charCount" class="text-xs text-gray-500 dark:text-gray-400">0/1000</span>
                    </div>
                </div>

                <!-- Language Selection -->
                <div>
                    <label for="targetLanguage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Target Language
                    </label>
                    <select 
                        id="targetLanguage" 
                        name="target_language" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="">Choose a language...</option>
                        @foreach($languages as $code => $name)
                            <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Voice Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="voiceSpeed" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Speed: <span id="speedValue">0.8</span>
                        </label>
                        <input 
                            type="range" 
                            id="voiceSpeed" 
                            name="voice_speed" 
                            min="0.5" 
                            max="1.5" 
                            step="0.1" 
                            value="0.8"
                            class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer"
                        >
                    </div>
                    <div>
                        <label for="voicePitch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pitch: <span id="pitchValue">1.0</span>
                        </label>
                        <input 
                            type="range" 
                            id="voicePitch" 
                            name="voice_pitch" 
                            min="0.5" 
                            max="2.0" 
                            step="0.1" 
                            value="1.0"
                            class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer"
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button 
                        type="submit" 
                        id="translateBtn"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Translate & Speak
                        </span>
                    </button>
                    
                    <button 
                        type="button" 
                        id="clearBtn"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="hidden">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-8 transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Translation Results</h3>
                
                <div class="space-y-4">
                    <!-- Original Text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Original Text (English)</label>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md border dark:border-gray-600">
                            <p id="originalText" class="text-gray-900 dark:text-white"></p>
                        </div>
                    </div>

                    <!-- Translated Text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Translated Text (<span id="targetLanguageLabel"></span>)
                        </label>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-700">
                            <p id="translatedText" class="text-gray-900 dark:text-white"></p>
                        </div>
                    </div>

                    <!-- Audio Player -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Audio Playback</label>
                        <div class="flex items-center space-x-4">
                            <audio id="audioPlayer" controls class="flex-1">
                                Your browser does not support the audio element.
                            </audio>
                            <div id="audioContainer" class="flex-1"></div>
                            <button 
                                id="downloadBtn" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            >
                                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Translations -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Translations</h3>
            <div id="recentTranslations" class="space-y-3">
                @if($recentTranslations->count() > 0)
                @foreach($recentTranslations as $translation)
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <span class="font-medium">{{ $translation->target_language }}</span> • 
                                {{ $translation->created_at->format('M j, Y g:i A') }}
                            </p>
                            <p class="text-gray-900 dark:text-white mb-2">{{ Str::limit($translation->input_text, 100) }}</p>
                            <p class="text-blue-600 dark:text-blue-400">{{ Str::limit($translation->translated_text, 100) }}</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            @if($translation->audio_url)
                            <button 
                                onclick="playAudio('{{ $translation->audio_url }}')"
                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-full"
                                title="Play Audio"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            @if($translation->downloadable_audio_url)
                            <a 
                                href="{{ route('download.audio', $translation->id) }}?type=json"
                                class="p-2 text-orange-600 hover:bg-orange-100 rounded-full"
                                title="Download JSON"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            <a 
                                href="{{ route('download.audio', $translation->id) }}?type=mp3"
                                class="p-2 text-green-600 hover:bg-green-100 rounded-full"
                                title="Download MP3"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent translations yet. Start translating to see them here!</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700 transition-colors duration-300">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Translation History</h3>
                <button id="closeHistoryModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="historyContent" class="max-h-96 overflow-y-auto">
                <!-- History content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentTranslationId = null;

document.getElementById('inputText').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('charCount').textContent = count + '/1000';
});

document.getElementById('translationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const text = formData.get('text').trim();
    const targetLanguage = formData.get('target_language');
    
    if (!text || !targetLanguage) {
        showToast('Please enter text and select a language', 'error');
        return;
    }
    
    showLoading(true);
    document.getElementById('translateBtn').disabled = true;
    
    try {
        const response = await fetch('/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: text,
                target_language: targetLanguage
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayResults(data.translation);
            addToRecentTranslations(data.translation);
            showToast('Translation completed successfully!', 'success');
        } else {
            showToast(data.message || 'Translation failed', 'error');
        }
    } catch (error) {
        showToast('Network error. Please try again.', 'error');
    } finally {
        showLoading(false);
        document.getElementById('translateBtn').disabled = false;
    }
});

function displayResults(translation) {
    document.getElementById('originalText').textContent = translation.input_text;
    document.getElementById('translatedText').textContent = translation.translated_text;
    document.getElementById('targetLanguageLabel').textContent = translation.target_language;
    
    const audioPlayer = document.getElementById('audioPlayer');
    const audioContainer = document.getElementById('audioContainer');
    
    if (translation.audio_url) {
        if (translation.audio_url.startsWith('browser-tts:')) {
            // Handle browser-based TTS
            const ttsData = JSON.parse(atob(translation.audio_url.replace('browser-tts:', '')));
            setupBrowserTTS(ttsData, audioContainer);
            audioPlayer.style.display = 'none';
            document.getElementById('downloadBtn').style.display = 'none';
        } else {
            // Handle regular audio files
            audioPlayer.src = translation.audio_url;
            audioPlayer.style.display = 'block';
            audioContainer.innerHTML = '';
            document.getElementById('downloadBtn').style.display = 'inline-flex';
            currentTranslationId = translation.id;
        }
    } else {
        audioPlayer.style.display = 'none';
        audioContainer.innerHTML = '';
        document.getElementById('downloadBtn').style.display = 'none';
    }
    
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth' });
}

document.getElementById('clearBtn').addEventListener('click', function() {
    document.getElementById('translationForm').reset();
    document.getElementById('charCount').textContent = '0/1000';
    document.getElementById('resultsSection').classList.add('hidden');
    currentTranslationId = null;
});

document.getElementById('downloadBtn').addEventListener('click', function() {
    if (currentTranslationId) {
        window.open(`/download/${currentTranslationId}`, '_blank');
    }
});

document.getElementById('historyBtn').addEventListener('click', async function() {
    document.getElementById('historyModal').classList.remove('hidden');
    await loadHistory();
});

document.getElementById('closeHistoryModal').addEventListener('click', function() {
    document.getElementById('historyModal').classList.add('hidden');
});

function addToRecentTranslations(translation) {
    const recentTranslationsContainer = document.getElementById('recentTranslations');
    if (!recentTranslationsContainer) return;
    
    // Remove the "no translations" message if it exists
    const noTranslationsMsg = recentTranslationsContainer.querySelector('p');
    if (noTranslationsMsg && noTranslationsMsg.textContent.includes('No recent translations')) {
        noTranslationsMsg.remove();
    }
    
    // Create the new translation element
    const newTranslationElement = document.createElement('div');
    newTranslationElement.className = 'border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors';
    newTranslationElement.innerHTML = `
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">${translation.target_language}</span> • 
                    ${translation.created_at}
                </p>
                <p class="text-gray-900 mb-2">${translation.input_text}</p>
                <p class="text-blue-600">${translation.translated_text}</p>
            </div>
            <div class="flex space-x-2 ml-4">
                ${translation.audio_url ? `
                    <button onclick="playAudio('${translation.audio_url}')" class="p-2 text-blue-600 hover:bg-blue-100 rounded-full" title="Play Audio">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    ${translation.downloadable_audio_url ? `
                        <a href="/download/${translation.id}?type=json" class="p-2 text-orange-600 hover:bg-orange-100 rounded-full" title="Download JSON">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                        <a href="/download/${translation.id}?type=mp3" class="p-2 text-green-600 hover:bg-green-100 rounded-full" title="Download MP3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    ` : ''}
                ` : ''}
            </div>
        </div>
    `;
    
    // Add to the top of the recent translations list
    const firstChild = recentTranslationsContainer.firstElementChild;
    if (firstChild) {
        recentTranslationsContainer.insertBefore(newTranslationElement, firstChild);
    } else {
        recentTranslationsContainer.appendChild(newTranslationElement);
    }
    
    // Limit to 5 recent translations
    const translations = recentTranslationsContainer.children;
    if (translations.length > 5) {
        recentTranslationsContainer.removeChild(translations[translations.length - 1]);
    }
}

async function loadHistory() {
    try {
        const response = await fetch('/history', {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        
        const historyContent = document.getElementById('historyContent');
        
        if (data.success && data.translations.length > 0) {
            historyContent.innerHTML = data.translations.map(translation => `
                <div class="border border-gray-200 rounded-lg p-4 mb-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">${translation.target_language}</span> • 
                                ${translation.created_at}
                            </p>
                            <p class="text-gray-900 mb-2">${translation.input_text}</p>
                            <p class="text-blue-600">${translation.translated_text}</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            ${translation.audio_url ? `
                                <button onclick="playAudio('${translation.audio_url}')" class="p-2 text-blue-600 hover:bg-blue-100 rounded-full" title="Play Audio">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                ${translation.downloadable_audio_url ? `
                                    <a href="/download/${translation.id}?type=json" class="p-2 text-orange-600 hover:bg-orange-100 rounded-full" title="Download JSON">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="/download/${translation.id}?type=mp3" class="p-2 text-green-600 hover:bg-green-100 rounded-full" title="Download MP3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                ` : ''}
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            historyContent.innerHTML = '<p class="text-gray-500 text-center py-8">No translation history found.</p>';
        }
    } catch (error) {
        document.getElementById('historyContent').innerHTML = '<p class="text-red-500 text-center py-8">Error loading history.</p>';
    }
}

document.getElementById('cleanupBtn').addEventListener('click', async function() {
    if (confirm('Are you sure you want to clean up old audio files?')) {
        try {
            const response = await fetch('/cleanup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            showToast(data.message, data.success ? 'success' : 'error');
        } catch (error) {
            showToast('Cleanup failed', 'error');
        }
    }
});

function playAudio(audioUrl) {
    if (audioUrl.startsWith('browser-tts:')) {
        // Handle browser TTS
        const ttsData = JSON.parse(atob(audioUrl.replace('browser-tts:', '')));
        
        if (!('speechSynthesis' in window)) {
            showToast('Text-to-speech not supported in this browser', 'error');
            return;
        }
        
        const utterance = new SpeechSynthesisUtterance(ttsData.text);
        utterance.lang = ttsData.lang_code;
        utterance.rate = parseFloat(document.getElementById('voiceSpeed')?.value || 0.8);
        utterance.pitch = parseFloat(document.getElementById('voicePitch')?.value || 1.0);
        utterance.volume = 1;
        
        utterance.onerror = (event) => {
            showToast(`Error playing ${ttsData.language} audio: ${event.error}`, 'error');
        };
        
        speechSynthesis.speak(utterance);
    } else {
        // Handle regular audio files
        const audio = new Audio(audioUrl);
        audio.play().catch(error => {
            showToast('Error playing audio', 'error');
        });
    }
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.toggle('hidden', !show);
}

function setupBrowserTTS(ttsData, container) {
    container.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Browser Text-to-Speech</p>
                        <p class="text-xs text-gray-500">Language: ${ttsData.language}</p>
                    </div>
                </div>
                <button id="playTTSBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Play</span>
                </button>
            </div>
        </div>
    `;
    
    const playBtn = container.querySelector('#playTTSBtn');
    playBtn.addEventListener('click', () => {
        playBrowserTTS(ttsData, playBtn);
    });
}

function playBrowserTTS(ttsData, button) {
    if (!('speechSynthesis' in window)) {
        showToast('Text-to-speech not supported in this browser', 'error');
        return;
    }
    
    const originalText = button.innerHTML;
    button.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Loading voices...</span>
    `;
    button.disabled = true;
    
    // Wait for voices to load
    const speakWithVoice = () => {
        button.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Playing...</span>
        `;
        
        const utterance = new SpeechSynthesisUtterance(ttsData.text);
        
        // Try the specific locale first, then fallback to base language
        const langCode = ttsData.lang_code;
        const baseLang = langCode.split('-')[0];
        
        utterance.lang = langCode;
        utterance.rate = parseFloat(document.getElementById('voiceSpeed').value);
        utterance.pitch = parseFloat(document.getElementById('voicePitch').value);
        utterance.volume = 1;
        
        // Use browser's default voice selection
        
        // Debug: Log available voices for Portuguese
        if (ttsData.language === 'Portuguese') {
            const voices = speechSynthesis.getVoices();
            const portugueseVoices = voices.filter(voice => voice.lang.startsWith('pt'));
            console.log('Available Portuguese voices:', portugueseVoices);
            console.log('Using language code:', langCode);
        }
        
        utterance.onend = () => {
            button.innerHTML = originalText;
            button.disabled = false;
        };
        
        utterance.onerror = (event) => {
            console.log('TTS Error:', event.error, 'for language:', ttsData.language, 'code:', langCode);
            
            // If specific locale fails, try base language
            if (utterance.lang === langCode && baseLang !== langCode) {
                console.log('Trying fallback to base language:', baseLang);
                utterance.lang = baseLang;
                speechSynthesis.speak(utterance);
                return;
            }
            
            button.innerHTML = originalText;
            button.disabled = false;
            showToast(`Error playing ${ttsData.language} audio: ${event.error}`, 'error');
        };
        
        speechSynthesis.speak(utterance);
    };
    
    // Check if voices are loaded, if not wait for them
    if (speechSynthesis.getVoices().length === 0) {
        speechSynthesis.addEventListener('voiceschanged', () => {
            speakWithVoice();
        }, { once: true });
    } else {
        speakWithVoice();
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between min-w-80`;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Voice controls and dark mode functionality
document.addEventListener('DOMContentLoaded', function() {
    // Voice controls
    const speedSlider = document.getElementById('voiceSpeed');
    const pitchSlider = document.getElementById('voicePitch');
    const speedValue = document.getElementById('speedValue');
    const pitchValue = document.getElementById('pitchValue');
    
    if (speedSlider && speedValue) {
        speedSlider.addEventListener('input', function() {
            speedValue.textContent = this.value;
        });
    }
    
    if (pitchSlider && pitchValue) {
        pitchSlider.addEventListener('input', function() {
            pitchValue.textContent = this.value;
        });
    }
    
    // Dark mode functionality
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const html = document.documentElement;
    
    if (darkModeToggle && darkModeIcon) {
        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            darkModeIcon.textContent = '☀️';
        }
        
        darkModeToggle.addEventListener('click', function() {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            darkModeIcon.textContent = isDark ? '☀️' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
});
</script>
@endpush
