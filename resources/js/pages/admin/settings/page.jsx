import React, {useMemo} from "react";
import PageTabs from "@/components/PageTabs";
import {defineMessages, useIntl} from "react-intl";
import settings from "@iconify-icons/ri/settings-3-fill";
import bug from "@iconify-icons/ri/bug-fill";
import SystemLogs from "./_components/SystemLogs";
import General from "./_components/General";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Settings"},
    documents: {defaultMessage: "Documents"},
    limits: {defaultMessage: "Limits"},
    systemLogs: {defaultMessage: "System Logs"},
    general: {defaultMessage: "General"}
});

const Settings = () => {
    const intl = useIntl();

    const tabs = useMemo(() => {
        return [
            {
                value: "general",
                label: intl.formatMessage(messages.general),
                icon: settings,
                component: <General />
            },
            {
                value: "system-logs",
                label: intl.formatMessage(messages.systemLogs),
                icon: bug,
                component: <SystemLogs />
            }
        ];
    }, [intl]);

    return (
        <PageTabs
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
            initial="general"
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:settings" fallback={<Result403 />}>
            <Settings />
        </Authorized>
    );
};

export {Component};
export default Settings;
