import './bootstrap';

window.audioUtils = {
    play: function(url) {
        const audio = new Audio(url);
        return audio.play().catch(error => {
            console.error('Audio playback failed:', error);
            throw error;
        });
    },
    
    download: function(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename || 'audio.mp3';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
    
    console.log('App initialized');
});
