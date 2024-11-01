import React, {useCallback, useEffect, useState} from "react";
import {styled} from "@mui/material/styles";
import ResponsiveCard from "@/components/ResponsiveWidgets/responsiveCard";
import {Box, Stack, Typography} from "@mui/material";
import {formatNumber} from "@/utils/formatter";
import {FormattedMessage} from "react-intl";
import CheckOutIllustration from "@/assets/illustrations/checkout";
import {errorHandler, route, useRequest} from "@/services/Http";
import Spin from "@/components/Spin";

const PendingWithdrawals = () => {
    const [total, setTotal] = useState(0);
    const [request, loading] = useRequest();

    const fetchTotal = useCallback(() => {
        request
            .get(route("admin.statistics.pending-withdrawal"))
            .then(({data}) => setTotal(data.total))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        fetchTotal();
    }, [fetchTotal]);

    return (
        <StyledResponsiveCard>
            <Spin sx={{height: "100%"}} spinning={loading}>
                <Stack
                    alignItems="center"
                    direction="row"
                    justifyContent="space-between"
                    sx={{height: "100%"}}>
                    <Stack>
                        <Typography variant="h4">
                            {formatNumber(total)}
                        </Typography>

                        <Typography
                            sx={{color: "text.secondary"}}
                            variant="overline">
                            <FormattedMessage defaultMessage="Withdrawals" />
                        </Typography>
                    </Stack>

                    <IconContainer>
                        <CheckOutIllustration />
                    </IconContainer>
                </Stack>
            </Spin>
        </StyledResponsiveCard>
    );
};

const StyledResponsiveCard = styled(ResponsiveCard)(({theme}) => ({
    padding: theme.spacing(2, 2, 2, 3)
}));

const IconContainer = styled(Box)(({theme}) => ({
    height: 80,
    width: 80,
    lineHeight: 0,
    backgroundColor: theme.palette.background.neutral,
    borderRadius: "50%"
}));

PendingWithdrawals.dimensions = {
    lg: {w: 4, h: 1, isResizable: false},
    md: {w: 4, h: 1, isResizable: false},
    sm: {w: 2, h: 1, isResizable: false},
    xs: {w: 1, h: 1, isResizable: false}
};

export default PendingWithdrawals;
