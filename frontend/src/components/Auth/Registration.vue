<template>
    <v-container class="fill-height justify-center">
        <v-row class="justify-center">
            <v-col class="col-12 col-sm-8 col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title>{{formTitle}}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                                ref="form"
                                v-model="valid"
                                lazy-validation
                        >

                            <v-text-field
                                    label="Имя"
                                    v-model="name"
                                    :rules="nameRules"
                                    prepend-icon="mdi-account"
                                    name="name"
                                    type="text"
                                    :counter="25"
                                    required
                            />
                            <v-text-field
                                    label="Email"
                                    v-model="email"
                                    :rules="emailRules"
                                    prepend-icon="mdi-email"
                                    name="email"
                                    type="text"
                                    required
                            />
                            <v-text-field
                                    label="Логин"
                                    v-model="login"
                                    :rules="loginRules"
                                    prepend-icon="mdi-login-variant"
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
            formTitle: 'Регистрация',
            valid: false,

            name: '',
            nameRules: [
                v => !!v || 'Поле обязательно к заполнению!',
                v => (v && v.length >= 4) || 'Минимальное кол-во символов - 4',
                v => (v && v.length <= 50) || 'Максимальное кол-во символов - 25'
            ],

            email: '',
            emailRules: [
                v => !!v || 'Поле обязательно к заполнению!',
                v => /.+@.+\..+/.test(v) || 'Поле не является email адресом'
            ],

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
