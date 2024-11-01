import React from "react";
import {createRoot} from "react-dom/client";
import store from "@/redux/store/admin";
import Localization from "@/components/Localization";
import Bootstrap from "@/components/Bootstrap";
import Router from "@/components/Router";
import {Provider} from "react-redux";
import ErrorBoundary from "@/components/ErrorBoundary";
import {Navigate} from "react-router-dom";
import Result404 from "@/components/Result404";
import PageRefresh from "@/components/PageRefresh";
import {defineMessages} from "react-intl";
import gaugeIcon from "@iconify/icons-ph/gauge-fill";
import homeIcon from "@iconify/icons-ph/house-fill";
import coinsIcon from "@iconify/icons-ph/coins-fill";
import usersIcon from "@iconify/icons-ph/users-fill";
import qrCodeIcon from "@iconify/icons-ph/qr-code-fill";
import usersThreeIcon from "@iconify/icons-ph/users-three-fill";
import shieldStarIcon from "@iconify/icons-ph/shield-star-fill";
import vaultIcon from "@iconify/icons-ph/vault-fill";
import currencyCircleDollarIcon from "@iconify/icons-ph/currency-circle-dollar-fill";
import rocketLaunchIcon from "@iconify/icons-ph/rocket-launch-fill";
import gearSixIcon from "@iconify/icons-ph/gear-six-fill";
import puzzlePieceIcon from "@iconify/icons-ph/puzzle-piece-fill";
import translateIcon from "@iconify/icons-ph/translate-fill";
import paintBrushBroadIcon from "@iconify/icons-ph/paint-brush-broad-fill";
import xCircleIcon from "@iconify/icons-ph/x-circle-fill";
import globeSimpleIcon from "@iconify/icons-ph/globe-simple-fill";

import "@/scss/global.scss";

const admin = () => import("@/pages/admin/page");
const adminHome = () => import("@/pages/admin/home/page");
const adminCommerce = () => import("@/pages/admin/commerce/page");
const adminWallets = () => import("@/pages/admin/wallets/page");
const adminPayments = () => import("@/pages/admin/payments/page");
const adminUsers = () => import("@/pages/admin/users/page");
const adminExchange = () => import("@/pages/admin/exchange/page");
const adminPeer = () => import("@/pages/admin/peer/page");
const adminStake = () => import("@/pages/admin/stake/page");
const adminSettings = () => import("@/pages/admin/settings/page");
const adminModules = () => import("@/pages/admin/modules/page");
const adminVerification = () => import("@/pages/admin/verification/page");
const adminCustomization = () => import("@/pages/admin/customization/page");
const adminLocalization = () => import("@/pages/admin/localization/page");

const getRoutes = () => [
    {
        key: "admin",
        icon: gaugeIcon,
        path: "admin",
        descriptor: messages.admin,
        lazy: admin,
        errorElement: <ErrorBoundary />,
        children: [
            {
                key: "index",
                element: <Navigate to="home" />,
                index: true
            },
            {
                errorElement: <ErrorBoundary />,
                children: [
                    {
                        key: "home",
                        icon: homeIcon,
                        path: "home",
                        descriptor: messages.dashboard,
                        lazy: adminHome
                    },
                    {
                        key: "wallets",
                        icon: coinsIcon,
                        path: "wallets",
                        descriptor: messages.wallets,
                        lazy: adminWallets
                    },
                    {
                        key: "users",
                        icon: usersIcon,
                        path: "users",
                        descriptor: messages.users,
                        lazy: adminUsers
                    },
                    {
                        key: "commerce",
                        icon: qrCodeIcon,
                        path: "commerce",
                        descriptor: messages.commerce,
                        lazy: adminCommerce
                    },
                    {
                        key: "peer",
                        icon: usersThreeIcon,
                        path: "peer",
                        descriptor: messages.peer,
                        lazy: adminPeer
                    },
                    {
                        key: "verification",
                        icon: shieldStarIcon,
                        path: "verification",
                        descriptor: messages.verification,
                        lazy: adminVerification
                    },
                    {
                        key: "payments",
                        icon: vaultIcon,
                        path: "payments",
                        descriptor: messages.payments,
                        lazy: adminPayments
                    },
                    {
                        key: "exchange",
                        icon: currencyCircleDollarIcon,
                        path: "exchange",
                        descriptor: messages.exchange,
                        lazy: adminExchange
                    },
                    {
                        key: "stake",
                        icon: rocketLaunchIcon,
                        path: "stake",
                        descriptor: messages.stake,
                        lazy: adminStake
                    },
                    {
                        key: "settings",
                        icon: gearSixIcon,
                        path: "settings",
                        descriptor: messages.settings,
                        lazy: adminSettings
                    },
                    {
                        key: "modules",
                        icon: puzzlePieceIcon,
                        path: "modules",
                        descriptor: messages.modules,
                        lazy: adminModules
                    },
                    {
                        key: "localization",
                        icon: translateIcon,
                        path: "localization",
                        descriptor: messages.localization,
                        lazy: adminLocalization
                    },
                    {
                        key: "customization",
                        icon: paintBrushBroadIcon,
                        path: "customization",
                        descriptor: messages.customization,
                        lazy: adminCustomization
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
    admin: {defaultMessage: "Admin"},
    dashboard: {defaultMessage: "Dashboard"},
    wallets: {defaultMessage: "Wallets"},
    users: {defaultMessage: "Users"},
    marketplace: {defaultMessage: "Marketplace"},
    commerce: {defaultMessage: "Commerce"},
    peer: {defaultMessage: "Peer"},
    verification: {defaultMessage: "Verification"},
    payments: {defaultMessage: "Payments"},
    exchange: {defaultMessage: "Exchange"},
    stake: {defaultMessage: "Stake"},
    settings: {defaultMessage: "Settings"},
    modules: {defaultMessage: "Modules"},
    localization: {defaultMessage: "Localization"},
    customization: {defaultMessage: "Customization"},
    developer: {defaultMessage: "Developer"},
    notFound: {defaultMessage: "Not Found"},
    auth: {defaultMessage: "Auth"},
    index: {defaultMessage: "Index"}
});
