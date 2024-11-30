<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

export default {
    name: "Index",
    components: {TextInput, PrimaryButton, SecondaryButton, Head, AuthenticatedLayout, Link},

    props: ['users', 'chats'],

    data() {
        return {
            isGroup: null,
            groupTitle: '',
            userIdsGroup: [],
        }
    },

    created() {
        Echo.private(`user.${this.$page.props.auth.user.id}`)
            .listen('.store-message-status', res => {
                this.chats.filter(chat => {
                    if (chat.id === res.chat_id) {
                        chat.unreadable_message_status_count = res.count;
                        chat.last_message = res.message;
                    }
                })
            })
    },

    methods: {
        store(id) {
            this.$inertia.post(route('chats.store'), {
                title: null,
                users: [id],
            });
        },

        storeGroup() {
            if (!this.groupTitle && this.userIdsGroup.length >= 1) return;

            this.$inertia.post(route('chats.store'), {
                title: this.groupTitle,
                users: this.userIdsGroup,
                is_group: true,
            });
        },

        refreshGroup() {
            this.isGroup = false;
            this.groupTitle = '';
            this.userIdsGroup = [];
        },

        toggleUser(id) {
            const index = this.userIdsGroup.indexOf(id);
            if (index === -1) {
                this.userIdsGroup.push(id);
            } else {
                this.userIdsGroup.splice(index, 1);
            }
        },

    },
}
</script>

<template>
    <Head title="Chats"/>

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="flex items-start gap-x-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Chats
                    </h2>
                    <div v-if="chats" class="mt-4">
                        <div v-for="chat in chats" class="mt-4 pt-2 border-t border-gray-500">
                            <Link :href="route('chats.show', chat.id)">
                                <p>{{ chat.title }}</p>
                                <div :class="['flex items-center bg-gray-100 rounded-2xl p-4',
                                    chat.unreadable_message_status_count ? 'bg-sky-100' : '']">
                                    <div v-if="chat.last_message" class="text-sm">
                                        <p class="text-gray-500">{{ chat.last_message.user.name }} <span class="text-gray-500 italic">{{ chat.last_message.time }}</span>
                                        </p>
                                        <p>{{ chat.last_message.body }}</p>
                                    </div>
                                    <span v-if="chat.unreadable_message_status_count"
                                          class="ml-auto bg-sky-500 px-2 text-white rounded-full">{{
                                            chat.unreadable_message_status_count
                                        }}</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            Users
                        </h2>
                        <PrimaryButton v-if="!isGroup" @click="isGroup = true">Make group</PrimaryButton>
                        <div v-if="isGroup">
                            <TextInput type="text"
                                       class="mr-4 py-1"
                                       v-model="groupTitle">
                            </TextInput>
                            <PrimaryButton @click="storeGroup"
                                           :class="this.groupTitle && this.userIdsGroup.length >= 1 ? 'bg-sky-500' : '!bg-gray-500 cursor-not-allowed'">
                                Go chat
                            </PrimaryButton>
                            <PrimaryButton @click="refreshGroup" class="bg-red-500 ml-4">X</PrimaryButton>
                        </div>
                    </div>
                    <div v-if="users" class="mt-4">
                        <div v-for="user in users"
                             class="flex items-center justify-between mt-2 pt-2 border-t border-gray-500">
                            <div class="flex items-center">
                                <p>{{ user.name }}</p>
                                <PrimaryButton @click="store(user.id)" class="bg-sky-500 ml-4">Message</PrimaryButton>
                            </div>
                            <input v-if="isGroup" @click="toggleUser(user.id)" type="checkbox">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
