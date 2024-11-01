import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import stack from "@iconify-icons/ri/stack-fill.js";
import layoutGrid from "@iconify-icons/ri/layout-grid-fill.js";
import PageTabs from "@/components/PageTabs";
import ModulesTab from "./_components/Modules";
import Grid from "./_components/Grid";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Modules"},
    modules: {defaultMessage: "Modules"},
    grid: {defaultMessage: "Grid"}
});

const Modules = () => {
    const intl = useIntl();

    const tabs = useMemo(
        () => [
            {
                value: "modules",
                label: intl.formatMessage(messages.modules),
                icon: stack,
                component: <ModulesTab />
            },
            {
                value: "grid",
                label: intl.formatMessage(messages.grid),
                icon: layoutGrid,
                component: <Grid />
            }
        ],
        [intl]
    );

    return (
        <PageTabs
            initial="modules"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:modules" fallback={<Result403 />}>
            <Modules />
        </Authorized>
    );
};

export {Component};
export default Modules;
