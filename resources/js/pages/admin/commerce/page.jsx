import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import PageTabs from "@/components/PageTabs";
import history from "@iconify-icons/ri/history-fill";
import Transactions from "./_components/Transactions";
import percent from "@iconify-icons/ri/percent-fill";
import Fee from "./_components/Fee";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Commerce"},
    transactions: {defaultMessage: "Transactions"},
    fee: {defaultMessage: "Fee"}
});

const Commerce = () => {
    const intl = useIntl();

    const tabs = useMemo(
        () => [
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
        ],
        [intl]
    );

    return (
        <PageTabs
            initial="transactions"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:commerce" fallback={<Result403 />}>
            <Commerce />
        </Authorized>
    );
};

export {Component};
export default Commerce;
