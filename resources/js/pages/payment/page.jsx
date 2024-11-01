import React, {useMemo} from "react";
import {Container, Grid, Stack} from "@mui/material";
import Sidebar from "./_components/Sidebar";
import {styled} from "@mui/material/styles";
import CommercePaymentModel from "@/models/CommercePayment";
import {defineMessages, useIntl} from "react-intl";
import Header from "./_components/Header";
import Divider from "@mui/material/Divider";
import Payment from "./_components/Payment";
import Page from "@/components/Page";
import {route, routeRequest} from "@/services/Http";
import {CommercePaymentProvider} from "@/contexts/CommercePaymentContext";
import {useLoaderData, useRevalidator} from "react-router-dom";
import Result403 from "@/components/Result403";
import {HEADER_CONFIG} from "@/theme/layout";

const messages = defineMessages({
    title: {defaultMessage: "{title} Payment"}
});

const CommercePayment = () => {
    const intl = useIntl();
    const validator = useRevalidator();
    const data = useLoaderData();

    const fetchPayment = () => {
        validator.revalidate();
    };

    const payment = useMemo(() => {
        return CommercePaymentModel.use(data);
    }, [data]);

    if (!payment.available) {
        return <Result403 />;
    }

    return (
        <StyledPage
            title={intl.formatMessage(messages.title, {
                title: payment.title
            })}>
            <Container>
                <CommercePaymentProvider
                    fetchPayment={fetchPayment}
                    payment={payment}>
                    <Grid container spacing={{xs: 10, md: 3}}>
                        <Grid item xs={12} md={8}>
                            <Stack spacing={2}>
                                <Header />
                                <Divider sx={{borderStyle: "dashed"}} />
                                <Payment />
                            </Stack>
                        </Grid>

                        <Grid item xs={12} md={4}>
                            <Sidebar />
                        </Grid>
                    </Grid>
                </CommercePaymentProvider>
            </Container>
        </StyledPage>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("page.commerce-payment.get", {payment: params.payment});
    return await routeRequest(request).get(url);
}

const StyledPage = styled(Page)(({theme}) => ({
    paddingTop: HEADER_CONFIG.MOBILE_HEIGHT,
    paddingBottom: HEADER_CONFIG.MOBILE_HEIGHT,
    overflow: "auto",
    minHeight: "100%",
    flexGrow: 1,
    [theme.breakpoints.up("lg")]: {
        paddingBottom: HEADER_CONFIG.DASHBOARD_DESKTOP_HEIGHT,
        paddingTop: HEADER_CONFIG.DASHBOARD_DESKTOP_HEIGHT
    }
}));

export {CommercePayment as Component};
export default CommercePayment;
