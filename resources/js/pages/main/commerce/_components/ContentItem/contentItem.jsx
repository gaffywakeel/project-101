import React from "react";
import {Stack} from "@mui/material";

const ContentItem = (props) => {
    return (
        <Stack
            spacing={0.5}
            justifyContent="space-between"
            sx={{minWidth: 0, flex: 1}}
            {...props}
        />
    );
};

export default ContentItem;
