import React from "react";
import Page from "@/components/Page";
import {m} from "framer-motion";
import {Link} from "react-router-dom";
import {styled} from "@mui/material/styles";
import {Button, Container, Typography} from "@mui/material";
import {MotionContainer, varBounce} from "@/components/Animate";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {PageNotFoundIllustration} from "@/assets/index";
import useHomePath from "@/hooks/useHomePath";

const messages = defineMessages({
    title: {defaultMessage: "Page Not Found"}
});

const Result404 = () => {
    const intl = useIntl();
    const homePath = useHomePath();

    return (
        <StyledPage title={intl.formatMessage(messages.title)}>
            <Container component={MotionContainer}>
                <ResultContent>
                    <m.div variants={varBounce().in}>
                        <Typography variant="h3" paragraph>
                            <FormattedMessage defaultMessage="Sorry, page not found!" />
                        </Typography>
                    </m.div>

                    <m.div variants={varBounce().in}>
                        <Typography sx={{color: "text.secondary"}}>
                            <FormattedMessage defaultMessage="Sorry, we could not find the page you’re looking for." />
                        </Typography>
                    </m.div>

                    <m.div variants={varBounce().in}>
                        <PageNotFoundIllustration
                            sx={{my: {xs: 5, sm: 10}, height: 250}}
                        />
                    </m.div>

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

const StyledPage = styled(Page)(({theme}) => ({
    display: "flex",
    paddingTop: theme.spacing(10),
    paddingBottom: theme.spacing(10),
    minHeight: "100%",
    alignItems: "center"
}));

const ResultContent = styled("div")({
    maxWidth: 480,
    margin: "auto",
    display: "flex",
    justifyContent: "center",
    flexDirection: "column",
    textAlign: "center",
    alignItems: "center"
});

export default Result404;
