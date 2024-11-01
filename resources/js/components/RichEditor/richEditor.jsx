import React from "react";
import loadable from "@loadable/component";
import {CircularProgress, Stack} from "@mui/material";

const Editor = loadable(() => import("./editor"), {
    fallback: (
        <Stack alignItems="center">
            <CircularProgress size="2em" />
        </Stack>
    )
});

const RichEditor = (props) => {
    return <Editor {...props} />;
};

export default RichEditor;
