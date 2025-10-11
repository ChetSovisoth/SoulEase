<template>
    <AppLayout title="Therapist Profile">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Therapist Profile
                </h2>
                <Link :href="route('therapists.index')"
                      class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                    ← Back to Browse
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Therapist Info Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 sticky top-6">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-32 w-32 bg-indigo-100 dark:bg-indigo-900 rounded-full mb-4">
                                    <span class="text-5xl font-bold text-indigo-600 dark:text-indigo-300">
                                        {{ therapist.name.charAt(0) }}
                                    </span>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ therapist.name }}
                                </h3>
                                <p class="text-lg text-indigo-600 dark:text-indigo-400 mb-4">
                                    {{ therapist.therapist_profile.specialization }}
                                </p>
                                <div v-if="therapist.therapist_profile.is_verified"
                                     class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ therapist.therapist_profile.years_of_experience }} years experience</span>
                                </div>

                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                        ${{ therapist.therapist_profile.hourly_rate }}/hour
                                    </span>
                                </div>

                                <div class="flex items-start text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-3 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    <span>{{ formatLanguages(therapist.therapist_profile.languages) }}</span>
                                </div>

                                <div v-if="therapist.phone" class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ therapist.phone }}</span>
                                </div>

                                <div v-if="therapist.email" class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm">{{ therapist.email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- About Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                About
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ therapist.bio || 'No biography available.' }}
                            </p>
                        </div>

                        <!-- Qualifications -->
                        <div v-if="therapist.therapist_profile.qualifications"
                             class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Qualifications
                            </h4>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ therapist.therapist_profile.qualifications }}
                            </div>
                        </div>

                        <!-- Available Time Slots -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Book a Session
                            </h4>

                            <!-- Date Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Date
                                </label>
                                <div class="grid grid-cols-7 gap-2">
                                    <button
                                        v-for="date in availableDates"
                                        :key="date.dateStr"
                                        @click="selectedDate = date.dateStr"
                                        :class="[
                                            'p-3 rounded-lg text-center transition',
                                            selectedDate === date.dateStr
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600'
                                        ]"
                                    >
                                        <div class="text-xs font-medium">{{ date.dayName }}</div>
                                        <div class="text-lg font-bold">{{ date.day }}</div>
                                        <div class="text-xs">{{ date.month }}</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Time Slots -->
                            <div v-if="selectedDate">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Available Time Slots for {{ formatSelectedDate }}
                                </label>

                                <div v-if="filteredSlots.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                    <button
                                        v-for="slot in filteredSlots"
                                        :key="slot.id"
                                        @click="selectSlot(slot)"
                                        :disabled="!slot.is_available"
                                        :class="[
                                            'p-3 rounded-lg text-sm font-medium transition',
                                            slot.is_available
                                                ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500'
                                        ]"
                                    >
                                        {{ formatTime(slot.start_time) }}
                                    </button>
                                </div>

                                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <p>No available time slots for this date</p>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <p>Please select a date to view available time slots</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Confirmation Modal -->
        <Modal :show="showBookingModal" @close="showBookingModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Confirm Session Booking
                </h3>

                <div v-if="selectedSlot" class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Therapist:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ therapist.name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Date:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ formatSelectedDate }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Time:</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ formatTime(selectedSlot.start_time) }} - {{ formatTime(selectedSlot.end_time) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Rate:</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            ${{ therapist.therapist_profile.hourly_rate }}
                        </span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                ${{ therapist.therapist_profile.hourly_rate }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button
                        @click="showBookingModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmBooking"
                        :disabled="bookingForm.processing"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                    >
                        {{ bookingForm.processing ? 'Booking...' : 'Confirm Booking' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { format, parseISO, addDays } from 'date-fns';

const props = defineProps({
    therapist: Object,
    availabilitySlots: Array,
});

const selectedDate = ref(null);
const selectedSlot = ref(null);
const showBookingModal = ref(false);

const bookingForm = useForm({
    therapist_id: props.therapist.id,
    availability_slot_id: null,
});

// Generate next 7 days for date selection
const availableDates = computed(() => {
    const dates = [];
    for (let i = 0; i < 7; i++) {
        const date = addDays(new Date(), i);
        dates.push({
            dateStr: format(date, 'yyyy-MM-dd'),
            day: format(date, 'd'),
            dayName: format(date, 'EEE'),
            month: format(date, 'MMM'),
        });
    }
    return dates;
});

// Filter slots by selected date
const filteredSlots = computed(() => {
    if (!selectedDate.value) return [];
    return props.availabilitySlots.filter(slot => slot.date === selectedDate.value);
});

const formatSelectedDate = computed(() => {
    if (!selectedDate.value) return '';
    return format(parseISO(selectedDate.value), 'EEEE, MMMM d, yyyy');
});

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
};

const formatLanguages = (languages) => {
    if (!languages || languages.length === 0) return 'Not specified';
    return languages.join(', ');
};

const selectSlot = (slot) => {
    if (!slot.is_available) return;
    selectedSlot.value = slot;
    showBookingModal.value = true;
};

const confirmBooking = () => {
    bookingForm.availability_slot_id = selectedSlot.value.id;
    bookingForm.post(route('sessions.store'), {
        onSuccess: () => {
            showBookingModal.value = false;
            selectedSlot.value = null;
            // Controller will redirect to sessions.show
        },
    });
};
</script>
