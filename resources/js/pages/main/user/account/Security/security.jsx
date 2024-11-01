import React from "react";
import {Grid} from "@mui/material";
import UserActivity from "./UserActivity";
import TwoFactor from "./TwoFactor";
import ChangePassword from "./ChangePassword";

const Security = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12}>
                <UserActivity />
            </Grid>
            <Grid item xs={12} md={6}>
                <TwoFactor />
            </Grid>
            <Grid item xs={12} md={6}>
                <ChangePassword />
            </Grid>
        </Grid>
    );
};

export default Security;
