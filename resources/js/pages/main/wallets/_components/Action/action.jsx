import React, {useMemo} from "react";
import Send from "./Send";
import Receive from "./Receive";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {useActiveWalletAccount} from "@/hooks/accounts";
import {styled} from "@mui/material/styles";
import {Box, Card, Stack, Typography} from "@mui/material";
import CardTabs from "@/components/CardTabs";

const messages = defineMessages({
    send: {defaultMessage: "Send"},
    receive: {defaultMessage: "Receive"}
});

const Action = () => {
    const intl = useIntl();
    const {account: activeAccount} = useActiveWalletAccount();

    const tabs = useMemo(
        () => [
            {
                label: intl.formatMessage(messages.send),
                component: <Send />
            },
            {
                label: intl.formatMessage(messages.receive),
                component: <Receive />
            }
        ],
        [intl]
    );

    return (
        <Card>
            <CardTabs tabs={tabs} />

            {!activeAccount.isEmpty() && (
                <FooterStyle sx={{px: 2}}>
                    <Box sx={{display: "inline"}}>
                        <Stack
                            direction="row"
                            sx={{display: "inline-flex"}}
                            spacing={1}>
                            <Typography variant="body2" color="inherit">
                                {activeAccount.wallet.coin.symbol}
                            </Typography>

                            <Typography variant="body2" color="inherit">
                                <FormattedMessage defaultMessage="Balance" />
                            </Typography>
                        </Stack>
                    </Box>

                    <Box sx={{display: "inline"}}>
                        <Stack
                            direction="row"
                            sx={{display: "inline-flex"}}
                            divider={<span>&#8776;</span>}
                            spacing={2}>
                            <Typography variant="body2">
                                {activeAccount.available}
                            </Typography>

                            <Typography variant="body2">
                                {activeAccount.formatted_available_price}
                            </Typography>
                        </Stack>
                    </Box>
                </FooterStyle>
            )}
        </Card>
    );
};

const FooterStyle = styled("div")({
    display: "flex",
    justifyContent: "space-between"
});

export default Action;
