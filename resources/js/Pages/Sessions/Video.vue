<template>
    <AppLayout title="Video Session">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Video Session
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Session with {{ otherParty.name }}
                    </p>
                </div>
                <Link :href="route('sessions.show', session.id)"
                      class="text-sm text-red-600 hover:text-red-900 dark:text-red-400">
                    Leave Session
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Video Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div id="jitsi-container" class="w-full" style="min-height: 600px;"></div>
                </div>

                <!-- Session Info Bar -->
                <div class="mt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ formatTime(session.scheduled_at) }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ session.duration }} minutes
                            </div>
                        </div>
                        <div class="mt-3 md:mt-0 flex items-center space-x-3">
                            <Link v-if="otherParty?.id" :href="route('messages.index', { user: otherParty.id })"
                                  class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Chat
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Session Notes (For Therapists) -->
                <div v-if="isTherapist" class="mt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Session Notes
                    </h3>
                    <form @submit.prevent="saveNotes">
                        <textarea
                            v-model="notesForm.notes"
                            rows="4"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add notes about this session..."
                        ></textarea>
                        <div class="mt-3 flex justify-end">
                            <button
                                type="submit"
                                :disabled="notesForm.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                {{ notesForm.processing ? 'Saving...' : 'Save Notes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Important Notice -->
                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Privacy & Security
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>This session is private and confidential</li>
                                    <li>Recording is disabled for your privacy</li>
                                    <li>End the call when your session is complete</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted } from 'vue';
import { format, parseISO } from 'date-fns';

const props = defineProps({
    session: Object,
    jitsiDomain: String,
});

const page = usePage();

const notesForm = useForm({
    notes: props.session.notes || '',
});

const isTherapist = computed(() => page.props.auth.user.role === 'therapist');
const isPatient = computed(() => page.props.auth.user.role === 'patient');

const otherParty = computed(() => {
    return isPatient.value ? props.session.therapist : props.session.patient;
});

const formatTime = (date) => {
    return format(parseISO(date), 'h:mm a');
};

let jitsiApi = null;

const initJitsi = () => {
    const domain = props.jitsiDomain || 'meet.jit.si';
    const options = {
        roomName: props.session.video_room_id,
        width: '100%',
        height: 600,
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: page.props.auth.user.name,
            email: page.props.auth.user.email,
        },
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            enableWelcomePage: false,
            prejoinPageEnabled: false,
            disableDeepLinking: true,
            enableClosePage: false,
            hideConferenceTimer: false,
            enableNoisyMicDetection: true,
            // Disable recording for privacy
            disableRecording: true,
            fileRecordingsEnabled: false,
            liveStreamingEnabled: false,
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone',
                'camera',
                'closedcaptions',
                'desktop',
                'fullscreen',
                'fodeviceselection',
                'hangup',
                'chat',
                'settings',
                'raisehand',
                'videoquality',
                'filmstrip',
                'tileview',
                'select-background',
                'mute-everyone',
                'mute-video-everyone',
            ],
            SETTINGS_SECTIONS: ['devices', 'language'],
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            SHOW_POWERED_BY: false,
            MOBILE_APP_PROMO: false,
        },
    };

    // Load Jitsi API script
    const script = document.createElement('script');
    script.src = `https://${domain}/external_api.js`;
    script.async = true;
    script.onload = () => {
        // Initialize Jitsi Meet
        jitsiApi = new window.JitsiMeetExternalAPI(domain, options);

        // Event listeners
        jitsiApi.addEventListener('videoConferenceLeft', () => {
            // User left the conference
            console.log('User left the conference');
        });

        jitsiApi.addEventListener('readyToClose', () => {
            // Meeting ended, redirect to session details
            window.location.href = route('sessions.show', props.session.id);
        });
    };
    document.head.appendChild(script);
};

const saveNotes = () => {
    notesForm.post(route('sessions.notes', props.session.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
            console.log('Notes saved successfully');
        }
    });
};

onMounted(() => {
    initJitsi();
});

onUnmounted(() => {
    if (jitsiApi) {
        jitsiApi.dispose();
    }
});
</script>
