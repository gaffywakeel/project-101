import React, {useMemo} from "react";
import {useLoaderData, useRevalidator} from "react-router-dom";
import {route, routeRequest} from "@/services/Http";
import CommercePayment from "@/models/CommercePayment";
import {CommercePaymentProvider} from "@/contexts/CommercePaymentContext";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import StatusCell from "@/components/TableCells/CommercePaymentTable/StatusCell";
import Copyable from "@/components/Copyable";
import LinkCell from "@/components/TableCells/CommercePaymentTable/LinkCell";
import TypeCell from "@/components/TableCells/CommercePaymentTable/TypeCell";
import {formatNumber} from "@/utils/formatter";
import TransactionTable from "../../_components/TransactionTable";
import ContentCard from "../../_components/ContentCard";
import ContentItem from "../../_components/ContentItem";
import {Box, Card, Chip, Container, Grid, Typography} from "@mui/material";
import Page from "@/components/Page";
import RequireCommerceAccount from "@/components/RequireCommerceAccount";

const messages = defineMessages({
    title: {defaultMessage: "{title} - Commerce Payment"}
});

const Payment = () => {
    const intl = useIntl();
    const validator = useRevalidator();
    const data = useLoaderData();

    const payment = useMemo(() => {
        return CommercePayment.use(data);
    }, [data]);

    const fetchPayment = () => {
        validator.revalidate();
    };

    return (
        <Page
            title={intl.formatMessage(messages.title, {
                title: payment.title
            })}>
            <Container>
                <CommercePaymentProvider
                    fetchPayment={fetchPayment}
                    payment={payment}>
                    <HeaderBreadcrumbs
                        title={payment.title}
                        action={
                            <StatusCell
                                onChange={fetchPayment}
                                payment={data}
                            />
                        }
                    />

                    <Grid container spacing={2}>
                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Title" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {payment.title}
                                    </Copyable>
                                </ContentItem>

                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Description" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {payment.description}
                                    </Copyable>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Amount" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary">
                                        {payment.formatted_amount}
                                    </Copyable>
                                </ContentItem>

                                <ContentItem
                                    spacing={0}
                                    alignItems="flex-start">
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Link" />
                                    </Typography>

                                    <LinkCell payment={data} />
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Redirect" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {payment.redirect ?? "N/A"}
                                    </Copyable>
                                </ContentItem>

                                <ContentItem>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Message" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary"
                                        ellipsis>
                                        {payment.message ?? "N/A"}
                                    </Copyable>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12} md={6}>
                            <ContentCard>
                                <ContentItem alignItems="flex-start">
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Type" />
                                    </Typography>

                                    <TypeCell type={payment.type} />
                                </ContentItem>

                                <ContentItem alignItems="flex-start">
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Transactions" />
                                    </Typography>

                                    <Copyable
                                        variant="body2"
                                        color="text.secondary">
                                        {formatNumber(
                                            payment.transactions_count
                                        )}
                                    </Copyable>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12}>
                            <ContentCard>
                                <ContentItem spacing={0}>
                                    <Typography variant="subtitle1" noWrap>
                                        <FormattedMessage defaultMessage="Accepted Coins" />
                                    </Typography>

                                    <Box sx={{mx: -0.5, mt: 1}}>
                                        {payment.wallets.map((wallet) => (
                                            <Chip
                                                key={wallet.id}
                                                label={wallet.coin.name}
                                                sx={{m: 0.5}}
                                            />
                                        ))}
                                    </Box>
                                </ContentItem>
                            </ContentCard>
                        </Grid>

                        <Grid item xs={12}>
                            <Card>
                                <TransactionTable
                                    url={route(
                                        "commerce-payment.transaction-paginate",
                                        {id: payment.id}
                                    )}
                                />
                            </Card>
                        </Grid>
                    </Grid>
                </CommercePaymentProvider>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <RequireCommerceAccount>
            <Payment />
        </RequireCommerceAccount>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("commerce-payment.get", {id: params.payment});
    return await routeRequest(request).get(url);
}

export {Component};
export default Payment;
