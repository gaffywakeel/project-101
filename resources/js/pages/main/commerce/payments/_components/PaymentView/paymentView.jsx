import React from "react";
import {Link as RouterLink} from "react-router-dom";
import VisibilityIcon from "@mui/icons-material/Visibility";
import {IconButton} from "@mui/material";

const PaymentView = ({payment}) => {
    return (
        <IconButton
            color="primary"
            component={RouterLink}
            to={`/main/commerce/payments/${payment.id}`}>
            <VisibilityIcon />
        </IconButton>
    );
};

export default PaymentView;
