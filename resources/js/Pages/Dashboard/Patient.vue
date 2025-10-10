<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Patient Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Welcome back, {{ $page.props.auth.user.name }}!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Here's an overview of your therapy sessions and messages.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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

                            <div v-if="upcomingSessions.length > 0" class="space-y-4">
                                <div v-for="session in upcomingSessions" :key="session.id"
                                     class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                {{ session.therapist.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ session.therapist.therapist_profile.specialization }}
                                            </p>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
                                            Join Video
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="mb-4">No upcoming sessions</p>
                                <Link :href="route('therapists.index')"
                                      class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Book a Session
                                </Link>
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
                                     class="border-l-4 border-indigo-500 pl-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
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
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <p>No new messages</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Past Sessions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Past Sessions
                        </h3>

                        <div v-if="pastSessions.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Therapist
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="session in pastSessions" :key="session.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ session.therapist.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDate(session.scheduled_at) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="statusClass(session.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                {{ session.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('sessions.show', session.id)"
                                                  class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>No past sessions</p>
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
    pastSessions: Array,
    unreadMessages: Array,
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
