import React, {useState} from "react";
import {defineMessages, useIntl} from "react-intl";
import LogoLayout from "../_components/LogoLayout";
import {Box, Container} from "@mui/material";
import {styled} from "@mui/material/styles";
import Page from "@/components/Page";
import RequestToken from "./_components/RequestToken";
import SendEmailCode from "./_components/SendEmailCode";
import ResetForm from "./_components/ResetForm";

const messages = defineMessages({
    title: {defaultMessage: "Reset Password"}
});

const ForgotPassword = () => {
    const intl = useIntl();
    const [sent, setSent] = useState(false);
    const [email, setEmail] = useState(null);
    const [token, setToken] = useState(null);

    return (
        <StyledPage title={intl.formatMessage(messages.title)}>
            <LogoLayout />

            <Container>
                <Box sx={{maxWidth: 480, mx: "auto"}}>
                    {email && token ? (
                        <ResetForm email={email} token={token} />
                    ) : sent ? (
                        <RequestToken
                            email={email}
                            onSuccess={setToken}
                            onReset={() => {
                                setSent(false);
                                setEmail(null);
                            }}
                        />
                    ) : (
                        <SendEmailCode
                            email={email}
                            onSuccess={(email) => {
                                setSent(true);
                                setEmail(email);
                            }}
                        />
                    )}
                </Box>
            </Container>
        </StyledPage>
    );
};

const StyledPage = styled(Page)(({theme}) => ({
    display: "flex",
    padding: theme.spacing(12, 0),
    alignItems: "center",
    justifyContent: "center",
    minHeight: "100%"
}));

export {ForgotPassword as Component};
export default ForgotPassword;
