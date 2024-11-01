import React from "react";
import {Grid} from "@mui/material";
import Profile from "./Profile";
import Picture from "./Picture";

const General = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12} md={4}>
                <Picture />
            </Grid>

            <Grid item xs={12} md={8}>
                <Profile />
            </Grid>
        </Grid>
    );
};

export default General;
