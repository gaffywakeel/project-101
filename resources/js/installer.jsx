import React from "react";
import {createRoot} from "react-dom/client";
import store from "@/redux/store/installer";
import Localization from "@/components/Localization";
import Bootstrap from "@/components/Bootstrap";
import Router from "@/components/Router";
import ErrorBoundary from "@/components/ErrorBoundary";
import PageRefresh from "@/components/PageRefresh";
import {defineMessages} from "react-intl";
import {Provider} from "react-redux";
import globeSimpleIcon from "@iconify/icons-ph/globe-simple-fill";
import wrenchIcon from "@iconify/icons-ph/wrench-fill";

import "@/scss/global.scss";

const installer = () => import("@/pages/installer/page");

const getRoutes = () => [
    {
        key: "installer",
        icon: wrenchIcon,
        path: "installer/*",
        descriptor: messages.installer,
        lazy: installer,
        errorElement: <ErrorBoundary />
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
    installer: {defaultMessage: "Installer"},
    admin: {defaultMessage: "Admin"},
    index: {defaultMessage: "Index"}
});
