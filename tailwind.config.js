import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

const plugin = require("tailwindcss/plugin");

/** @type {import("tailwindcss").Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*{.js,.ts,.vue}",
        "./node_modules/flowbite/**/*.js",
        "./vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php"
    ],

    theme: {
        extend: {
            colors: {
                "primary-50": "#fdedf0",
                "primary-100": "#fad3d9",
                "primary-200": "#f7b6c0",
                "primary-300": "#f499a6",
                "primary-400": "#f18393",
                "primary-500": "#ef6d80",
                "primary-600": "#ed6578",
                "primary-700": "#eb5a6d",
                "primary-800": "#e85063",
                "primary-900": "#e43e50",
                "dark-grey-1": "#757f95",
                "dark-grey-2": "#596273",
                "light-grey-1": "#BABFCA",
                "light-grey-2": "#979FAF"
            },
            gridTemplateColumns: {
                body: "16rem 1fr"
            },
            gridTemplateRows: {
                body: "auto 1fr",
                "order-vertical": "repeat(4, auto) 1fr auto",
                "order-horizontal": "1fr auto"
            },
            zIndex: {
                60: "60"
            },
            boxShadow: {
                "input": "0px 0px 0px 2px rgba(107, 148, 226, 0.2)",
                "button": "0px 0px 0px 2px rgba(236, 63, 89, 0.2)"
            }
        }
    },

    plugins: [
        forms,
        typography,
        require("flowbite/plugin"),
        plugin(({ addVariant, e }) => {
            addVariant("sidebar-expanded", ({ modifySelectors, separator }) => {
                modifySelectors(
                    ({ className }) =>
                        `.sidebar-expanded .${e(
                            `sidebar-expanded${separator}${className}`
                        )}`
                );
            });
        })
    ]
};
