import React, {useMemo} from "react";
import {useLoaderData, useRevalidator} from "react-router-dom";
import {route, routeRequest} from "@/services/Http";
import PeerTrade from "@/models/PeerTrade";
import {PeerTradeProvider} from "@/contexts/PeerTradeContext";
import TradeChat from "./_components/TradeChat";
import {useIntl} from "react-intl";
import {Container} from "@mui/material";
import Page from "@/components/Page";

const Trade = () => {
    const intl = useIntl();
    const validator = useRevalidator();
    const data = useLoaderData();

    const trade = useMemo(() => {
        return PeerTrade.use(data);
    }, [data]);

    const fetchTrade = () => {
        validator.revalidate();
    };

    return (
        <Page title={trade.title(intl)}>
            <Container maxWidth="xl">
                <PeerTradeProvider trade={trade} fetchTrade={fetchTrade}>
                    <TradeChat />
                </PeerTradeProvider>
            </Container>
        </Page>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("peer-trade.get", {trade: params.trade});
    return await routeRequest(request).get(url);
}

export {Trade as Component};
export default Trade;
