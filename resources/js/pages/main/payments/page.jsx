import React, {useEffect} from "react";
import {defineMessages, useIntl} from "react-intl";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Page from "@/components/Page";
import {Container, Grid} from "@mui/material";
import Balance from "./_components/Balance";
import Transaction from "./_components/Transaction";
import Action from "./_components/Action";
import {fetchPaymentAccount} from "@/redux/slices/payment";
import {useDispatch} from "react-redux";
import Module from "@/components/Module";
import RequireUserSetup from "@/components/RequireUserSetup";

const messages = defineMessages({
    title: {defaultMessage: "Payments"}
});

const Payments = () => {
    const dispatch = useDispatch();
    const intl = useIntl();

    useEffect(() => {
        dispatch(fetchPaymentAccount());
    }, [dispatch]);

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs />

                <Grid container spacing={3}>
                    <Grid item xs={12} md={6}>
                        <Grid container spacing={3}>
                            <Grid item xs={12}>
                                <Balance />
                            </Grid>

                            <Grid item xs={12}>
                                <Action />
                            </Grid>
                        </Grid>
                    </Grid>

                    <Grid item xs={12} md={6}>
                        <Grid container spacing={3}>
                            <Grid item xs={12}>
                                <Transaction />
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <Module module="payment">
            <RequireUserSetup>
                <Payments />
            </RequireUserSetup>
        </Module>
    );
};

export {Component};
export default Payments;
