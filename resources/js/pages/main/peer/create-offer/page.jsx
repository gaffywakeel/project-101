import React, {useCallback, useMemo, useState} from "react";
import {Box, Container, Grid, Step, StepLabel, Stepper} from "@mui/material";
import Page from "@/components/Page";
import {defineMessages, useIntl} from "react-intl";
import GlobalAccountSelect from "@/components/GlobalAccountSelect";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Form from "@/components/Form";
import {useAuth} from "@/models/Auth";
import {useAccountEffect, useValuesChangeHandler} from "./_components/hooks";
import Price from "./_components/Price";
import Payment from "./_components/Payment";
import Terms from "./_components/Terms";
import StepConnector from "@/components/StepConnector";
import StepIcon from "@/components/StepIcon";
import {StepProvider} from "@/contexts/StepContext";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {has, tap} from "lodash";
import {notify} from "@/utils/index";
import {useNavigate} from "react-router-dom";
import PageSlider from "@/components/PageSlider";
import {styled} from "@mui/material/styles";
import useScreenType from "@/hooks/useScreenType";

const messages = defineMessages({
    title: {defaultMessage: "Create Offer"},
    success: {defaultMessage: "Your offer was created."},
    errors: {defaultMessage: "Please check your inputs and try again."},
    price: {defaultMessage: "Price"},
    payment: {defaultMessage: "Payment"},
    terms: {defaultMessage: "Terms"}
});

const CreateOffer = () => {
    const [form] = Form.useForm();
    const intl = useIntl();
    const auth = useAuth();
    const [current, setCurrent] = useState(0);
    const handleValuesChange = useValuesChangeHandler();
    const [request, loading] = useFormRequest(form);
    const {isMobile} = useScreenType();
    const navigate = useNavigate();

    useAccountEffect(form);

    const next = useCallback(() => {
        setCurrent((c) => c + 1);
    }, [setCurrent]);

    const prev = useCallback(() => {
        setCurrent((c) => c - 1);
    }, [setCurrent]);

    const steps = useMemo(
        () => [
            {
                label: intl.formatMessage(messages.price),
                component: <Price />
            },
            {
                label: intl.formatMessage(messages.payment),
                component: <Payment />
            },
            {
                label: intl.formatMessage(messages.terms),
                component: <Terms />
            }
        ],
        [intl]
    );

    const initialValues = useMemo(
        () => ({
            type: "buy",
            currency: auth.user.currency,
            price_type: "fixed",
            payment: "bank_account"
        }),
        [auth]
    );

    const submitForm = useCallback(
        (values) => {
            request
                .post(route("peer-offer.create"), normalize(values))
                .then(() => {
                    notify.success(intl.formatMessage(messages.success));
                    if (values.type === "buy") {
                        navigate("/main/peer/sell-crypto");
                    } else {
                        navigate("/main/peer/buy-crypto");
                    }
                })
                .catch(
                    errorHandler((error) => {
                        if (has(error, "response.data.errors")) {
                            notify.error(error.response.data.message);
                        }
                    })
                );
        },
        [request, navigate, intl]
    );

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <HeaderBreadcrumbs
                    action={<GlobalAccountSelect sx={{width: 150}} />}
                />

                <Form
                    form={form}
                    initialValues={initialValues}
                    onValuesChange={handleValuesChange}
                    onFinish={submitForm}>
                    <Grid container justifyContent="center">
                        <Grid item xs={12} md={8}>
                            <Stepper
                                alternativeLabel
                                connector={<StepConnector />}
                                activeStep={current}>
                                {steps.map((step, key) => (
                                    <Step key={key}>
                                        <StyledStepLabel
                                            StepIconComponent={StepIcon}>
                                            {step.label}
                                        </StyledStepLabel>
                                    </Step>
                                ))}
                            </Stepper>
                        </Grid>
                    </Grid>

                    <Box sx={{py: 4, mt: 4}}>
                        <StepProvider next={next} prev={prev} loading={loading}>
                            <PageSlider
                                index={current}
                                offsetX={isMobile ? 2 : 3}
                                offsetY={4}>
                                {steps.map((step, key) => (
                                    <Box key={key} component="div">
                                        {step.component}
                                    </Box>
                                ))}
                            </PageSlider>
                        </StepProvider>
                    </Box>
                </Form>
            </Container>
        </Page>
    );
};

const normalize = (values) => {
    return tap(values, (values) => {
        values.payment_method = values.payment_method?.id;
        values.bank_account = values.bank_account?.id;
    });
};

const StyledStepLabel = styled(StepLabel)({
    "& .MuiStepLabel-label": {
        typography: "subtitle2",
        color: "text.disabled"
    }
});

export {CreateOffer as Component};
export default CreateOffer;
