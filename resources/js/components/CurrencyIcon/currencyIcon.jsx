import React from "react";
import {Avatar} from "@mui/material";
import {useTheme} from "@mui/material/styles";

const CurrencyIcon = ({symbol, size = 25}) => {
    const theme = useTheme();

    return (
        <Avatar
            sx={{
                height: size,
                width: size,
                lineHeight: "inherit",
                background: theme.palette.primary.main,
                color: theme.palette.primary.contrastText,
                fontSize: (size * 2) / 3
            }}>
            <span>{symbol}</span>
        </Avatar>
    );
};

export default CurrencyIcon;
