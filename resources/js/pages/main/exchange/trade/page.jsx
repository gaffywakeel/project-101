import React from "react";
import {defineMessages, useIntl} from "react-intl";
import Page from "@/components/Page";
import {Container, Grid} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Action from "./_components/Action";
import Transaction from "./_components/Transaction";
import GlobalAccountSelect from "@/components/GlobalAccountSelect";

const messages = defineMessages({
    title: {defaultMessage: "Trades"}
});

const Trade = () => {
    const intl = useIntl();

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs
                    action={<GlobalAccountSelect sx={{width: 150}} />}
                />

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

export {Trade as Component};
export default Trade;
