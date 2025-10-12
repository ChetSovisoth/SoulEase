<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Peer from 'simple-peer';
import axios from 'axios';

const props = defineProps({
    users: Array,
    authUser: Object
});

const localVideo = ref(null);
const remoteVideo = ref(null);
const localStream = ref(null);
const peer = ref(null);
const onlineUsers = ref([]);
const inCall = ref(false);
const callingUser = ref(null);

let echoChannel = null;

onMounted(async () => {
    // Join presence channel first (works without camera)
    echoChannel = window.Echo.join('presence-video-channel')
            .here((users) => {
                onlineUsers.value = users.filter(u => u.id !== props.authUser.id);
            })
            .joining((user) => {
                if (user.id !== props.authUser.id) {
                    const index = onlineUsers.value.findIndex(u => u.id === user.id);
                    if (index === -1) {
                        onlineUsers.value.push(user);
                    }
                }
            })
            .leaving((user) => {
                onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
            })
            .listen('.StartVideoChat', (data) => {
                if (data.data.userToCall === props.authUser.id) {
                    answerCall(data.data);
                }
            });

    // Get user media
    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true
            });
            localStream.value = stream;
            if (localVideo.value) {
                localVideo.value.srcObject = stream;
            }
        } else {
            console.error('getUserMedia is not supported');
            alert('Your browser does not support video calling. Please use Chrome, Firefox, or Safari on HTTPS.');
        }
    } catch (err) {
        console.error('Error accessing media devices:', err);
        alert('Please allow camera and microphone access to use video calling.');
    }
});

onBeforeUnmount(() => {
    // Clean up
    if (peer.value) {
        peer.value.destroy();
    }
    if (localStream.value) {
        localStream.value.getTracks().forEach(track => track.stop());
    }
    if (echoChannel) {
        window.Echo.leave('presence-video-channel');
    }
});

const isUserOnline = (userId) => {
    return onlineUsers.value.some(u => u.id === userId);
};

const callUser = (user) => {
    if (!confirm(`Call ${user.name}?`)) {
        return;
    }

    callingUser.value = user;

    peer.value = new Peer({
        initiator: true,
        trickle: false,
        stream: localStream.value
    });

    peer.value.on('signal', (data) => {
        axios.post('/video-call/call-user', {
            user_to_call: user.id,
            signal_data: data
        }).catch(err => {
            console.error('Error calling user:', err);
            alert('Failed to initiate call');
        });
    });

    peer.value.on('stream', (stream) => {
        if (remoteVideo.value) {
            remoteVideo.value.srcObject = stream;
            inCall.value = true;
        }
    });

    peer.value.on('error', (err) => {
        console.error('Peer error:', err);
        endCall();
    });
};

const answerCall = (data) => {
    const caller = props.users.find(u => u.id === data.from);

    if (!confirm(`Incoming call from ${caller?.name || 'Unknown'}. Accept?`)) {
        return;
    }

    callingUser.value = caller;

    peer.value = new Peer({
        initiator: false,
        trickle: false,
        stream: localStream.value
    });

    peer.value.on('signal', (signalData) => {
        axios.post('/video-call/call-user', {
            user_to_call: data.from,
            signal_data: signalData
        }).catch(err => {
            console.error('Error answering call:', err);
        });
    });

    peer.value.on('stream', (stream) => {
        if (remoteVideo.value) {
            remoteVideo.value.srcObject = stream;
            inCall.value = true;
        }
    });

    peer.value.on('error', (err) => {
        console.error('Peer error:', err);
        endCall();
    });

    peer.value.signal(data.signalData);
};

const endCall = () => {
    if (peer.value) {
        peer.value.destroy();
        peer.value = null;
    }
    if (remoteVideo.value) {
        remoteVideo.value.srcObject = null;
    }
    inCall.value = false;
    callingUser.value = null;
};
</script>

<template>
    <AppLayout title="Video Call">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Video Call Room
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- User List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        Available Users
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <button
                            v-for="user in users"
                            :key="user.id"
                            @click="callUser(user)"
                            :disabled="!isUserOnline(user.id)"
                            :class="[
                                'px-4 py-2 rounded-lg font-medium transition-colors',
                                isUserOnline(user.id)
                                    ? 'bg-blue-500 hover:bg-blue-600 text-white cursor-pointer'
                                    : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                            ]"
                        >
                            {{ user.name }}
                            <span class="ml-2 text-sm">
                                {{ isUserOnline(user.id) ? '(Online)' : '(Offline)' }}
                            </span>
                        </button>
                    </div>
                    <p v-if="users.length === 0" class="text-gray-500 dark:text-gray-400">
                        No other users available
                    </p>
                </div>

                <!-- Video Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Local Video -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">
                                Your Video
                            </h3>
                            <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                                <video
                                    ref="localVideo"
                                    autoplay
                                    muted
                                    playsinline
                                    class="w-full h-full object-cover"
                                ></video>
                            </div>
                        </div>

                        <!-- Remote Video -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">
                                Remote Video
                                <span v-if="callingUser" class="text-sm font-normal">
                                    - {{ callingUser.name }}
                                </span>
                            </h3>
                            <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                                <video
                                    ref="remoteVideo"
                                    autoplay
                                    playsinline
                                    class="w-full h-full object-cover"
                                ></video>
                                <div
                                    v-if="!inCall"
                                    class="absolute inset-0 flex items-center justify-center text-gray-400"
                                >
                                    Waiting for call...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End Call Button -->
                    <div v-if="inCall" class="mt-6 text-center">
                        <button
                            @click="endCall"
                            class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors"
                        >
                            End Call
                        </button>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 overflow-hidden shadow-xl sm:rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">
                        How to use:
                    </h3>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        <li>Make sure your camera and microphone are enabled</li>
                        <li>Wait for other users to come online</li>
                        <li>Click on a user's name to start a video call</li>
                        <li>Accept incoming calls with the browser prompt</li>
                        <li>Click "End Call" to disconnect</li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
