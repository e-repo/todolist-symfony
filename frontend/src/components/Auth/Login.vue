<template>
    <v-container
            class="fill-height"
            fluid
    >
        <v-row
            align="center"
            justify="center"
        >
            <v-col
                cols="12"
                sm="8"
                md="6"
            >
                <v-card class="elevation-12">
                    <v-toolbar
                        color="primary"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{formTitle}}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-text-field
                                    label="Логин"
                                    v-model="login"
                                    :rules="loginRules"
                                    prepend-icon="mdi-account"
                                    name="login"
                                    type="text"
                                    required
                            />
                            <v-text-field
                                    ref="password"
                                    label="Пароль"
                                    v-model="password"
                                    :rules="passwordRules"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    required
                            />
                            <v-text-field
                                    label="Повтор пароля"
                                    v-model="confirmPassword"
                                    :rules="confirmPasswordRules"
                                    name="confirmPassword"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    required
                            />
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn
                                :disabled="!valid"
                                color="success"
                                @click="formValidate"
                        >
                            Войти
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {
        name: 'Login',

        data: () => ({
            formTitle: 'Вход',
            valid: false,

            login: '',
            loginRules: [
                v => !!v || 'Поле обязательно к заполнению!',
                v => (v && v.length >= 4) || 'Минимальное кол-во символов - 4'
            ],

            password: '',
            passwordRules: [
                v => !!v || 'Поле обязательно к заполнению!',
                v => (v && v.length >= 6) || 'Минимальное кол-во символов - 6',
                v => !(/[а-яё]+/i).test(v) || 'Возможны только латинские буквы!'
            ],

            confirmPassword: ''
        }),
        methods: {
            formValidate() {
                this.$refs.form.validate()
            }
        },
        computed: {
            confirmPasswordRules() {
                return [
                    () => (this.password === this.confirmPassword) || 'Пароли не совпадают!',
                    v => !!v || 'Поле обязательно к заполнению!',
                ]
            }
        }
    }
</script>
