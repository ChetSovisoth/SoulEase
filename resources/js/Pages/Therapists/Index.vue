<template>
    <AppLayout :title="__('app.therapists.browse')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('app.therapists.browse') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search and Filter -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('app.therapists.browse') }}
                            </label>
                            <input
                                type="text"
                                v-model="filters.specialization"
                                @input="filterTherapists"
                                :placeholder="__('app.therapists.search_placeholder')"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                            >
                                {{ __('app.therapists.clear_filters') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Therapists Grid -->
                <div v-if="therapists.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="therapist in therapists.data" :key="therapist.id"
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition duration-300">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">
                                            {{ therapist.name.charAt(0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ therapist.name }}
                                    </h3>
                                    <p class="text-sm text-indigo-600 dark:text-indigo-400">
                                        {{ therapist.therapist_profile.specialization }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ therapist.therapist_profile.years_of_experience }} {{ __('app.therapists.years_experience') }}
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ${{ therapist.therapist_profile.hourly_rate }}{{ __('app.therapists.per_hour') }}
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    {{ formatLanguages(therapist.therapist_profile.languages) }}
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                {{ therapist.bio || 'No bio available' }}
                            </p>

                            <Link
                                :href="route('therapists.show', therapist.id)"
                                class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                View Profile & Book
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-12">
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('app.therapists.no_therapists') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('app.therapists.adjust_filters') }}
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="therapists.data.length > 0" class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-400">
                            {{ __('app.common.showing') }} {{ therapists.from }} {{ __('app.common.to') }} {{ therapists.to }} {{ __('app.common.of') }} {{ therapists.total }} {{ __('app.common.results') }}
                        </div>
                        <div class="flex space-x-2">
                            <component
                                v-for="link in therapists.links"
                                :key="link.label"
                                :is="link.url ? Link : 'span'"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded-md text-sm',
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : link.url
                                            ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                            : 'bg-gray-100 dark:bg-gray-900 text-gray-400 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();

const props = defineProps({
    therapists: Object,
    filters: Object,
});

const filters = reactive({
    specialization: props.filters.specialization || '',
});

const filterTherapists = () => {
    router.get(route('therapists.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.specialization = '';
    filterTherapists();
};

const formatLanguages = (languages) => {
    if (!languages || languages.length === 0) return 'Not specified';
    return languages.join(', ');
};
</script>
