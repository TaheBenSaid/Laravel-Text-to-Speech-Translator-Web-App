@extends('layout')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Translate & Speak Any Text
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Enter your English text, select a target language, and get instant translation with speech audio.
        </p>
    </div>

    <!-- Main Translation Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <form id="translationForm" class="space-y-6">
                @csrf
                
                <!-- Text Input -->
                <div>
                    <label for="inputText" class="block text-sm font-medium text-gray-700 mb-2">
                        Enter English Text
                    </label>
                    <textarea 
                        id="inputText" 
                        name="text" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Type your English text here..."
                        maxlength="1000"
                        required
                    ></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500">Maximum 1000 characters</span>
                        <span id="charCount" class="text-xs text-gray-500">0/1000</span>
                    </div>
                </div>

                <!-- Language Selection -->
                <div>
                    <label for="targetLanguage" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Target Language
                    </label>
                    <select 
                        id="targetLanguage" 
                        name="target_language" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="">Choose a language...</option>
                        @foreach($languages as $code => $name)
                            <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                        @endforeach
                    </select>
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
            <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Translation Results</h3>
                
                <div class="space-y-4">
                    <!-- Original Text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Original Text (English)</label>
                        <div class="p-3 bg-gray-50 rounded-md border">
                            <p id="originalText" class="text-gray-900"></p>
                        </div>
                    </div>

                    <!-- Translated Text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Translated Text (<span id="targetLanguageLabel"></span>)
                        </label>
                        <div class="p-3 bg-blue-50 rounded-md border border-blue-200">
                            <p id="translatedText" class="text-gray-900"></p>
                        </div>
                    </div>

                    <!-- Audio Player -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Audio Playback</label>
                        <div class="flex items-center space-x-4">
                            <audio id="audioPlayer" controls class="flex-1">
                                Your browser does not support the audio element.
                            </audio>
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
        @if($recentTranslations->count() > 0)
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Translations</h3>
            <div class="space-y-3">
                @foreach($recentTranslations as $translation)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">{{ $translation->target_language }}</span> • 
                                {{ $translation->created_at->format('M j, Y g:i A') }}
                            </p>
                            <p class="text-gray-900 mb-2">{{ Str::limit($translation->input_text, 100) }}</p>
                            <p class="text-blue-600">{{ Str::limit($translation->translated_text, 100) }}</p>
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
                            <a 
                                href="{{ route('download.audio', $translation->id) }}"
                                class="p-2 text-green-600 hover:bg-green-100 rounded-full"
                                title="Download Audio"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- History Modal -->
<div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Translation History</h3>
                <button id="closeHistoryModal" class="text-gray-400 hover:text-gray-600">
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
    if (translation.audio_url) {
        audioPlayer.src = translation.audio_url;
        audioPlayer.style.display = 'block';
        document.getElementById('downloadBtn').style.display = 'inline-flex';
        currentTranslationId = translation.id;
    } else {
        audioPlayer.style.display = 'none';
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
                                <a href="/download/${translation.id}" class="p-2 text-green-600 hover:bg-green-100 rounded-full" title="Download Audio">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
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
    const audio = new Audio(audioUrl);
    audio.play().catch(error => {
        showToast('Error playing audio', 'error');
    });
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.toggle('hidden', !show);
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
</script>
@endpush
