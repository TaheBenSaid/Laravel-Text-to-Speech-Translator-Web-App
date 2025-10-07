# Deployment Guide - Text-to-Speech Translator

## Quick Deploy to Render (Recommended - Free)

### Step 1: Push to GitHub
```bash
git add .
git commit -m "Ready for deployment"
git push origin main
```

### Step 2: Deploy to Render
1. Go to [render.com](https://render.com) and sign up/login
2. Click "New +" → "Web Service"
3. Connect your GitHub repository
4. Select your repository: `text-to-speech-translator`
5. Configure the service:
   - **Name**: `text-to-speech-translator`
   - **Environment**: `PHP`
   - **Build Command**: 
     ```bash
     composer install --no-dev --optimize-autoloader
     php artisan key:generate --force
     php artisan migrate --force
     php artisan storage:link
     npm ci
     npm run build
     ```
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Plan**: Free

6. Add Environment Variables:
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `DB_CONNECTION` = `sqlite`
   - `SESSION_DRIVER` = `database`
   - `CACHE_STORE` = `database`
   - `QUEUE_CONNECTION` = `database`
   - `LOG_LEVEL` = `error`

7. Click "Create Web Service"

### Step 3: Access Your App
- Render will provide a URL like: `https://text-to-speech-translator.onrender.com`
- The app will be fully functional with:
  ✅ Real translations (MyMemory API)
  ✅ Text-to-speech (Browser TTS)
  ✅ Dark mode toggle
  ✅ Download functionality
  ✅ Translation history
  ✅ Responsive design

## Alternative: Deploy to Vercel

### Step 1: Install Vercel CLI
```bash
npm i -g vercel
```

### Step 2: Deploy
```bash
vercel --prod
```

## Alternative: Deploy with Docker

### Step 1: Build Docker Image
```bash
docker build -t text-to-speech-translator .
```

### Step 2: Run Container
```bash
docker run -p 8000:80 text-to-speech-translator
```

## Features Available After Deployment

- **Translation**: English to Arabic, Portuguese, Spanish, Hindi, Chinese
- **Text-to-Speech**: Browser-based TTS with speed/pitch controls
- **Dark Mode**: Toggle between light and dark themes
- **Downloads**: JSON and MP3 file downloads
- **History**: View and replay previous translations
- **Responsive**: Works on desktop, tablet, and mobile
- **Real-time**: Updates without page refresh

## API Keys (Optional)
The app works without API keys using:
- MyMemory API for translations (free)
- Browser TTS for speech (free)

To add premium features, set these environment variables:
- `OPENAI_API_KEY` - For OpenAI TTS (premium audio)
- `GOOGLE_TRANSLATE_API_KEY` - For Google Translate
- `GOOGLE_TTS_API_KEY` - For Google TTS

## Support
The app is production-ready and includes:
- Error handling
- Loading states
- Toast notifications
- CSRF protection
- Input validation
- Responsive design
- Dark mode
- File downloads
