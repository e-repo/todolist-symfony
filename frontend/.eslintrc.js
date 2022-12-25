module.exports = {
    root: true,
    parser: "vue-eslint-parser",
    parserOptions: {
        parser: "@typescript-eslint/parser",
    },
    extends: [
        "plugin:vue/vue3-recommended",
        "@vue/typescript/recommended",
    ],
    plugins: ["@typescript-eslint"],
    rules: {
        "semi": ["error", "never"],
        "object-curly-spacing": ["error", "always"],
        "@typescript-eslint/no-var-requires": "off",
        "@typescript-eslint/no-explicit-any": "off",
        "@typescript-eslint/no-inferrable-types": "off",
        "@typescript-eslint/ban-ts-comment": "off",
        "vue/no-multiple-template-root": "off",
    },
}
