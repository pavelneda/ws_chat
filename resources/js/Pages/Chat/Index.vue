<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: "Index",
    components: {PrimaryButton, SecondaryButton, Head, AuthenticatedLayout},

    props: ['users'],
}
</script>

<template>
    <Head title="Chats"/>

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="flex gap-x-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Chats
                    </h2>
                    133
                </div>
                <div class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Users
                    </h2>
                    <div v-if="users" class="mt-4">
                        <div v-for="user in users" class="flex items-center mt-2 pt-2 border-t border-gray-500">
                            <p>{{ user.name }}</p>
                            <PrimaryButton class="bg-sky-500 ml-4">Message</PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
