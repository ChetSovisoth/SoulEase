<template>
    <AppLayout title="Payment">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Process Payment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Payment Summary -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Payment Summary
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Session with:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ payment.therapy_session.therapist.name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Specialization:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ payment.therapy_session.therapist.therapist_profile.specialization }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Date & Time:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ formatDateTime(payment.therapy_session.scheduled_at) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ payment.therapy_session.duration }} minutes
                                </span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total Amount:</span>
                                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                        ${{ payment.amount }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Notice -->
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Demo Payment Gateway
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <p>This is a mock payment system for demonstration purposes. No real payment will be processed.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form @submit.prevent="processPayment">
                        <div class="space-y-6">
                            <!-- Card Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Card Number
                                </label>
                                <input
                                    type="text"
                                    v-model="paymentForm.card_number"
                                    placeholder="4242 4242 4242 4242"
                                    maxlength="19"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Use 4242 4242 4242 4242 for test payment
                                </p>
                            </div>

                            <!-- Cardholder Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cardholder Name
                                </label>
                                <input
                                    type="text"
                                    v-model="paymentForm.cardholder_name"
                                    placeholder="John Doe"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                            </div>

                            <!-- Expiry and CVV -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Expiry Date
                                    </label>
                                    <input
                                        type="text"
                                        v-model="paymentForm.expiry"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        CVV
                                    </label>
                                    <input
                                        type="text"
                                        v-model="paymentForm.cvv"
                                        placeholder="123"
                                        maxlength="4"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="flex items-start">
                                <input
                                    type="checkbox"
                                    v-model="paymentForm.agree_terms"
                                    class="mt-1 rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500"
                                    required
                                />
                                <label class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    I agree to the cancellation policy and understand that I must cancel at least 24 hours in advance for a full refund.
                                </label>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3 pt-4">
                                <Link :href="route('sessions.show', payment.therapy_session.id)"
                                      class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="paymentForm.processing"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50"
                                >
                                    <svg v-if="paymentForm.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ paymentForm.processing ? 'Processing...' : `Pay $${payment.amount}` }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Security Notice -->
                    <div class="mt-6 flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Your payment information is encrypted and secure. We never store your full card details.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';

const props = defineProps({
    payment: Object,
});

const paymentForm = useForm({
    card_number: '',
    cardholder_name: '',
    expiry: '',
    cvv: '',
    agree_terms: false,
});

const formatDateTime = (date) => {
    return format(parseISO(date), 'EEEE, MMMM d, yyyy \'at\' h:mm a');
};

const processPayment = () => {
    paymentForm.post(route('payments.process', props.payment.id), {
        onSuccess: () => {
            router.visit(route('sessions.show', props.payment.therapy_session.id));
        },
    });
};
</script>
