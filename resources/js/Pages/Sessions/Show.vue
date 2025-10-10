<template>
    <AppLayout title="Session Details">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Session Details
                </h2>
                <Link :href="route('sessions.index')"
                      class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                    ← Back to Sessions
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="space-y-6">
                    <!-- Session Info Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Session Information
                            </h3>
                            <span :class="statusClass(session.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ session.status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Therapist Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                    {{ isPatient ? 'Therapist' : 'Patient' }}
                                </h4>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-300">
                                                {{ otherParty.name.charAt(0) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ otherParty.name }}
                                        </p>
                                        <p v-if="isPatient" class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ session.therapist.therapist_profile.specialization }}
                                        </p>
                                        <p v-if="otherParty.email" class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ otherParty.email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                    Schedule
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex items-center text-gray-900 dark:text-white">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ formatDate(session.scheduled_at) }}
                                    </div>
                                    <div class="flex items-center text-gray-900 dark:text-white">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ formatTime(session.scheduled_at) }}
                                    </div>
                                    <div class="flex items-center text-gray-900 dark:text-white">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ session.duration }} minutes
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div v-if="session.notes" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                Session Notes
                            </h4>
                            <p class="text-gray-900 dark:text-white whitespace-pre-line">
                                {{ session.notes }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
                            <Link v-if="session.status === 'confirmed' && canJoinSession"
                                  :href="route('sessions.video', session.id)"
                                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Join Video Session
                            </Link>

                            <Link :href="route('messages.index', { user: otherParty.id })"
                                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Send Message
                            </Link>

                            <button v-if="canConfirmSession"
                                    @click="confirmSession"
                                    :disabled="confirmForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition disabled:opacity-50">
                                {{ confirmForm.processing ? 'Confirming...' : 'Confirm Session' }}
                            </button>

                            <button v-if="canCompleteSession"
                                    @click="completeSession"
                                    :disabled="completeForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition disabled:opacity-50">
                                {{ completeForm.processing ? 'Completing...' : 'Mark as Completed' }}
                            </button>

                            <button v-if="canCancelSession"
                                    @click="cancelSession"
                                    :disabled="cancelForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition disabled:opacity-50">
                                {{ cancelForm.processing ? 'Cancelling...' : 'Cancel Session' }}
                            </button>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div v-if="session.payment" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Payment Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Session Fee:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">${{ session.total_amount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Payment Status:</span>
                                        <span :class="paymentStatusClass(session.payment.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                            {{ session.payment.status }}
                                        </span>
                                    </div>
                                    <div v-if="session.payment.transaction_id" class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                                        <span class="font-mono text-sm text-gray-900 dark:text-white">{{ session.payment.transaction_id }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="session.payment.status === 'pending' && isPatient">
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                                        Payment is required to confirm this session
                                    </p>
                                    <Link :href="route('payments.process', session.payment.id)"
                                          class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                                        Pay Now
                                    </Link>
                                </div>
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
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { format, parseISO, differenceInMinutes } from 'date-fns';

const props = defineProps({
    session: Object,
});

const page = usePage();

const confirmForm = useForm({});
const completeForm = useForm({});
const cancelForm = useForm({});

const isPatient = computed(() => page.props.auth.user.role === 'patient');
const isTherapist = computed(() => page.props.auth.user.role === 'therapist');

const otherParty = computed(() => {
    return isPatient.value ? props.session.therapist : props.session.patient;
});

const canJoinSession = computed(() => {
    if (props.session.status !== 'confirmed') return false;
    const now = new Date();
    const scheduledTime = parseISO(props.session.scheduled_at);
    const minutesDiff = differenceInMinutes(scheduledTime, now);
    return minutesDiff <= 15 && minutesDiff >= -60;
});

const canConfirmSession = computed(() => {
    return isTherapist.value && props.session.status === 'pending' && props.session.payment?.status === 'completed';
});

const canCompleteSession = computed(() => {
    return isTherapist.value && props.session.status === 'confirmed';
});

const canCancelSession = computed(() => {
    if (props.session.status !== 'pending') return false;
    const now = new Date();
    const scheduledTime = parseISO(props.session.scheduled_at);
    const minutesDiff = differenceInMinutes(scheduledTime, now);
    return minutesDiff > 1440; // 24 hours
});

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

const paymentStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    };
    return classes[status] || classes.pending;
};

const confirmSession = () => {
    if (confirm('Are you sure you want to confirm this session?')) {
        confirmForm.post(route('sessions.confirm', props.session.id), {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

const completeSession = () => {
    if (confirm('Mark this session as completed?')) {
        completeForm.post(route('sessions.complete', props.session.id), {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

const cancelSession = () => {
    if (confirm('Are you sure you want to cancel this session? This action cannot be undone.')) {
        cancelForm.post(route('sessions.cancel', props.session.id), {
            onSuccess: () => {
                router.visit(route('sessions.index'));
            }
        });
    }
};
</script>
