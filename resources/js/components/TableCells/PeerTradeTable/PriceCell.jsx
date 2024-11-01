import React from "react";
import {Typography} from "@mui/material";

const PriceCell = ({trade}) => {
    return <Typography variant="body2">{trade.formatted_price}</Typography>;
};

export default PriceCell;
