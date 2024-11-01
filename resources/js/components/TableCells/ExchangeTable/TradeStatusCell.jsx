import React from "react";
import ArrowForwardIcon from "@mui/icons-material/ArrowForward";
import {Stack} from "@mui/material";
import CurrencyIcon from "@/components/CurrencyIcon";
import CoinIcon from "@/components/CoinIcon";

const TradeStatusCell = ({trade}) => {
    const color = trade.status === "completed" ? "success" : "disabled";

    switch (trade.type) {
        case "buy":
            return (
                <Stack
                    direction="row"
                    justifyContent="space-between"
                    alignItems="center"
                    spacing={1}>
                    <CurrencyIcon symbol={trade.payment_symbol} />
                    <ArrowForwardIcon color={color} fontSize="small" />
                    <CoinIcon coin={trade.coin} size={25} />
                </Stack>
            );

        case "sell":
            return (
                <Stack
                    direction="row"
                    justifyContent="space-between"
                    alignItems="center"
                    spacing={1}>
                    <CoinIcon coin={trade.coin} size={25} />
                    <ArrowForwardIcon color={color} fontSize="small" />
                    <CurrencyIcon symbol={trade.payment_symbol} />
                </Stack>
            );
    }
};

export default TradeStatusCell;
