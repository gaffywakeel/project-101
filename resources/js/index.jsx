import React from "react";
import {createRoot} from "react-dom/client";
import {Provider} from "react-redux";
import store from "@/redux/store";
import Localization from "@/components/Localization";
import Bootstrap from "@/components/Bootstrap";
import Router from "@/components/Router";
import PageRefresh from "@/components/PageRefresh";
import ErrorBoundary from "@/components/ErrorBoundary";
import {Navigate} from "react-router-dom";
import Result404 from "@/components/Result404";
import {defineMessages} from "react-intl";
import houseIcon from "@iconify/icons-ph/house-fill";
import keyholeIcon from "@iconify/icons-ph/keyhole-fill";
import userCircleIcon from "@iconify/icons-ph/user-circle-fill";
import identificationBadgeIcon from "@iconify/icons-ph/identification-badge-fill";
import globeSimpleIcon from "@iconify/icons-ph/globe-simple-fill";
import coinsIcon from "@iconify/icons-ph/coins-fill";
import qrCodeIcon from "@iconify/icons-ph/qr-code-fill";
import linkIcon from "@iconify/icons-ph/link-fill";
import listChecksIcon from "@iconify/icons-ph/list-checks";
import usersThreeIcon from "@iconify/icons-ph/users-three-fill";
import currencyCircleDollarIcon from "@iconify/icons-ph/currency-circle-dollar-fill";
import contactlessPaymentIcon from "@iconify/icons-ph/contactless-payment-fill";
import broadcastIcon from "@iconify/icons-ph/broadcast-fill";
import clipboardTextIcon from "@iconify/icons-ph/clipboard-text-fill";
import rocketLaunchIcon from "@iconify/icons-ph/rocket-launch-fill";
import vaultIcon from "@iconify/icons-ph/vault-fill";
import xCircleIcon from "@iconify/icons-ph/x-circle-fill";
import gaugeIcon from "@iconify/icons-ph/gauge-fill";

import "@/scss/global.scss";

const main = () => import("@/pages/main/page");

const mainHome = () => import("@/pages/main/home/page");
const mainProfile = () => import("@/pages/main/profile/page");
const mainPayments = () => import("@/pages/main/payments/page");
const mainWallets = () => import("@/pages/main/wallets/page");
const mainUser = () => import("@/pages/main/user/page");

const mainCommerce = () => import("@/pages/main/commerce/page");
const mainCommerceAccount = () => import("@/pages/main/commerce/account/page");
const mainCommerceDashboard = () =>
    import("@/pages/main/commerce/dashboard/page");
const mainCommerceTransactions = () =>
    import("@/pages/main/commerce/transactions/page");
const mainCommercePayments = () =>
    import("@/pages/main/commerce/payments/page");
const mainCommercePayment = () =>
    import("@/pages/main/commerce/payments/payment/page");
const mainCommerceCustomers = () =>
    import("@/pages/main/commerce/customers/page");
const mainCommerceCustomer = () =>
    import("@/pages/main/commerce/customers/customer/page");

const mainExchange = () => import("@/pages/main/exchange/page");
const mainExchangeTrade = () => import("@/pages/main/exchange/trade/page");
const mainExchangeSwap = () => import("@/pages/main/exchange/swap/page");

const mainPeer = () => import("@/pages/main/peer/page");
const mainPeerTrades = () => import("@/pages/main/peer/trades/page");
const mainPeerTrade = () => import("@/pages/main/peer/trades/trade/page");
const mainPeerCreateOffer = () => import("@/pages/main/peer/create-offer/page");
const mainPeerBuyCrypto = () => import("@/pages/main/peer/buy-crypto/page");
const mainPeerSellCrypto = () => import("@/pages/main/peer/sell-crypto/page");
const mainPeerOffer = () => import("@/pages/main/peer/offer/page");

const mainStake = () => import("@/pages/main/stake/page");
const mainStakeManage = () => import("@/pages/main/stake/manage/page");
const mainStakePlans = () => import("@/pages/main/stake/plans/page");

const mainUserAccount = () => import("@/pages/main/user/account/page");
const commercePayment = () => import("@/pages/payment/page");

const getRoutes = () => [
    {
        key: "admin",
        icon: gaugeIcon,
        path: "admin/*",
        descriptor: messages.admin,
        element: <PageRefresh />
    },
    {
        key: "auth",
        icon: keyholeIcon,
        path: "auth/*",
        descriptor: messages.auth,
        element: <PageRefresh />
    },
    {
        key: "main",
        icon: globeSimpleIcon,
        path: "main",
        descriptor: messages.main,
        lazy: main,
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
                        icon: houseIcon,
                        path: "home",
                        descriptor: messages.home,
                        lazy: mainHome
                    },
                    {
                        key: "user",
                        icon: userCircleIcon,
                        path: "user",
                        descriptor: messages.user,
                        lazy: mainUser,
                        children: [
                            {
                                key: "index",
                                element: <Navigate to="account" />,
                                index: true
                            },
                            {
                                key: "account",
                                icon: userCircleIcon,
                                path: "account",
                                descriptor: messages.account,
                                lazy: mainUserAccount
                            }
                        ]
                    },
                    {
                        key: "profile",
                        icon: identificationBadgeIcon,
                        path: "profile/:name",
                        descriptor: messages.profile,
                        lazy: mainProfile
                    },
                    {
                        key: "wallets",
                        icon: coinsIcon,
                        path: "wallets",
                        descriptor: messages.wallets,
                        lazy: mainWallets
                    },
                    {
                        key: "commerce",
                        icon: qrCodeIcon,
                        path: "commerce",
                        descriptor: messages.commerce,
                        lazy: mainCommerce,
                        children: [
                            {
                                key: "index",
                                element: <Navigate to="dashboard" />,
                                index: true
                            },
                            {
                                key: "dashboard",
                                icon: gaugeIcon,
                                path: "dashboard",
                                descriptor: messages.dashboard,
                                lazy: mainCommerceDashboard
                            },
                            {
                                key: "transactions",
                                icon: listChecksIcon,
                                path: "transactions",
                                descriptor: messages.transactions,
                                lazy: mainCommerceTransactions
                            },
                            {
                                key: "payments",
                                icon: linkIcon,
                                path: "payments",
                                descriptor: messages.payments,
                                children: [
                                    {
                                        key: "index",
                                        lazy: mainCommercePayments,
                                        index: true
                                    },
                                    {
                                        key: "payment",
                                        icon: linkIcon,
                                        path: ":payment",
                                        descriptor: messages.payment,
                                        lazy: mainCommercePayment
                                    }
                                ]
                            },
                            {
                                key: "customers",
                                icon: usersThreeIcon,
                                path: "customers",
                                descriptor: messages.customers,
                                children: [
                                    {
                                        key: "index",
                                        lazy: mainCommerceCustomers,
                                        index: true
                                    },
                                    {
                                        key: "customer",
                                        icon: userCircleIcon,
                                        path: ":customer",
                                        descriptor: messages.customer,
                                        lazy: mainCommerceCustomer
                                    }
                                ]
                            },
                            {
                                key: "account",
                                icon: identificationBadgeIcon,
                                path: "account",
                                descriptor: messages.account,
                                lazy: mainCommerceAccount
                            }
                        ]
                    },
                    {
                        key: "exchange",
                        icon: currencyCircleDollarIcon,
                        path: "exchange",
                        descriptor: messages.exchange,
                        lazy: mainExchange,
                        children: [
                            {
                                key: "index",
                                element: <Navigate to="trade" />,
                                index: true
                            },
                            {
                                key: "trade",
                                icon: currencyCircleDollarIcon,
                                path: "trade",
                                descriptor: messages.trade,
                                lazy: mainExchangeTrade
                            },
                            {
                                key: "swap",
                                icon: currencyCircleDollarIcon,
                                path: "swap",
                                descriptor: messages.swap,
                                lazy: mainExchangeSwap
                            }
                        ]
                    },
                    {
                        key: "peer",
                        icon: usersThreeIcon,
                        path: "peer",
                        descriptor: messages.peer,
                        lazy: mainPeer,
                        children: [
                            {
                                key: "index",
                                element: <Navigate to="trades" />,
                                index: true
                            },
                            {
                                key: "buy-crypto",
                                icon: broadcastIcon,
                                path: "buy-crypto",
                                descriptor: messages.buyCrypto,
                                lazy: mainPeerBuyCrypto
                            },
                            {
                                key: "sell-crypto",
                                icon: broadcastIcon,
                                path: "sell-crypto",
                                descriptor: messages.sellCrypto,
                                lazy: mainPeerSellCrypto
                            },
                            {
                                key: "offer",
                                icon: broadcastIcon,
                                path: "offers/:offer",
                                descriptor: messages.offer,
                                lazy: mainPeerOffer
                            },
                            {
                                key: "trades",
                                icon: usersThreeIcon,
                                path: "trades",
                                descriptor: messages.trades,
                                children: [
                                    {
                                        key: "index",
                                        lazy: mainPeerTrades,
                                        index: true
                                    },
                                    {
                                        key: "trade",
                                        icon: currencyCircleDollarIcon,
                                        path: ":trade",
                                        descriptor: messages.trade,
                                        lazy: mainPeerTrade
                                    }
                                ]
                            },
                            {
                                key: "create-offer",
                                icon: clipboardTextIcon,
                                path: "create-offer",
                                descriptor: messages.createOffer,
                                lazy: mainPeerCreateOffer
                            }
                        ]
                    },
                    {
                        key: "stake",
                        icon: rocketLaunchIcon,
                        path: "stake",
                        descriptor: messages.stake,
                        lazy: mainStake,
                        children: [
                            {
                                key: "index",
                                element: <Navigate to="manage" />,
                                index: true
                            },
                            {
                                key: "manage",
                                icon: rocketLaunchIcon,
                                path: "manage",
                                descriptor: messages.manage,
                                lazy: mainStakeManage
                            },
                            {
                                key: "plans",
                                icon: broadcastIcon,
                                path: "plans",
                                descriptor: messages.plans,
                                lazy: mainStakePlans
                            }
                        ]
                    },
                    {
                        key: "payments",
                        icon: vaultIcon,
                        path: "payments",
                        descriptor: messages.payments,
                        lazy: mainPayments
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
        path: "/",
        descriptor: messages.index,
        errorElement: <ErrorBoundary />,
        children: [
            {
                key: "index",
                element: <Navigate to="main" />,
                index: true
            },
            {
                key: "payment",
                icon: contactlessPaymentIcon,
                path: "payments/:payment",
                descriptor: messages.commercePayment,
                lazy: commercePayment
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
    main: {defaultMessage: "Main"},
    index: {defaultMessage: "Index"},
    home: {defaultMessage: "Home"},
    user: {defaultMessage: "User"},
    account: {defaultMessage: "Account"},
    profile: {defaultMessage: "Profile"},
    userSetup: {defaultMessage: "User Setup"},
    settings: {defaultMessage: "Settings"},
    help: {defaultMessage: "Help"},
    wallets: {defaultMessage: "Wallets"},
    commerce: {defaultMessage: "Commerce"},
    dashboard: {defaultMessage: "Dashboard"},
    commercePayment: {defaultMessage: "Commerce Payment"},
    payment: {defaultMessage: "Payment"},
    transactions: {defaultMessage: "Transactions"},
    payments: {defaultMessage: "Payments"},
    customer: {defaultMessage: "Customer"},
    customers: {defaultMessage: "Customers"},
    exchange: {defaultMessage: "Exchange"},
    trades: {defaultMessage: "Trades"},
    swap: {defaultMessage: "Swap"},
    peer: {defaultMessage: "Peer"},
    buyCrypto: {defaultMessage: "Buy Crypto"},
    sellCrypto: {defaultMessage: "Sell Crypto"},
    createOffer: {defaultMessage: "Create Offer"},
    offer: {defaultMessage: "Offer"},
    trade: {defaultMessage: "Trade"},
    stake: {defaultMessage: "Stake"},
    manage: {defaultMessage: "Manage"},
    plans: {defaultMessage: "Plans"},
    checkout: {defaultMessage: "Checkout"},
    shop: {defaultMessage: "Shop"},
    limits: {defaultMessage: "Limits"},
    admin: {defaultMessage: "Admin"},
    notFound: {defaultMessage: "Not Found"},
    auth: {defaultMessage: "Auth"}
});
