import React from "react";
import {defineMessages, useIntl} from "react-intl";
import Page from "@/components/Page";
import Statistics from "./_components/Statistics";
import {Container, Grid, Stack} from "@mui/material";
import PeriodSelector from "@/components/PeriodSelector";
import TransactionChart from "./_components/TransactionChart";
import WalletAggregate from "./_components/WalletAggregate";
import RequireCommerceAccount from "@/components/RequireCommerceAccount";

const messages = defineMessages({
    title: {defaultMessage: "Commerce Dashboard"}
});

const Dashboard = () => {
    const intl = useIntl();

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container maxWidth="xl">
                <PeriodSelector>
                    <Grid container spacing={3}>
                        <Grid item xs={12} md={8}>
                            <Stack spacing={3}>
                                <Statistics />
                                <TransactionChart />
                            </Stack>
                        </Grid>

                        <Grid item xs={12} md={4}>
                            <Stack spacing={3}>
                                <WalletAggregate />
                            </Stack>
                        </Grid>
                    </Grid>
                </PeriodSelector>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <RequireCommerceAccount>
            <Dashboard />
        </RequireCommerceAccount>
    );
};

export {Component};
export default Dashboard;
