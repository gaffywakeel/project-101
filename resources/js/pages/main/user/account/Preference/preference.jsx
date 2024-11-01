import React from "react";
import {Grid} from "@mui/material";
import NotificationForm from "./NotificationForm";
import PaymentForm from "./PaymentForm";

const Preference = () => {
    return (
        <Grid container spacing={2}>
            <Grid item xs={12} md={6}>
                <NotificationForm />
            </Grid>
            <Grid item xs={12} md={6}>
                <PaymentForm />
            </Grid>
        </Grid>
    );
};

export default Preference;
