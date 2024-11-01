import React from "react";
import {Grid} from "@mui/material";
import UserDocuments from "./UserDocuments";
import UserAddress from "./UserAddress";

const Verification = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12}>
                <UserDocuments />
            </Grid>

            <Grid item xs={12}>
                <UserAddress />
            </Grid>
        </Grid>
    );
};

export default Verification;
