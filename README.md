# 🎤 Text-to-Speech Translator

A Laravel web application that translates English text to multiple languages and generates speech audio.

## Features

- Translate English text to Arabic, French, Spanish, Hindi, and Chinese
- Generate speech audio for translated text
- Play audio directly in the browser
- Download audio files
- View translation history
- Works without API keys (uses free services)

## Quick Start

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```

3. **Build assets**
   ```bash
   npm run build
   ```

4. **Start server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to use the app.

## Configuration

The app works without API keys using free services. For better quality, add these to your `.env` file:

```env
OPENAI_API_KEY=your_openai_api_key_here
GOOGLE_TRANSLATE_API_KEY=your_google_translate_key_here
```

## Usage

1. Enter English text
2. Select target language
3. Click translate
4. Play or download the audio

## Deployment

### Render
Connect your GitHub repo to Render. The `render.yaml` file handles the setup.

### Docker
```bash
docker build -t text-to-speech-translator .
docker run -p 80:80 text-to-speech-translator
```

## API Endpoints

- `GET /` - Main interface
- `POST /translate` - Translate text
- `GET /history` - Get history
- `GET /download/{id}` - Download audio

## License

MIT License