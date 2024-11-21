<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

export default {
    name: "Show",
    components: {InputError, TextInput, PrimaryButton, SecondaryButton, Head, AuthenticatedLayout},

    props: ['chat', 'users'],

    data(){
        return{
            body: null,
            errors: null,
        }
    },

    computed: {
        userIds(){
            return this.users.map(user => user.id).filter(id => id !== this.$page.props.auth.user.id)
        }
    },

    methods: {
        store(){
            axios.post(route('messages.store'), {
                body: this.body,
                chat_id: this.chat.id,
                user_ids: this.userIds,
            }).then(res =>{
                console.log(res.data);

            }).catch(err => {
                this.errors = err.response.data.errors;
            })
        }
    },
}
</script>

<template>
    <Head title="Chat"/>

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="flex gap-x-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="w-3/5 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ chat.title ?? 'Chat' }}
                    </h2>
                    <div class="mt-4">

                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg">Send message</h3>
                        <textarea v-model="body"
                                  class="block w-full mt-2 resize-none rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </textarea>
                        <InputError v-if="errors" class="mt-2" :message="errors" />
                        <PrimaryButton @click="store" class="mt-2">Send</PrimaryButton>
                    </div>
                </div>
                <div class="w-2/5 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Users
                    </h2>
                    <div v-if="users" class="mt-4">
                        <div v-for="user in users" class="flex items-center mt-2 pt-2 border-t border-gray-500">
                            <p>{{ user.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
