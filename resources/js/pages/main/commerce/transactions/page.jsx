import React, {useCallback, useEffect, useMemo, useState} from "react";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import {defineMessages, useIntl} from "react-intl";
import {Card, Container} from "@mui/material";
import Page from "@/components/Page";
import Spin from "@/components/Spin";
import CardTabs from "@/components/CardTabs";
import {errorHandler, route, useRequest} from "@/services/Http";
import TransactionTable from "../_components/TransactionTable";
import Label from "@/components/Label";
import RequireCommerceAccount from "@/components/RequireCommerceAccount";

const messages = defineMessages({
    title: {defaultMessage: "Commerce Transactions"},
    pending: {defaultMessage: "Pending"},
    completed: {defaultMessage: "Completed"},
    canceled: {defaultMessage: "Canceled"},
    all: {defaultMessage: "All"}
});

const Transactions = () => {
    const intl = useIntl();
    const [request, loading] = useRequest();

    const [statistics, setStatistics] = useState({
        pending: 0,
        completed: 0,
        canceled: 0,
        all: 0
    });

    const fetchStatistics = useCallback(() => {
        request
            .get(route("commerce-transaction.get-status-statistics"))
            .then(({data}) => setStatistics(data))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        fetchStatistics();
    }, [fetchStatistics]);

    const tabs = useMemo(
        () => [
            {
                label: intl.formatMessage(messages.all),
                icon: <Label color="default">{statistics.all}</Label>,
                component: <TransactionTable />
            },
            {
                label: intl.formatMessage(messages.pending),
                icon: <Label color="info">{statistics.pending}</Label>,
                component: <TransactionTable status="pending" />
            },
            {
                label: intl.formatMessage(messages.completed),
                icon: <Label color="success">{statistics.completed}</Label>,
                component: <TransactionTable status="completed" />
            },
            {
                label: intl.formatMessage(messages.canceled),
                icon: <Label color="error">{statistics.canceled}</Label>,
                component: <TransactionTable status="canceled" />
            }
        ],
        [intl, statistics]
    );

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs />

                <Card>
                    <Spin spinning={loading}>
                        <CardTabs
                            variant="scrollable"
                            allowScrollButtonsMobile
                            scrollButtons="auto"
                            tabs={tabs}
                        />
                    </Spin>
                </Card>
            </Container>
        </Page>
    );
};

const Component = () => {
    return (
        <RequireCommerceAccount>
            <Transactions />
        </RequireCommerceAccount>
    );
};

export {Component};
export default Transactions;
