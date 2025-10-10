<template>
    <AppLayout title="Messages">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Messages
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex h-[600px]">
                        <!-- Conversations List -->
                        <div class="w-full md:w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Conversations
                                </h3>
                            </div>
                            <div class="flex-1 overflow-y-auto">
                                <div v-if="conversations.length > 0">
                                    <button
                                        v-for="conversation in conversations"
                                        :key="conversation.user.id"
                                        @click="selectConversation(conversation.user)"
                                        :class="[
                                            'w-full p-4 flex items-start hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700',
                                            selectedUserId === conversation.user.id ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''
                                        ]"
                                    >
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-300">
                                                    {{ conversation.user.name.charAt(0) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1 text-left">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ conversation.user.name }}
                                                </p>
                                                <p v-if="conversation.last_message" class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ formatMessageTime(conversation.last_message.created_at) }}
                                                </p>
                                            </div>
                                            <p v-if="conversation.last_message" class="text-sm text-gray-600 dark:text-gray-400 truncate mt-1">
                                                {{ conversation.last_message.content }}
                                            </p>
                                            <div v-if="conversation.unread_count > 0" class="mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                    {{ conversation.unread_count }} unread
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div v-else class="p-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <p>No conversations yet</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div v-if="selectedUser" class="flex-1 flex flex-col">
                            <!-- Chat Header -->
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-300">
                                            {{ selectedUser.name.charAt(0) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ selectedUser.name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ selectedUser.role }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                                <div v-for="message in messages" :key="message.id"
                                     :class="[
                                         'flex',
                                         message.sender_id === currentUserId ? 'justify-end' : 'justify-start'
                                     ]">
                                    <div :class="[
                                        'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                                        message.sender_id === currentUserId
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white'
                                    ]">
                                        <p class="text-sm whitespace-pre-wrap break-words">{{ message.content }}</p>
                                        <p :class="[
                                            'text-xs mt-1',
                                            message.sender_id === currentUserId
                                                ? 'text-indigo-200'
                                                : 'text-gray-500 dark:text-gray-400'
                                        ]">
                                            {{ formatMessageTime(message.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <div v-if="messages.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    <p>No messages yet. Start the conversation!</p>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                <form @submit.prevent="sendMessage" class="flex space-x-2">
                                    <textarea
                                        v-model="messageForm.content"
                                        rows="2"
                                        placeholder="Type your message..."
                                        class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                                        @keydown.enter.exact.prevent="sendMessage"
                                    ></textarea>
                                    <button
                                        type="submit"
                                        :disabled="!messageForm.content.trim() || messageForm.processing"
                                        class="self-end px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- No Conversation Selected -->
                        <div v-else class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <p>Select a conversation to start messaging</p>
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
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { formatDistanceToNow, parseISO } from 'date-fns';

const props = defineProps({
    conversations: Array,
    initialMessages: {
        type: Array,
        default: () => []
    },
    selectedUserId: Number,
});

const page = usePage();
const messagesContainer = ref(null);
const messages = ref(props.initialMessages);
const selectedUser = ref(null);

const messageForm = useForm({
    content: '',
    receiver_id: null,
});

const currentUserId = computed(() => page.props.auth.user.id);

const formatMessageTime = (date) => {
    return formatDistanceToNow(parseISO(date), { addSuffix: true });
};

const selectConversation = (user) => {
    selectedUser.value = user;
    messageForm.receiver_id = user.id;

    // Load messages for this conversation
    router.get(route('messages.index', { user: user.id }), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            messages.value = page.props.initialMessages || [];
            scrollToBottom();
        }
    });
};

const sendMessage = () => {
    if (!messageForm.content.trim()) return;

    messageForm.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset('content');
            // Message will be added to the list via Echo
        },
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

// Set up real-time messaging with Laravel Echo
const setupEcho = () => {
    if (window.Echo) {
        window.Echo.private(`chat.${currentUserId.value}`)
            .listen('MessageSent', (e) => {
                // Only add message if it's from the currently selected conversation
                if (selectedUser.value && (e.message.sender_id === selectedUser.value.id || e.message.receiver_id === selectedUser.value.id)) {
                    messages.value.push(e.message);
                    scrollToBottom();
                }

                // Reload conversations to update unread counts
                router.reload({ only: ['conversations'], preserveScroll: true });
            });
    }
};

onMounted(() => {
    // Select initial conversation if provided
    if (props.selectedUserId) {
        const conversation = props.conversations.find(c => c.user.id === props.selectedUserId);
        if (conversation) {
            selectConversation(conversation.user);
        }
    }

    scrollToBottom();
    setupEcho();
});

watch(messages, () => {
    scrollToBottom();
}, { deep: true });
</script>
