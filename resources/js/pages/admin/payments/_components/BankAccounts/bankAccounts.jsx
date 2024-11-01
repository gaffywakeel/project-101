import React from "react";
import {Grid} from "@mui/material";
import Banks from "./Banks";
import Accounts from "./Accounts";

const BankAccounts = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12}>
                <Accounts />
            </Grid>

            <Grid item xs={12}>
                <Banks />
            </Grid>
        </Grid>
    );
};

export default BankAccounts;
