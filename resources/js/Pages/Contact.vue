<template>
    <Head title="Contact Us - SoulEase" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">SoulEase</span>
                    </Link>

                    <!-- Navigation Links -->
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                        Get in <span class="text-indigo-600 dark:text-indigo-400">Touch</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        We're here to help. Reach out to us anytime.
                    </p>
                </div>

                <!-- Contact Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 md:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <!-- Contact Info -->
                        <div class="space-y-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                                Contact Information
                            </h2>

                            <!-- Email -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Email</h3>
                                    <p class="text-gray-600 dark:text-gray-300">support@soulease.com</p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Phone</h3>
                                    <p class="text-gray-600 dark:text-gray-300">1-800-THERAPY</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Available 24/7</p>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Office</h3>
                                    <p class="text-gray-600 dark:text-gray-300">Online Services</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Available Worldwide</p>
                                </div>
                            </div>
                        </div>

                        <!-- Telegram Section -->
                        <div class="flex flex-col justify-center items-center bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-8">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-500 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Chat on Telegram
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300 mb-6">
                                    Get instant support and quick responses through Telegram
                                </p>
                            </div>

                            <a
                                href="https://t.me/olalalalalhiyosima"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center space-x-3 px-8 py-4 text-lg font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1"
                            >
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                <span>Message Us on Telegram</span>
                            </a>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Click to open in Telegram
                            </p>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            Office Hours
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-300">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Monday - Friday</p>
                                <p>9:00 AM - 8:00 PM</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Weekend</p>
                                <p>10:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                            Emergency support available 24/7 through our hotline
                        </p>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        Frequently Asked Questions
                    </h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                How quickly will I receive a response?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                We typically respond within 24 hours during business hours. For urgent matters, please call our hotline or message us on Telegram for faster support.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                Is my information kept confidential?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Yes, all communications are confidential and HIPAA-compliant. We take your privacy seriously and protect your personal information.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                What's the best way to reach you?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                For immediate assistance, we recommend messaging us on Telegram. For non-urgent inquiries, email works great. For emergencies, please call our 24/7 hotline.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-lg font-bold text-white">SoulEase</span>
                        </div>
                        <p class="text-sm">
                            Professional online therapy services connecting you with licensed therapists.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/" class="hover:text-indigo-400 transition">Home</Link></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">About Us</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Terms of Service</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-white font-semibold mb-4">Contact</h4>
                        <ul class="space-y-2 text-sm">
                            <li>Email: support@soulease.com</li>
                            <li>Phone: 1-800-THERAPY</li>
                            <li>Available 24/7 for support</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 text-center text-sm">
                    <p>&copy; 2025 SoulEase. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>
