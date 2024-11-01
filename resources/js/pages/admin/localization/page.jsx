import React, {useState} from "react";
import {defineMessages, useIntl} from "react-intl";
import Page from "@/components/Page";
import ManageLocales from "./_components/ManageLocales";
import ManageGroup from "./_components/ManageGroup";
import {LocaleProvider} from "@/contexts/LocaleContext";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import {Container} from "@mui/material";
import Authorized from "@/components/Authorized";
import Result403 from "@/components/Result403";

const messages = defineMessages({
    title: {defaultMessage: "Localization"}
});

const Localization = () => {
    const intl = useIntl();
    const [group, setGroup] = useState();

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs />

                <LocaleProvider group={group} setGroup={setGroup}>
                    {group ? <ManageGroup /> : <ManageLocales />}
                </LocaleProvider>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <Authorized permission="manage:localization" fallback={<Result403 />}>
            <Localization />
        </Authorized>
    );
};

export {Component};
export default Localization;
