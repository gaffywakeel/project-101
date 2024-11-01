import React from "react";
import widgets from "./_components/widgets";
import ResponsiveWidgets from "@/components/ResponsiveWidgets";
import Page from "@/components/Page";
import {defineMessages, useIntl} from "react-intl";
import {Container} from "@mui/material";
import RequireUserSetup from "@/components/RequireUserSetup";

const messages = defineMessages({
    title: {defaultMessage: "Home"}
});

const Home = () => {
    const intl = useIntl();

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container maxWidth="xl" disableGutters>
                <ResponsiveWidgets widgets={widgets} page="index.home" />
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <RequireUserSetup>
            <Home />
        </RequireUserSetup>
    );
};

export {Component};
export default Home;
