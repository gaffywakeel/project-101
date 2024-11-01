import React from "react";
import {createRoot} from "react-dom/client";
import {Provider} from "react-redux";
import store from "@/redux/store/auth";
import Localization from "@/components/Localization";
import Bootstrap from "@/components/Bootstrap";
import Router from "@/components/Router";
import ErrorBoundary from "@/components/ErrorBoundary";
import {Navigate} from "react-router-dom";
import Result404 from "@/components/Result404";
import PageRefresh from "@/components/PageRefresh";
import {defineMessages} from "react-intl";
import signInIcon from "@iconify/icons-ph/sign-in-fill";
import globeSimpleIcon from "@iconify/icons-ph/globe-simple-fill";
import lockKeyOpenIcon from "@iconify/icons-ph/lock-key-open-fill";
import userPlusIcon from "@iconify/icons-ph/user-plus-fill";
import keyholeIcon from "@iconify/icons-ph/keyhole-fill";
import xCircleIcon from "@iconify/icons-ph/x-circle-fill";

import "@/scss/global.scss";

const auth = () => import("@/pages/auth/page");
const authLogin = () => import("@/pages/auth/login/page");
const authForgotPassword = () => import("@/pages/auth/forgot-password/page");
const authRegister = () => import("@/pages/auth/register/page");

const getRoutes = () => [
    {
        key: "auth",
        icon: keyholeIcon,
        path: "auth",
        descriptor: messages.auth,
        lazy: auth,
        errorElement: <ErrorBoundary />,
        children: [
            {
                key: "index",
                element: <Navigate to="login" />,
                index: true
            },
            {
                errorElement: <ErrorBoundary />,
                children: [
                    {
                        key: "login",
                        icon: signInIcon,
                        path: "login",
                        descriptor: messages.login,
                        lazy: authLogin
                    },
                    {
                        key: "forgot-password",
                        icon: lockKeyOpenIcon,
                        path: "forgot-password",
                        descriptor: messages.forgotPassword,
                        lazy: authForgotPassword
                    },
                    {
                        key: "register",
                        icon: userPlusIcon,
                        path: "register",
                        descriptor: messages.register,
                        lazy: authRegister
                    },
                    {
                        key: "404",
                        icon: xCircleIcon,
                        path: "*",
                        descriptor: messages.notFound,
                        element: <Result404 />
                    }
                ]
            }
        ]
    },
    {
        key: "index",
        icon: globeSimpleIcon,
        path: "*",
        descriptor: messages.index,
        element: <PageRefresh />
    }
];

const root = createRoot(document.getElementById("root"));

root.render(
    <Provider store={store}>
        <Localization>
            <Bootstrap>
                <Router getRoutes={getRoutes} />
            </Bootstrap>
        </Localization>
    </Provider>
);

const messages = defineMessages({
    auth: {defaultMessage: "Auth"},
    register: {defaultMessage: "Register"},
    login: {defaultMessage: "Login"},
    forgotPassword: {defaultMessage: "Forgot Password"},
    notFound: {defaultMessage: "Not Found"},
    admin: {defaultMessage: "Admin"},
    index: {defaultMessage: "Index"}
});
