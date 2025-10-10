<template>
    <AppLayout title="My Sessions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Sessions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filter Tabs -->
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="flex border-b border-gray-200 dark:border-gray-700">
                            <button
                                @click="activeTab = 'upcoming'"
                                :class="[
                                    'flex-1 px-6 py-4 text-sm font-medium transition',
                                    activeTab === 'upcoming'
                                        ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400'
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                ]"
                            >
                                Upcoming ({{ upcomingSessions.length }})
                            </button>
                            <button
                                @click="activeTab = 'past'"
                                :class="[
                                    'flex-1 px-6 py-4 text-sm font-medium transition',
                                    activeTab === 'past'
                                        ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400'
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                ]"
                            >
                                Past ({{ pastSessions.length }})
                            </button>
                            <button
                                @click="activeTab = 'cancelled'"
                                :class="[
                                    'flex-1 px-6 py-4 text-sm font-medium transition',
                                    activeTab === 'cancelled'
                                        ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400'
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                ]"
                            >
                                Cancelled ({{ cancelledSessions.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Session List -->
                <div class="space-y-4">
                    <!-- Upcoming Sessions -->
                    <div v-if="activeTab === 'upcoming'">
                        <div v-if="upcomingSessions.length > 0" class="space-y-4">
                            <div v-for="session in upcomingSessions" :key="session.id"
                                 class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-300">
                                                        {{ getOtherParty(session).name.charAt(0) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ getOtherParty(session).name }}
                                                </h3>
                                                <p v-if="isPatient" class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ session.therapist.therapist_profile.specialization }}
                                                </p>
                                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(session.scheduled_at) }}
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ formatTime(session.scheduled_at) }}
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        ${{ session.total_amount }}
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span :class="statusClass(session.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                        {{ session.status }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-wrap gap-2">
                                        <Link :href="route('sessions.show', session.id)"
                                              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                            View Details
                                        </Link>
                                        <Link v-if="session.status === 'confirmed' && canJoinSession(session)"
                                              :href="route('sessions.video', session.id)"
                                              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Join Video
                                        </Link>
                                        <button v-if="session.status === 'pending' && canCancelSession(session)"
                                                @click="cancelSession(session.id)"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-12">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    No upcoming sessions
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    {{ isPatient ? 'Book a session with a therapist to get started' : 'No upcoming appointments scheduled' }}
                                </p>
                                <Link v-if="isPatient" :href="route('therapists.index')"
                                      class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    Browse Therapists
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Past Sessions -->
                    <div v-if="activeTab === 'past'">
                        <div v-if="pastSessions.length > 0" class="space-y-4">
                            <div v-for="session in pastSessions" :key="session.id"
                                 class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                    <span class="text-lg font-bold text-gray-600 dark:text-gray-300">
                                                        {{ getOtherParty(session).name.charAt(0) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ getOtherParty(session).name }}
                                                </h3>
                                                <p v-if="isPatient" class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ session.therapist.therapist_profile.specialization }}
                                                </p>
                                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(session.scheduled_at) }}
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span :class="statusClass(session.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                        {{ session.status }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 lg:mt-0 lg:ml-6">
                                        <Link :href="route('sessions.show', session.id)"
                                              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                            View Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-12">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    No past sessions
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Cancelled Sessions -->
                    <div v-if="activeTab === 'cancelled'">
                        <div v-if="cancelledSessions.length > 0" class="space-y-4">
                            <div v-for="session in cancelledSessions" :key="session.id"
                                 class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 opacity-75">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                                    <span class="text-lg font-bold text-red-600 dark:text-red-300">
                                                        {{ getOtherParty(session).name.charAt(0) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ getOtherParty(session).name }}
                                                </h3>
                                                <p v-if="isPatient" class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ session.therapist.therapist_profile.specialization }}
                                                </p>
                                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(session.scheduled_at) }}
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span :class="statusClass(session.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                        Cancelled
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-12">
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    No cancelled sessions
                                </h3>
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { format, parseISO, differenceInMinutes } from 'date-fns';

const props = defineProps({
    upcomingSessions: Array,
    pastSessions: Array,
    cancelledSessions: Array,
});

const page = usePage();
const activeTab = ref('upcoming');

const isPatient = computed(() => page.props.auth.user.role === 'patient');

const formatDate = (date) => {
    return format(parseISO(date), 'EEEE, MMMM d, yyyy');
};

const formatTime = (date) => {
    return format(parseISO(date), 'h:mm a');
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        confirmed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return classes[status] || classes.pending;
};

const getOtherParty = (session) => {
    return isPatient.value ? session.therapist : session.patient;
};

const canJoinSession = (session) => {
    // Allow joining 15 minutes before scheduled time
    const now = new Date();
    const scheduledTime = parseISO(session.scheduled_at);
    const minutesDiff = differenceInMinutes(scheduledTime, now);
    return minutesDiff <= 15 && minutesDiff >= -60; // 15 min before to 60 min after
};

const canCancelSession = (session) => {
    // Allow cancellation up to 24 hours before session
    const now = new Date();
    const scheduledTime = parseISO(session.scheduled_at);
    const minutesDiff = differenceInMinutes(scheduledTime, now);
    return minutesDiff > 1440; // 24 hours = 1440 minutes
};

const cancelSession = (sessionId) => {
    if (confirm('Are you sure you want to cancel this session?')) {
        router.post(route('sessions.cancel', sessionId), {}, {
            onSuccess: () => {
                // Session list will be refreshed automatically
            }
        });
    }
};
</script>
