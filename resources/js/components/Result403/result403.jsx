import React from "react";
import Page from "@/components/Page";
import {Link} from "react-router-dom";
import {styled} from "@mui/material/styles";
import {Button, Container, Typography} from "@mui/material";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {MaintenanceIllustration} from "@/assets/index";
import useHomePath from "@/hooks/useHomePath";

const messages = defineMessages({
    title: {defaultMessage: "Access Forbidden"}
});

const Result403 = () => {
    const intl = useIntl();
    const homePath = useHomePath();

    return (
        <StyledPage title={intl.formatMessage(messages.title)}>
            <Container>
                <ResultContent>
                    <Typography variant="h3" paragraph>
                        <FormattedMessage defaultMessage="Access Forbidden!" />
                    </Typography>

                    <Typography sx={{color: "text.secondary"}}>
                        <FormattedMessage defaultMessage="Sorry, you are not authorized to access this page." />
                    </Typography>

                    <MaintenanceIllustration
                        sx={{my: {xs: 5, sm: 10}, height: 250}}
                    />

                    <Button
                        component={Link}
                        to={homePath}
                        variant="contained"
                        size="large">
                        <FormattedMessage defaultMessage="Go to Home" />
                    </Button>
                </ResultContent>
            </Container>
        </StyledPage>
    );
};

const ResultContent = styled("div")({
    maxWidth: 480,
    textAlign: "center",
    margin: "auto"
});

const StyledPage = styled(Page)(({theme}) => ({
    display: "flex",
    paddingTop: theme.spacing(10),
    paddingBottom: theme.spacing(10),
    minHeight: "100%",
    alignItems: "center"
}));

export default Result403;
