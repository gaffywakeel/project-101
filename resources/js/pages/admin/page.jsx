import React, {useEffect, useMemo} from "react";
import {Dashboard, DashboardContent} from "../_components/styled";
import Language from "@/components/Language";
import Notifications from "@/components/Notifications";
import UserArea from "./_components/UserArea";
import Sidebar from "../_components/Sidebar";
import {defineMessages, useIntl} from "react-intl";
import {Chip} from "@mui/material";
import {Outlet, ScrollRestoration} from "react-router-dom";
import {useSidebarToggle} from "@/hooks/useSidebarToggle";
import {first} from "lodash";
import {useAuth} from "@/models/Auth";
import {usePrivateBroadcast} from "@/services/Broadcast";
import useSidebarItem from "@/hooks/useSidebarItem";
import PresenceTimer from "@/components/PresenceTimer";
import Header from "../_components/Header";
import useNavbarDrawer from "@/hooks/useNavbarDrawer";
import LoadingNavigation from "@/components/LoadingNavigation";
import {
    fetchCountries,
    fetchOperatingCountries,
    fetchSupportedCurrencies,
    fetchWallets
} from "@/redux/slices/global";
import {useDispatch} from "react-redux";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    dashboard: {defaultMessage: "Dashboard"},
    configuration: {defaultMessage: "Configuration"},
    marketplace: {defaultMessage: "Marketplace"}
});

const Admin = () => {
    const intl = useIntl();
    const getSidebarItem = useSidebarItem();
    const [sidebarState, openSidebar, closeSidebar] = useSidebarToggle();
    const {collapseClick} = useNavbarDrawer();
    const dispatch = useDispatch();
    const auth = useAuth();

    usePrivateBroadcast("App.Models.User." + auth.user.id);

    useEffect(() => {
        dispatch(fetchCountries());
        dispatch(fetchSupportedCurrencies());
        dispatch(fetchOperatingCountries());
        dispatch(fetchWallets());
    }, [dispatch]);

    const links = useMemo(() => {
        return [
            {
                key: "dashboard",
                title: intl.formatMessage(messages.dashboard),
                items: [
                    getSidebarItem({
                        key: "admin.home",
                        permission: "access:control_panel"
                    }),
                    getSidebarItem({
                        key: "admin.users",
                        permission: "manage:users"
                    }),
                    getSidebarItem({
                        key: "admin.payments",
                        permission: "manage:payments"
                    }),
                    getSidebarItem({
                        key: "admin.wallets",
                        permission: "view:wallets"
                    }),
                    getSidebarItem({
                        key: "admin.commerce",
                        permission: "manage:commerce"
                    })
                ]
            },
            {
                key: "marketplace",
                title: intl.formatMessage(messages.marketplace),
                items: [
                    getSidebarItem({
                        key: "admin.peer",
                        permission: "manage:peer_trades"
                    }),
                    getSidebarItem({
                        key: "admin.exchange",
                        permission: "manage:exchange"
                    }),
                    getSidebarItem({
                        key: "admin.stake",
                        permission: "manage:stakes"
                    })
                ]
            },
            {
                key: "configuration",
                title: intl.formatMessage(messages.configuration),
                items: [
                    getSidebarItem({
                        key: "admin.settings",
                        permission: "manage:settings"
                    }),
                    getSidebarItem({
                        key: "admin.verification",
                        permission: "manage:settings"
                    }),
                    getSidebarItem({
                        key: "admin.modules",
                        permission: "manage:modules"
                    }),
                    getSidebarItem({
                        key: "admin.customization",
                        permission: "manage:customization"
                    }),
                    getSidebarItem({
                        key: "admin.localization",
                        permission: "manage:localization"
                    })
                ]
            }
        ];
    }, [intl, getSidebarItem]);

    return (
        <Dashboard>
            <PresenceTimer />
            <LoadingNavigation />

            <Header
                onOpenSidebar={openSidebar}
                actions={[
                    <Language key="language" />,
                    <Notifications key="notification" />,
                    <UserArea key="userarea" />
                ]}
            />

            <Sidebar
                isOpenSidebar={sidebarState}
                onCloseSidebar={closeSidebar}
                links={links}
                action={
                    <Chip
                        variant="outlined"
                        size="small"
                        label={first(auth.user.all_roles)}
                        sx={{mt: 0.5}}
                    />
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
        <Authorized permission="access:control_panel" fallback={<Result403 />}>
            <Admin />
        </Authorized>
    );
};

export {Component};
export default Admin;
