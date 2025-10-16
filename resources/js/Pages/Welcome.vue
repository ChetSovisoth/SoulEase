<template>
    <Head title="Welcome - Online Therapy System" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">SoulEase</span>
                    </div>

                    <!-- Navigation Links -->
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            :href="route('contact')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                        >
                            Contact
                        </Link>
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

        <!-- Hero Section -->
        <div class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                        Your Mental Health
                        <span class="text-indigo-600 dark:text-indigo-400">Journey</span>
                        <br />Starts Here
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                        Connect with licensed therapists from the comfort of your home. Get professional support through secure video sessions and messaging.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="canLogin && !$page.props.auth.user ? route('register') : route('login')"
                            class="px-8 py-4 text-lg font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-lg hover:shadow-xl transition"
                        >
                            Book Your First Session
                        </Link>
                        <a
                            href="#features"
                            class="px-8 py-4 text-lg font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 shadow-lg hover:shadow-xl transition"
                        >
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Why Choose SoulEase?
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Professional therapy made accessible and convenient
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-xl">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Licensed Professionals</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            All therapists are verified, licensed professionals with years of experience in their specializations.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-xl">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Secure Video Sessions</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Connect face-to-face through encrypted video calls from anywhere, at any time that works for you.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-xl">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">24/7 Messaging</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Stay connected with your therapist through secure messaging for ongoing support between sessions.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specializations Section -->
        <div class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Areas of Support
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Find therapists specialized in various areas of mental health
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg text-center hover:shadow-lg transition">
                        <div class="text-2xl mb-2">😌</div>
                        <p class="font-medium text-gray-900 dark:text-white">Anxiety & Depression</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg text-center hover:shadow-lg transition">
                        <div class="text-2xl mb-2">💪</div>
                        <p class="font-medium text-gray-900 dark:text-white">Trauma & PTSD</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg text-center hover:shadow-lg transition">
                        <div class="text-2xl mb-2">👨‍👩‍👧‍👦</div>
                        <p class="font-medium text-gray-900 dark:text-white">Family & Relationships</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg text-center hover:shadow-lg transition">
                        <div class="text-2xl mb-2">🔥</div>
                        <p class="font-medium text-gray-900 dark:text-white">Stress & Burnout</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        How It Works
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Getting started is easy and takes just a few minutes
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">1</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Create Account</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Sign up in minutes and tell us about your therapy needs
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">2</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Choose Therapist</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Browse profiles and select a therapist that matches your needs
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">3</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Start Your Journey</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Book your first session and begin your path to better mental health
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Ready to Start Your Healing Journey?
                </h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Take the first step towards better mental health today. Our therapists are here to support you.
                </p>
                <Link
                    :href="canLogin && !$page.props.auth.user ? route('register') : route('login')"
                    class="inline-block px-8 py-4 text-lg font-semibold text-indigo-600 bg-white rounded-lg hover:bg-gray-100 shadow-lg hover:shadow-xl transition"
                >
                    Get Started Now
                </Link>
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
                            <li><a href="#features" class="hover:text-indigo-400 transition">Features</a></li>
                            <li><Link :href="route('contact')" class="hover:text-indigo-400 transition">Contact Us</Link></li>
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
