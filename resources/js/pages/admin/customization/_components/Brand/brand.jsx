import React from "react";
import {Grid} from "@mui/material";
import UploadLogos from "./UploadLogos";
import UpdateLinks from "./UpdateLinks";

const Brand = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12} md={6}>
                <UpdateLinks />
            </Grid>

            <Grid item xs={12} md={6}>
                <UploadLogos />
            </Grid>
        </Grid>
    );
};

export default Brand;
