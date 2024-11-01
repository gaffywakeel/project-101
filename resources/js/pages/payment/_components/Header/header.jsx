import React, {useContext} from "react";
import {Box, Stack, Typography} from "@mui/material";
import BusinessAccount from "./BusinessAccount";
import CommercePaymentContext from "@/contexts/CommercePaymentContext";

const Header = () => {
    const {payment} = useContext(CommercePaymentContext);

    return (
        <Stack
            direction="row"
            alignItems="center"
            sx={{minWidth: 0}}
            spacing={2}>
            <BusinessAccount account={payment.account} />

            <Typography variant="subtitle1" noWrap>
                {payment.title}
            </Typography>

            <Box sx={{flexGrow: 1}} />

            <Typography
                variant="subtitle1"
                color="text.secondary"
                flexShrink={0}>
                {payment.formatted_amount}
            </Typography>
        </Stack>
    );
};

export default Header;
