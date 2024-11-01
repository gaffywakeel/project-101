import React from "react";
import {FormattedMessage} from "react-intl";
import {Chip, Stack, Typography} from "@mui/material";
import {first} from "lodash";

const PermissionsCell = ({permissions, isAdministrator = false}) => {
    if (isAdministrator) {
        return (
            <Typography variant="body2">
                <FormattedMessage defaultMessage="All" />
            </Typography>
        );
    }

    if (permissions.length === 0) {
        return (
            <Typography variant="body2">
                <FormattedMessage defaultMessage="None" />
            </Typography>
        );
    }

    return (
        <Stack
            direction="row"
            sx={{minWidth: 100}}
            alignItems="center"
            spacing={1}>
            <Chip
                variant="filled"
                label={first(permissions).title}
                size="small"
            />

            {permissions.length > 1 && (
                <Typography variant="caption">
                    {`+${permissions.length - 1}`}
                </Typography>
            )}
        </Stack>
    );
};

export default PermissionsCell;
