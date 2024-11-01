import React from "react";
import {CircularProgress, Stack} from "@mui/material";
import loadable from "@loadable/component";

const Input = loadable(() => import("./input"), {
    fallback: (
        <Stack alignItems="center">
            <CircularProgress size="1.5em" />
        </Stack>
    )
});

const PhoneInput = (props) => {
    return <Input {...props} />;
};

export default PhoneInput;
