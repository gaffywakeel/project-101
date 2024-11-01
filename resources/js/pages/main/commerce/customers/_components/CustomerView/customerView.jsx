import React from "react";
import {IconButton} from "@mui/material";
import VisibilityIcon from "@mui/icons-material/Visibility";
import {Link as RouterLink} from "react-router-dom";

const CustomerView = ({customer}) => {
    return (
        <IconButton
            color="primary"
            component={RouterLink}
            to={`/main/commerce/customers/${customer.id}`}>
            <VisibilityIcon />
        </IconButton>
    );
};

export default CustomerView;
