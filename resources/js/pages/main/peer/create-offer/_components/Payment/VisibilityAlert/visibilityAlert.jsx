import React from "react";
import {FormattedMessage} from "react-intl";
import Form from "@/components/Form";
import {Alert} from "@mui/material";

const VisibilityAlert = () => {
    const type = Form.useWatch("type");

    if (type !== "sell") return null;

    return (
        <Alert severity="info">
            <FormattedMessage defaultMessage="This offer will only be visible when your available balance is greater than the offer limit." />
        </Alert>
    );
};

export default VisibilityAlert;
