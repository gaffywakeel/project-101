import React, {useMemo} from "react";
import PageTabs from "@/components/PageTabs";
import {defineMessages, useIntl} from "react-intl";
import survey from "@iconify-icons/ri/survey-fill";
import rocket from "@iconify-icons/ri/rocket-fill";
import Plans from "./_components/Plans";
import Subscription from "./_components/Subscription";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Stake"},
    plans: {defaultMessage: "Plans"},
    subscription: {defaultMessage: "Subscription"}
});

const Stake = () => {
    const intl = useIntl();

    const tabs = useMemo(() => {
        return [
            {
                value: "plans",
                label: intl.formatMessage(messages.plans),
                icon: survey,
                component: <Plans />
            },
            {
                value: "subscription",
                label: intl.formatMessage(messages.subscription),
                icon: rocket,
                component: <Subscription />
            }
        ];
    }, [intl]);

    return (
        <PageTabs
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
            initial="plans"
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:stakes" fallback={<Result403 />}>
            <Stake />
        </Authorized>
    );
};

export {Component};
export default Stake;
