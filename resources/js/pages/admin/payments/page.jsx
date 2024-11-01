import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import PageTabs from "@/components/PageTabs";
import dollarCircle from "@iconify-icons/ri/money-dollar-circle-fill";
import flag from "@iconify-icons/ri/flag-fill";
import bank from "@iconify-icons/ri/bank-fill";
import exchangeDollar from "@iconify-icons/ri/exchange-dollar-line";
import sendPlane from "@iconify-icons/ri/send-plane-fill";
import Currencies from "./_components/Currencies";
import BankAccounts from "./_components/BankAccounts";
import history from "@iconify-icons/ri/history-fill";
import PendingTransfer from "./_components/PendingTransfer";
import Transactions from "./_components/Transactions";
import {useAuth} from "@/models/Auth";
import Countries from "./_components/Countries";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    currencies: {defaultMessage: "Currencies"},
    countries: {defaultMessage: "Countries"},
    title: {defaultMessage: "Payments"},
    bankAccounts: {defaultMessage: "Bank Accounts"},
    withdrawals: {defaultMessage: "Withdrawals"},
    deposits: {defaultMessage: "Deposits"},
    transactions: {defaultMessage: "Transactions"}
});

const Payments = () => {
    const intl = useIntl();
    const auth = useAuth();

    const tabs = useMemo(() => {
        const stack = [
            {
                value: "currencies",
                label: intl.formatMessage(messages.currencies),
                icon: dollarCircle,
                component: <Currencies />
            },
            {
                value: "countries",
                label: intl.formatMessage(messages.countries),
                icon: flag,
                component: <Countries />
            }
        ];

        if (auth.can("manage:banks")) {
            stack.push({
                value: "bank-accounts",
                label: intl.formatMessage(messages.bankAccounts),
                icon: bank,
                component: <BankAccounts />
            });

            stack.push({
                value: "deposits",
                label: intl.formatMessage(messages.deposits),
                icon: exchangeDollar,
                component: <PendingTransfer type="receive" />
            });

            stack.push({
                value: "withdrawals",
                label: intl.formatMessage(messages.withdrawals),
                icon: sendPlane,
                component: <PendingTransfer type="send" />
            });
        }

        stack.push({
            value: "transactions",
            label: intl.formatMessage(messages.transactions),
            icon: history,
            component: <Transactions />
        });

        return stack;
    }, [intl, auth]);

    return (
        <PageTabs
            initial="currencies"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:payments" fallback={<Result403 />}>
            <Payments />
        </Authorized>
    );
};

export {Component};
export default Payments;
