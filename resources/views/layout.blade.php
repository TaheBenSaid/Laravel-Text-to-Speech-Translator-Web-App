<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Text-to-Speech Translator</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                🎤 Text-to-Speech Translator
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="darkModeToggle" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <span id="darkModeIcon">🌙</span>
                        </button>
                        <button id="historyBtn" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            📚 History
                        </button>
                        <button id="cleanupBtn" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            🧹 Cleanup
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 mt-12">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>Built with Laravel & Tailwind CSS | 
                       <a href="https://github.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View on GitHub</a>
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">Processing...</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Translating and generating speech audio</p>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    @stack('scripts')
    
    <script>
        // Dark mode functionality
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const html = document.documentElement;
            
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
        });
    </script>
</body>
</html>
