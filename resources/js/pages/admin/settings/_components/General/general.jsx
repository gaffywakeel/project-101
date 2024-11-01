import React from "react";
import {Grid} from "@mui/material";
import UpdateGeneral from "./UpdateGeneral";
import UpdateService from "./UpdateService";

const General = () => {
    return (
        <Grid container spacing={2}>
            <Grid item xs={12} md={6}>
                <UpdateGeneral />
            </Grid>
            <Grid item xs={12} md={6}>
                <UpdateService />
            </Grid>
        </Grid>
    );
};

export default General;
