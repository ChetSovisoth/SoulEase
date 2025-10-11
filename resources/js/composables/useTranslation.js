import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslation() {
    const page = usePage();

    const translations = computed(() => page.props.translations || {});
    const locale = computed(() => page.props.locale || 'en');

    const trans = (key, replacements = {}) => {
        const keys = key.split('.');
        let value = translations.value;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return key; // Return the key if translation not found
            }
        }

        // Handle replacements like :name
        if (typeof value === 'string') {
            Object.keys(replacements).forEach(replace => {
                value = value.replace(`:${replace}`, replacements[replace]);
            });
        }

        return value;
    };

    const __ = trans; // Alias

    return {
        trans,
        __,
        locale,
        translations,
    };
}
