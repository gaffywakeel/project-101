import React, {useMemo} from "react";
import PageTabs from "@/components/PageTabs";
import {defineMessages, useIntl} from "react-intl";
import exchangeDollar from "@iconify-icons/ri/exchange-dollar-fill";
import exchange from "@iconify-icons/ri/exchange-fill";
import Trade from "./_components/Trade";
import percent from "@iconify-icons/ri/percent-fill";
import Fee from "./_components/Fee";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";
import Swap from "./_components/Swap";
import {useAuth} from "@/models/Auth";

const messages = defineMessages({
    trade: {defaultMessage: "Trades"},
    fee: {defaultMessage: "Fee"},
    title: {defaultMessage: "Exchange"},
    swap: {defaultMessage: "Swaps"}
});

const Exchange = () => {
    const intl = useIntl();
    const auth = useAuth();

    const tabs = useMemo(() => {
        const tabs = [];

        tabs.push({
            value: "trade",
            label: intl.formatMessage(messages.trade),
            icon: exchangeDollar,
            component: <Trade />
        });

        if (auth.can("view:exchange_swaps")) {
            tabs.push({
                value: "swap",
                label: intl.formatMessage(messages.swap),
                icon: exchange,
                component: <Swap />
            });
        }

        tabs.push({
            value: "fee",
            label: intl.formatMessage(messages.fee),
            icon: percent,
            component: <Fee />
        });

        return tabs;
    }, [intl, auth]);

    return (
        <PageTabs
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
            initial="trade"
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:exchange" fallback={<Result403 />}>
            <Exchange />
        </Authorized>
    );
};

export {Component};
export default Exchange;
