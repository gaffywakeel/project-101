import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import fileList from "@iconify-icons/ri/file-list-2-fill";
import history from "@iconify-icons/ri/history-fill";
import percent from "@iconify-icons/ri/percent-fill";
import WalletsTab from "./_components/Wallets";
import Transactions from "./_components/Transactions";
import Fee from "./_components/Fee";
import PageTabs from "@/components/PageTabs";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    wallets: {defaultMessage: "Wallets"},
    transactions: {defaultMessage: "Transactions"},
    fee: {defaultMessage: "Fee"},
    title: {defaultMessage: "Wallets"}
});

const Wallets = () => {
    const intl = useIntl();

    const tabs = useMemo(() => {
        return [
            {
                value: "wallets",
                label: intl.formatMessage(messages.wallets),
                icon: fileList,
                component: <WalletsTab />
            },
            {
                value: "transactions",
                label: intl.formatMessage(messages.transactions),
                icon: history,
                component: <Transactions />
            },
            {
                value: "fee",
                label: intl.formatMessage(messages.fee),
                icon: percent,
                component: <Fee />
            }
        ];
    }, [intl]);

    return (
        <PageTabs
            initial="wallets"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="view:wallets" fallback={<Result403 />}>
            <Wallets />
        </Authorized>
    );
};

export {Component};
export default Wallets;
