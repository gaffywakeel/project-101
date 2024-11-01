import React from "react";
import {FormattedMessage} from "react-intl";
import ItemSummary from "../ItemSummary";
import FlagIcon from "@/components/FlagIcon";
import {useAuth} from "@/models/Auth";
import {Alert} from "@mui/material";

const Country = () => {
    const auth = useAuth();

    if (!auth.user.country) {
        return (
            <Alert severity="warning" sx={{m: 0.5}}>
                <FormattedMessage defaultMessage="Your country is not set on profile." />
            </Alert>
        );
    }

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Country" />}
            content={
                <FlagIcon
                    sx={{width: 20, height: 20}}
                    code={auth.user.country}
                />
            }
        />
    );
};

export default Country;
