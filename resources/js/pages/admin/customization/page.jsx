import React, {useMemo} from "react";
import PageTabs from "@/components/PageTabs";
import {defineMessages, useIntl} from "react-intl";
import paintBrush from "@iconify-icons/ri/paint-brush-fill";
import copyright from "@iconify-icons/ri/copyright-fill";
import Theme from "./_components/Theme";
import Brand from "./_components/Brand";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Customization"},
    brand: {defaultMessage: "Brand"},
    theme: {defaultMessage: "Theme"}
});

const Customization = () => {
    const intl = useIntl();

    const tabs = useMemo(() => {
        return [
            {
                value: "theme",
                label: intl.formatMessage(messages.theme),
                icon: paintBrush,
                component: <Theme />
            },
            {
                value: "brand",
                label: intl.formatMessage(messages.brand),
                icon: copyright,
                component: <Brand />
            }
        ];
    }, [intl]);

    return (
        <PageTabs
            initial="theme"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:customization" fallback={<Result403 />}>
            <Customization />
        </Authorized>
    );
};

export {Component};
export default Customization;
