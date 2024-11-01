import React from "react";
import {Box} from "@mui/material";

const IconBuilder = ({icon: svgIcon, sx: iconStyle, ...otherProps}) => {
    if (!svgIcon) {
        return null;
    }

    return (
        <Box
            component="span"
            {...otherProps}
            sx={{
                display: "flex",
                alignItems: "center",
                flexShrink: 0,
                ...iconStyle
            }}>
            <Box
                component="img"
                sx={{
                    height: "1em",
                    width: "1em",
                    maxWidth: "none"
                }}
                src={svgIcon}
            />
        </Box>
    );
};

export default IconBuilder;
