<template>
    <AppLayout title="Therapist Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Therapist Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Sessions</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.total_sessions }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.completed_sessions }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.pending_sessions }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.today_sessions }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Sessions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Today's Sessions
                        </h3>

                        <div v-if="todaySessions.length > 0" class="space-y-4">
                            <div v-for="session in todaySessions" :key="session.id"
                                 class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ session.patient.name }}
                                        </h4>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ formatDate(session.scheduled_at) }}
                                        </div>
                                    </div>
                                    <span :class="statusClass(session.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                                        {{ session.status }}
                                    </span>
                                </div>
                                <div class="mt-3 flex space-x-2">
                                    <Link :href="route('sessions.show', session.id)"
                                          class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                        View Details
                                    </Link>
                                    <Link v-if="session.status === 'confirmed'"
                                          :href="route('sessions.video', session.id)"
                                          class="text-sm text-green-600 hover:text-green-900 font-medium">
                                        Start Video
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>No sessions scheduled for today</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Upcoming Sessions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Upcoming Sessions
                                </h3>
                                <Link :href="route('sessions.index')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    View All
                                </Link>
                            </div>

                            <div v-if="upcomingSessions.length > 0" class="space-y-3">
                                <div v-for="session in upcomingSessions.slice(0, 5)" :key="session.id"
                                     class="border-l-4 border-indigo-500 pl-4 py-2">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ session.patient.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ formatDate(session.scheduled_at) }}
                                    </p>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <p>No upcoming sessions</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Recent Messages
                                </h3>
                                <Link :href="route('messages.index')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    View All
                                </Link>
                            </div>

                            <div v-if="unreadMessages.length > 0" class="space-y-3">
                                <div v-for="message in unreadMessages" :key="message.id"
                                     class="border-l-4 border-indigo-500 pl-4 py-2">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ message.sender.name }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                        {{ message.content }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        {{ formatDate(message.created_at) }}
                                    </p>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <p>No new messages</p>
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
import { Link } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';

const props = defineProps({
    upcomingSessions: Array,
    todaySessions: Array,
    unreadMessages: Array,
    stats: Object,
});

const formatDate = (date) => {
    return format(parseISO(date), 'MMM dd, yyyy h:mm a');
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
</script>
