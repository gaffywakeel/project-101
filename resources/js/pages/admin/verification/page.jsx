import React, {useMemo} from "react";
import PageTabs from "@/components/PageTabs";
import {defineMessages, useIntl} from "react-intl";
import scales from "@iconify-icons/ri/scales-fill";
import fileText from "@iconify-icons/ri/file-text-fill";
import Limits from "./_components/Limits";
import Documents from "./_components/Documents";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Verification"},
    documents: {defaultMessage: "Documents"},
    limits: {defaultMessage: "Limits"}
});

const Verification = () => {
    const intl = useIntl();

    const tabs = useMemo(() => {
        return [
            {
                value: "limits",
                label: intl.formatMessage(messages.limits),
                icon: scales,
                component: <Limits />
            },
            {
                value: "documents",
                label: intl.formatMessage(messages.documents),
                icon: fileText,
                component: <Documents />
            }
        ];
    }, [intl]);

    return (
        <PageTabs
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
            initial="limits"
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:settings" fallback={<Result403 />}>
            <Verification />
        </Authorized>
    );
};

export {Component};
export default Verification;
