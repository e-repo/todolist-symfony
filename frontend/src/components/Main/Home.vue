<template>
    <v-container>
        <v-row>
            <v-col>
                <h2 class="text-center">Home page</h2>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {
        name: 'Home',

        data: () => ({

        }),
        mounted() {
            console.log(process.env)
        }
    }
</script>
