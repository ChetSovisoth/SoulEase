<template>
    <div class="p-8">
        <h1>Translation Debug</h1>
        <div class="mt-4">
            <p><strong>Session Locale:</strong> {{ sessionLocale }}</p>
            <p><strong>App Locale:</strong> {{ appLocale }}</p>
            <p><strong>Current Locale (from Inertia):</strong> {{ locale }}</p>
            <p><strong>Translations loaded:</strong> {{ !!translations }}</p>
            <p><strong>Translation keys:</strong> {{ Object.keys(translations) }}</p>
            <hr class="my-4">
            <p><strong>Test translation:</strong> {{ __('app.therapists.browse') }}</p>
            <hr class="my-4">
            <pre class="bg-gray-100 p-4 text-xs overflow-auto">{{ JSON.stringify(translations, null, 2).substring(0, 500) }}</pre>
        </div>
    </div>
</template>

<script setup>
import { useTranslation } from '@/composables/useTranslation';

defineProps({
    sessionLocale: String,
    appLocale: String,
});

const { __, locale, translations } = useTranslation();
</script>
