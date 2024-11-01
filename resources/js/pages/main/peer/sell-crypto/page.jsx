import React, {useState} from "react";
import {defineMessages, useIntl} from "react-intl";
import Page from "@/components/Page";
import {Container, Grid} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import OffersTable from "../_components/OffersTable";
import OffersFilter from "../_components/OffersFilter";

const messages = defineMessages({
    sell: {defaultMessage: "Sell Crypto"}
});

const SellCrypto = () => {
    const intl = useIntl();
    const [filters, setFilters] = useState();

    return (
        <Page title={intl.formatMessage(messages.sell)}>
            <Container>
                <HeaderBreadcrumbs />

                <Grid container spacing={3}>
                    <Grid item xs={12} md={8}>
                        <OffersTable type="buy" filters={filters} />
                    </Grid>

                    <Grid item xs={12} md={4}>
                        <OffersFilter apply={setFilters} />
                    </Grid>
                </Grid>
            </Container>
        </Page>
    );
};

export {SellCrypto as Component};
export default SellCrypto;
