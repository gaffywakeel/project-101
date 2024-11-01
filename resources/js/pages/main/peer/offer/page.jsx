import React, {useMemo} from "react";
import {useLoaderData, useRevalidator} from "react-router-dom";
import {route, routeRequest} from "@/services/Http";
import PeerOffer from "@/models/PeerOffer";
import {PeerOfferProvider} from "@/contexts/PeerOfferContext";
import {useIntl} from "react-intl";
import {Container, Grid, Stack} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Action from "./_components/Action";
import Description from "./_components/Description";
import Extras from "./_components/Extras";
import Details from "./_components/Details";
import Page from "@/components/Page";

const Offer = () => {
    const intl = useIntl();
    const validator = useRevalidator();
    const data = useLoaderData();

    const offer = useMemo(() => {
        return PeerOffer.use(data);
    }, [data]);

    const title = useMemo(() => {
        return offer.title(intl);
    }, [offer, intl]);

    const fetchOffer = () => {
        validator.revalidate();
    };

    return (
        <Page title={title}>
            <Container>
                <PeerOfferProvider offer={offer} fetchOffer={fetchOffer}>
                    <HeaderBreadcrumbs title={title} />

                    <Grid container spacing={3}>
                        <Grid item xs={12} md={8}>
                            <Stack spacing={3}>
                                <Action />
                                <Description />
                                <Extras />
                            </Stack>
                        </Grid>

                        <Grid item xs={12} md={4}>
                            <Details />
                        </Grid>
                    </Grid>
                </PeerOfferProvider>
            </Container>
        </Page>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("peer-offer.get", {offer: params.offer});
    return await routeRequest(request).get(url);
}

export {Offer as Component};
export default Offer;
