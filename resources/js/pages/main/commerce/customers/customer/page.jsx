import React, {useMemo} from "react";
import {useLoaderData, useRevalidator} from "react-router-dom";
import {route, routeRequest} from "@/services/Http";
import CommerceCustomer from "@/models/CommerceCustomer";
import {CommerceCustomerProvider} from "@/contexts/CommerceCustomerContext";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {Card, Container, Grid, Typography} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Copyable from "@/components/Copyable";
import {formatNumber} from "@/utils/formatter";
import TransactionTable from "../../_components/TransactionTable";
import Page from "@/components/Page";
import ContentItem from "../../_components/ContentItem";
import ContentCard from "../../_components/ContentCard";
import RequireCommerceAccount from "@/components/RequireCommerceAccount";

const messages = defineMessages({
    title: {defaultMessage: "{name} - Commerce Customer"}
});

const Customer = () => {
    const intl = useIntl();
    const validator = useRevalidator();
    const data = useLoaderData();

    const customer = useMemo(() => {
        return CommerceCustomer.use(data);
    }, [data]);

    const fetchCustomer = () => {
        validator.revalidate();
    };

    return (
        <Page
            title={intl.formatMessage(messages.title, {
                name: customer.first_name
            })}>
            <Container>
                <CommerceCustomerProvider
                    fetchCustomer={fetchCustomer}
                    customer={customer}>
                    <HeaderBreadcrumbs title={customer.first_name} />

                    <Grid container spacing={2}>
                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="First Name" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {customer.first_name}
                                    </Copyable>
                                </ContentItem>

                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Last Name" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {customer.last_name}
                                    </Copyable>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Email" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {customer.email}
                                    </Copyable>
                                </ContentItem>

                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Transactions" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary">
                                        {formatNumber(
                                            customer.transactions_count
                                        )}
                                    </Copyable>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12}>
                            <Card>
                                <TransactionTable
                                    url={route(
                                        "commerce-customer.transaction-paginate",
                                        {id: customer.id}
                                    )}
                                />
                            </Card>
                        </Grid>
                    </Grid>
                </CommerceCustomerProvider>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <RequireCommerceAccount>
            <Customer />
        </RequireCommerceAccount>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("commerce-customer.get", {id: params.customer});
    return await routeRequest(request).get(url);
}

export {Component};
export default Customer;
