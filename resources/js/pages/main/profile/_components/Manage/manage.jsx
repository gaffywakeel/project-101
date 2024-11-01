import React from "react";
import {Grid} from "@mui/material";
import Activity from "./Activity";
import TwoFactor from "./TwoFactor";
import ResetPassword from "./ResetPassword";
import Management from "./Management";

const Manage = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12} md={4}>
                <Grid container spacing={2}>
                    <Grid item xs={12}>
                        <ResetPassword />
                    </Grid>

                    <Grid item xs={12}>
                        <TwoFactor />
                    </Grid>
                </Grid>
            </Grid>

            <Grid item xs={12} md={8}>
                <Grid container spacing={2}>
                    <Grid item xs={12}>
                        <Management />
                    </Grid>

                    <Grid item xs={12}>
                        <Activity />
                    </Grid>
                </Grid>
            </Grid>
        </Grid>
    );
};

export default Manage;
