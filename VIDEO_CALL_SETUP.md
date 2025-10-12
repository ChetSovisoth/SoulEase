# Video Call Setup Instructions

## Overview
This application now includes WebRTC-based video and voice calling functionality. Users can see who is online and initiate 1-on-1 video calls.

## Setup Steps

### 1. Get Pusher Credentials (Free)

1. Go to [https://pusher.com](https://pusher.com) and create a free account
2. Create a new "Channels" app
3. Copy the following credentials from your app settings:
   - App ID
   - App Key
   - App Secret
   - Cluster (e.g., mt1, us2, eu, etc.)

### 2. Update Your .env File

Update the following lines in your `.env` file with your Pusher credentials:

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id_here
PUSHER_APP_KEY=your_app_key_here
PUSHER_APP_SECRET=your_app_secret_here
PUSHER_APP_CLUSTER=your_cluster_here

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Install Dependencies & Build Assets

```bash
# Install npm packages (already done if you ran this before)
npm install

# Build the frontend assets
npm run build

# Or run in development mode
npm run dev
```

### 4. Start the Laravel Server

```bash
php artisan serve
```

## How to Use

1. **Create Multiple User Accounts**: You need at least 2 users to test video calling
   - Create accounts through the registration page

2. **Access the Video Call Page**: Navigate to `/video-call` route
   - Example: `http://localhost:8000/video-call`

3. **Allow Camera & Microphone Access**:
   - Your browser will prompt you to allow camera and microphone access
   - Click "Allow" to enable video calling

4. **See Online Users**:
   - Open the video call page in two different browsers (or one normal + one incognito window)
   - Log in as different users in each window
   - You'll see each other appear as "Online" in the user list

5. **Start a Call**:
   - Click on an online user's name to initiate a call
   - The other user will receive a browser confirmation dialog
   - Accept the call to connect

6. **End Call**:
   - Click the "End Call" button to disconnect

## Features

- ✅ Real-time presence detection (see who's online)
- ✅ WebRTC peer-to-peer video calls
- ✅ Audio calling included
- ✅ Simple browser-based interface
- ✅ No additional servers needed (uses Pusher for signaling)
- ✅ Works for 2-10 concurrent users (perfect for class project)

## Troubleshooting

### Camera/Microphone Not Working
- Make sure you allowed browser permissions for camera and microphone
- Check if another application is using your camera
- Try a different browser (Chrome and Firefox work best)

### Users Not Showing as Online
- Make sure you've updated the Pusher credentials in `.env`
- Run `npm run build` or `npm run dev` after changing `.env`
- Check browser console for errors
- Verify `BROADCAST_CONNECTION=pusher` is set in `.env`

### Call Not Connecting
- Both users must be on the video call page
- Check browser console for WebRTC errors
- Make sure both users have allowed camera/microphone access
- Firewalls may block WebRTC in some networks

### "Module not found" Error
- Run `npm install --legacy-peer-deps` again
- Clear cache: `npm cache clean --force`
- Delete `node_modules` and `package-lock.json`, then run `npm install --legacy-peer-deps`

## Technical Details

**Stack:**
- Laravel 12 backend
- Vue 3 frontend (Inertia.js)
- WebRTC (via simple-peer library)
- Pusher (for presence channels and signaling)
- Laravel Echo (for real-time events)

**Files Created:**
- `app/Http/Controllers/VideoCallController.php` - Controller for video call routes
- `app/Events/StartVideoChat.php` - Event for WebRTC signaling
- `app/Providers/BroadcastServiceProvider.php` - Broadcasting service provider
- `resources/js/Pages/VideoCall.vue` - Video call page component
- `routes/channels.php` - Updated with presence channel
- `routes/web.php` - Updated with video call routes

## Free Tier Limits

Pusher free tier includes:
- 100 concurrent connections
- 200,000 messages per day
- Unlimited channels

This is more than enough for a class project or small business application!

## Production Notes

For production use with many users or users behind restrictive firewalls, you may need:
- A TURN server for NAT traversal
- Consider alternatives like Agora SDK or Twilio for better reliability
- Implement call history and session management
- Add call quality monitoring
