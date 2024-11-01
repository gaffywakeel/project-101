import React from "react";
import {Stack, Typography} from "@mui/material";
import CoinIcon from "@/components/CoinIcon";

const ValueCell = ({value, coin, price}) => {
    return (
        <Stack direction="row" spacing={1.5}>
            <CoinIcon coin={coin} size={25} />

            <Stack sx={{width: "100%", minWidth: 0}}>
                <Typography variant="body2" noWrap>
                    {value}
                </Typography>

                <Typography variant="caption" color="text.secondary" noWrap>
                    {price}
                </Typography>
            </Stack>
        </Stack>
    );
};

export default ValueCell;
