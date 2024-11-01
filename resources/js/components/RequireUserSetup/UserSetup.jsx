import React, {useCallback, useEffect, useMemo, useState} from "react";
import {defineMessages, useIntl} from "react-intl";
import StepContent from "@/components/StepContent";
import VerifyEmail from "./VerifyEmail";
import {fetchUser} from "@/redux/slices/auth";
import {useDispatch} from "react-redux";
import Page from "@/components/Page";
import {Box, Container, Grid, Step, StepLabel, Stepper} from "@mui/material";
import EnableTwoFactor from "./EnableTwoFactor";
import UpdateProfile from "./UpdateProfile";
import StepIcon from "@/components/StepIcon";
import StepConnector from "@/components/StepConnector";
import {StepProvider} from "@/contexts/StepContext";

const messages = defineMessages({
    title: {defaultMessage: "Account Setup"},
    verifyEmail: {defaultMessage: "Verify Email"},
    enableTwoFactor: {defaultMessage: "Enable Two Factor"},
    updateProfile: {defaultMessage: "Update Profile"}
});

const UserSetup = () => {
    const intl = useIntl();
    const [current, setCurrent] = useState(0);
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(fetchUser());
    }, [dispatch]);

    const next = useCallback(() => {
        setCurrent((c) => c + 1);
    }, [setCurrent]);

    const steps = useMemo(
        () => [
            {
                label: intl.formatMessage(messages.verifyEmail),
                component: <VerifyEmail />
            },
            {
                label: intl.formatMessage(messages.enableTwoFactor),
                component: <EnableTwoFactor />
            },
            {
                label: intl.formatMessage(messages.updateProfile),
                component: <UpdateProfile />
            }
        ],
        [intl]
    );

    return (
        <Page title={intl.formatMessage(messages.title)}>
            <Container>
                <Grid container spacing={5} justifyContent="center">
                    <Grid item xs={12} md={8}>
                        <Stepper
                            alternativeLabel
                            connector={<StepConnector />}
                            activeStep={current}>
                            {steps.map((step, key) => (
                                <Step key={key}>
                                    <StepLabel
                                        StepIconComponent={StepIcon}
                                        sx={{
                                            "& .MuiStepLabel-label": {
                                                typography: "subtitle2",
                                                color: "text.disabled"
                                            }
                                        }}>
                                        {step.label}
                                    </StepLabel>
                                </Step>
                            ))}
                        </Stepper>
                    </Grid>

                    <Grid item xs={12} md={8}>
                        <StepProvider next={next}>
                            <StepContent current={current}>
                                {steps.map((step, key) => (
                                    <Box key={key} component="div">
                                        {step.component}
                                    </Box>
                                ))}
                            </StepContent>
                        </StepProvider>
                    </Grid>
                </Grid>
            </Container>
        </Page>
    );
};

export default UserSetup;
