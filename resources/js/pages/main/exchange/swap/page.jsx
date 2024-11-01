import React from "react";
import {defineMessages, useIntl} from "react-intl";
import Page from "@/components/Page";
import {Container, Grid} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Action from "./_components/Action";
import Transaction from "./_components/Transaction";
import Module from "@/components/Module";
import RequireUserSetup from "@/components/RequireUserSetup";

const messages = defineMessages({
    title: {defaultMessage: "Swap"}
});

const Swap = () => {
    const intl = useIntl();

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs />

                <Grid container spacing={3}>
                    <Grid item xs={12} md={6}>
                        <Action />
                    </Grid>

                    <Grid item xs={12} md={6}>
                        <Transaction />
                    </Grid>
                </Grid>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <Module module="exchange">
            <RequireUserSetup>
                <Swap />
            </RequireUserSetup>
        </Module>
    );
};

export {Component};
export default Swap;
