import React, {createElement, Fragment, useCallback, useState} from "react";
import {CircularProgress, IconButton, Popover, Stack} from "@mui/material";
import loadable from "@loadable/component";

const Picker = loadable(() => import("./picker"), {
    fallback: (
        <Stack alignItems="center">
            <CircularProgress size="2em" />
        </Stack>
    )
});

const EmojiPicker = ({
    component = IconButton,
    value,
    onChange,
    pickerProps,
    ...otherProps
}) => {
    const [anchor, setAnchor] = useState(null);

    const close = useCallback(() => {
        setAnchor(null);
    }, []);

    const handleClick = useCallback((e) => {
        setAnchor(e.currentTarget);
    }, []);

    return (
        <Fragment>
            {createElement(component, {
                ...otherProps,
                onClick: handleClick
            })}

            <Popover
                anchorEl={anchor}
                open={Boolean(anchor)}
                onClose={close}
                anchorOrigin={{
                    horizontal: "center",
                    vertical: "top"
                }}
                transformOrigin={{
                    horizontal: "left",
                    vertical: "bottom"
                }}>
                <Picker value={value} onChange={onChange} {...pickerProps} />
            </Popover>
        </Fragment>
    );
};

export default EmojiPicker;
