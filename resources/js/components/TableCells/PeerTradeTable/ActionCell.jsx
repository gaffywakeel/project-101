import React, {useMemo} from "react";
import ForumIcon from "@mui/icons-material/Forum";
import {Badge, IconButton} from "@mui/material";
import {Link as RouterLink} from "react-router-dom";

const ActionCell = ({trade}) => {
    const link = useMemo(() => {
        return `/main/peer/trades/${trade.id}`;
    }, [trade]);

    return (
        <IconButton
            size="large"
            component={RouterLink}
            color="inherit"
            to={link}>
            <Badge badgeContent={trade.unread_messages} color="primary">
                <ForumIcon fontSize="inherit" />
            </Badge>
        </IconButton>
    );
};

export default ActionCell;
