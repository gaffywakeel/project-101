import React, {useEffect, useMemo} from "react";
import {Dashboard, DashboardContent} from "../_components/styled";
import Sidebar from "../_components/Sidebar";
import Account from "./_components/Account";
import Language from "@/components/Language";
import Notifications from "@/components/Notifications";
import {useAuth} from "@/models/Auth";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {Box, Button, Typography} from "@mui/material";
import {Link as RouterLink, Outlet, ScrollRestoration} from "react-router-dom";
import MemoryIcon from "@mui/icons-material/Memory";
import Clock from "@/components/Clock";
import {DocIcon} from "@/assets/index";
import {useSidebarToggle} from "@/hooks/useSidebarToggle";
import {useBrand} from "@/hooks/settings";
import {usePrivateBroadcast} from "@/services/Broadcast";
import useSidebarItem from "@/hooks/useSidebarItem";
import PresenceTimer from "@/components/PresenceTimer";
import Header from "../_components/Header";
import useNavbarDrawer from "@/hooks/useNavbarDrawer";
import LoadingNavigation from "@/components/LoadingNavigation";
import Authenticated from "@/components/Authenticated";
import {useDispatch} from "react-redux";
import {alpha, styled} from "@mui/material/styles";
import {fetchWalletAccounts} from "@/redux/slices/wallet";
import {fetchCommerceAccount} from "@/redux/slices/commerce";
import {fetchPaymentAccount} from "@/redux/slices/payment";
import {fetchVerification} from "@/redux/slices/auth";
import {
    fetchCountries,
    fetchOperatingCountries,
    fetchSupportedCurrencies,
    fetchWallets
} from "@/redux/slices/global";

const messages = defineMessages({
    general: {defaultMessage: "General"},
    marketplace: {defaultMessage: "Marketplace"}
});

const Main = () => {
    const auth = useAuth();
    const brand = useBrand();
    const intl = useIntl();
    const getSidebarItem = useSidebarItem();
    const [sidebarState, openSidebar, closeSidebar] = useSidebarToggle();
    const {collapseClick} = useNavbarDrawer();
    const dispatch = useDispatch();

    usePrivateBroadcast("App.Models.User." + auth.user.id);

    useEffect(() => {
        dispatch(fetchWalletAccounts());
        dispatch(fetchCommerceAccount());
        dispatch(fetchPaymentAccount());
        dispatch(fetchVerification());
    }, [dispatch]);

    useEffect(() => {
        dispatch(fetchCountries());
        dispatch(fetchSupportedCurrencies());
        dispatch(fetchOperatingCountries());
        dispatch(fetchWallets());
    }, [dispatch]);

    const links = useMemo(() => {
        return [
            {
                key: "general",
                title: intl.formatMessage(messages.general),
                items: [
                    getSidebarItem({
                        key: "main.home"
                    }),
                    getSidebarItem({
                        key: "main.profile",
                        params: {name: auth.user.name}
                    }),
                    getSidebarItem({
                        key: "main.payments",
                        module: "payment"
                    }),
                    getSidebarItem({
                        key: "main.wallets",
                        module: "wallet"
                    }),
                    getSidebarItem({
                        module: "commerce",
                        key: "main.commerce",
                        children: [
                            getSidebarItem({
                                key: "main.commerce.dashboard"
                            }),
                            getSidebarItem({
                                key: "main.commerce.transactions"
                            }),
                            getSidebarItem({
                                key: "main.commerce.payments"
                            }),
                            getSidebarItem({
                                key: "main.commerce.customers"
                            }),
                            getSidebarItem({
                                key: "main.commerce.account"
                            })
                        ]
                    })
                ]
            },
            {
                key: "marketplace",
                title: intl.formatMessage(messages.marketplace),
                items: [
                    getSidebarItem({
                        module: "peer",
                        key: "main.peer",
                        children: [
                            getSidebarItem({
                                key: "main.peer.buy-crypto"
                            }),
                            getSidebarItem({
                                key: "main.peer.sell-crypto"
                            }),
                            getSidebarItem({
                                key: "main.peer.create-offer"
                            }),
                            getSidebarItem({
                                key: "main.peer.trades"
                            })
                        ]
                    }),
                    getSidebarItem({
                        module: "exchange",
                        key: "main.exchange",
                        children: [
                            getSidebarItem({
                                key: "main.exchange.trade"
                            }),
                            getSidebarItem({
                                key: "main.exchange.swap"
                            })
                        ]
                    }),
                    getSidebarItem({
                        module: "stake",
                        key: "main.stake",
                        children: [
                            getSidebarItem({
                                key: "main.stake.plans"
                            }),
                            getSidebarItem({
                                key: "main.stake.manage"
                            })
                        ]
                    })
                ]
            }
        ];
    }, [intl, auth, getSidebarItem]);

    return (
        <Dashboard>
            <PresenceTimer />
            <LoadingNavigation />

            <Header
                onOpenSidebar={openSidebar}
                actions={[
                    <Language key="language" />,
                    <Notifications key="notifications" />,
                    <Account key="account" />
                ]}
            />

            <Sidebar
                isOpenSidebar={sidebarState}
                onCloseSidebar={closeSidebar}
                links={links}
                action={
                    auth.can("access:control_panel") ? (
                        <Button
                            color="primary"
                            component={RouterLink}
                            variant="contained"
                            sx={{boxShadow: "none", mt: 0.5}}
                            startIcon={<MemoryIcon />}
                            to={"/admin"}
                            size="small">
                            <FormattedMessage defaultMessage="Control Panel" />
                        </Button>
                    ) : (
                        <Typography
                            variant="caption"
                            sx={{color: "text.secondary"}}
                            noWrap={true}>
                            <Clock />
                        </Typography>
                    )
                }
                extras={
                    <ExtraContent sx={{mx: 3, mb: 3, mt: 10}}>
                        <DocIcon sx={{width: 30, mb: 1}} />

                        <Typography
                            variant="subtitle1"
                            sx={{color: "grey.700"}}
                            gutterBottom>
                            <FormattedMessage defaultMessage="Need some help?" />
                        </Typography>

                        <Typography
                            variant="body2"
                            sx={{mb: 2, color: "grey.600"}}>
                            <FormattedMessage defaultMessage="Contact one of our support agents." />
                        </Typography>

                        <Button
                            target="_blank"
                            href={brand.support_url}
                            variant="contained"
                            fullWidth>
                            <FormattedMessage defaultMessage="Get Support" />
                        </Button>
                    </ExtraContent>
                }
            />

            <DashboardContent collapseClick={collapseClick}>
                <Outlet />
                <ScrollRestoration />
            </DashboardContent>
        </Dashboard>
    );
};

const Component = () => {
    return (
        <Authenticated>
            <Main />
        </Authenticated>
    );
};

const ExtraContent = styled(Box)(({theme}) => ({
    padding: theme.spacing(2.5),
    borderRadius: theme.shape.borderRadius,
    backgroundColor:
        theme.palette.mode === "light"
            ? alpha(theme.palette.primary.main, 0.08)
            : theme.palette.primary.lighter
}));

export {Component};
export default Main;
