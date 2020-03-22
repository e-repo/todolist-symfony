<template>
    <v-app>


        <div>

            <v-app-bar
                    color="primary"
                    dark
            >
                <v-app-bar-nav-icon
                        @click="drawer = !drawer"
                        class="hidden-md-and-up"
                >
                </v-app-bar-nav-icon>

                <v-toolbar-title>
                    <router-link to="/" tag="span" class="pointer">{{title}}</router-link>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-toolbar-items
                        class="hidden-sm-and-down"
                >

                    <v-btn
                        v-for="(link, index) in links"
                        :key="index"
                        :to="link.url"
                        text
                    >
                        <v-icon left>mdi-{{link.icon}}</v-icon>
                        {{link.title}}
                    </v-btn>

                </v-toolbar-items>

            </v-app-bar>

        </div>

        <v-content>

            <v-navigation-drawer
                    class="hidden-md-and-up"
                    v-model="drawer"
                    absolute
                    left
            >
                <template v-slot:prepend>

                    <v-list-item two-line>

                        <v-list-item-avatar>
                            <img :src="currentUser.avatarLink">
                        </v-list-item-avatar>

                        <v-list-item-content>
                            <v-list-item-title>{{currentUser.name}}</v-list-item-title>
                            <v-list-item-subtitle>{{currentUser.role}}</v-list-item-subtitle>
                        </v-list-item-content>

                    </v-list-item>

                </template>

                <v-divider></v-divider>

                <v-list dense>
                    <v-list-item-group>
                        <v-list-item
                                v-for="(link, index) in links"
                                :key="index"
                                :to="link.url"
                        >
                            <v-list-item-icon>
                                <v-icon>mdi-{{link.icon}}</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>{{link.title}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-item-group>
                </v-list>

            </v-navigation-drawer>

            <router-view></router-view>

        </v-content>

        <v-footer app></v-footer>


    </v-app>
</template>

<script>

    export default {
        name: 'App',

        data: () => ({
            title: 'ToDo',
            drawer: false,
        }),
        computed: {
            links() {
                return [
                    {title: 'Войти', icon: 'login', url: '/login'},
                    {title: 'Регистрация', icon: 'account', url: '/registration'},
                ]
            },
            currentUser() {
                return {
                    avatarLink: 'https://i.pravatar.cc/300',
                    name: 'John Smith',
                    role: 'administrator',
                }
            },
        }
    };
</script>

<style lang="scss">
    @import "./style/common.scss";
</style>


