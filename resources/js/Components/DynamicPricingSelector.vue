<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Select Session Duration
        </h3>

        <div v-if="loading" class="text-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading pricing...</p>
        </div>

        <div v-else-if="error" class="text-red-600 dark:text-red-400">
            {{ error }}
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="option in durationOptions"
                :key="option.value"
                @click="selectDuration(option)"
                class="border rounded-lg p-4 cursor-pointer transition-all"
                :class="[
                    selectedDuration?.value === option.value
                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600'
                ]"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center"
                            :class="[
                                selectedDuration?.value === option.value
                                    ? 'border-indigo-500'
                                    : 'border-gray-300 dark:border-gray-600'
                            ]"
                        >
                            <div
                                v-if="selectedDuration?.value === option.value"
                                class="w-3 h-3 rounded-full bg-indigo-500"
                            ></div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ option.label }}
                            </p>
                            <p v-if="option.discount > 0" class="text-xs text-green-600 dark:text-green-400">
                                Save {{ formatDiscount(option.discount) }}%
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                            ${{ option.price }}
                        </p>
                        <p v-if="therapist" class="text-xs text-gray-500 dark:text-gray-400">
                            ${{ therapist.hourly_rate }}/hour
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price Breakdown -->
        <div
            v-if="selectedDuration && priceBreakdown"
            class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
        >
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                Price Breakdown
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                    <span>Base Price ({{ priceBreakdown.duration_hours }}h):</span>
                    <span>${{ priceBreakdown.base_price }}</span>
                </div>
                <div
                    v-if="priceBreakdown.discount > 0"
                    class="flex justify-between text-green-600 dark:text-green-400"
                >
                    <span>Duration Discount:</span>
                    <span>-${{ priceBreakdown.discount }}</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-600">
                    <span>Total:</span>
                    <span>${{ priceBreakdown.final_price }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    therapistId: {
        type: Number,
        required: true,
    },
    modelValue: Number,
});

const emit = defineEmits(['update:modelValue', 'priceCalculated']);

const loading = ref(true);
const error = ref(null);
const durationOptions = ref([]);
const therapist = ref(null);
const selectedDuration = ref(null);
const priceBreakdown = ref(null);

const formatDiscount = (discount) => {
    return Math.round((1 - discount) * 100);
};

const selectDuration = (option) => {
    selectedDuration.value = option;
    emit('update:modelValue', option.value);
    loadPriceBreakdown(option.value);
};

const loadDurationOptions = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get(`/api/session-duration-options`, {
            params: {
                therapist_id: props.therapistId
            }
        });

        const data = response.data;
        durationOptions.value = data.durations.map(d => ({
            ...d,
            discount: 1 - (d.price / ((data.therapist.hourly_rate / 60) * d.value))
        }));
        therapist.value = data.therapist;

        // Auto-select first option if none selected
        if (!selectedDuration.value && durationOptions.value.length > 0) {
            const defaultOption = durationOptions.value.find(d => d.value === 60) || durationOptions.value[0];
            selectDuration(defaultOption);
        }
    } catch (err) {
        console.error('Error loading duration options:', err);
        error.value = err.response?.data?.message || 'Failed to load pricing options. Please try again.';
    } finally {
        loading.value = false;
    }
};

const loadPriceBreakdown = async (durationMinutes) => {
    try {
        const response = await axios.post('/api/calculate-session-price', {
            therapist_id: props.therapistId,
            duration_minutes: durationMinutes,
        });

        priceBreakdown.value = response.data;
        emit('priceCalculated', response.data);
    } catch (err) {
        console.error('Error calculating price:', err);
    }
};

onMounted(() => {
    loadDurationOptions();
});

watch(() => props.therapistId, () => {
    loadDurationOptions();
});
</script>
