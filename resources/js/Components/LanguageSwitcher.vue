<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
                <span>{{ currentLanguageName }}</span>
                <svg class="ml-1 -mr-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </template>

        <template #content>
            <DropdownLink
                v-for="lang in languages"
                :key="lang.code"
                :href="route('locale.switch', lang.code)"
            >
                <div class="flex items-center">
                    <span :class="currentLocale === lang.code ? 'font-bold' : ''">
                        {{ lang.name }}
                    </span>
                    <svg v-if="currentLocale === lang.code" class="ml-auto h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </DropdownLink>
        </template>
    </Dropdown>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';
import DropdownLink from './DropdownLink.vue';

const page = usePage();

const currentLocale = computed(() => page.props.locale || 'en');

const languages = [
    { code: 'en', name: 'English' },
    { code: 'km', name: 'ភាសាខ្មែរ' },
];

const currentLanguageName = computed(() => {
    return languages.find(lang => lang.code === currentLocale.value)?.name || 'English';
});
</script>
