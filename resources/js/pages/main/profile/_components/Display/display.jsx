import React from "react";
import {Grid, Stack} from "@mui/material";
import FollowStats from "./FollowStats";
import UserDetails from "./UserDetails";
import PeerOffers from "./PeerOffers";

const Display = () => {
    return (
        <Grid container spacing={3}>
            <Grid item xs={12} md={4}>
                <Stack spacing={3}>
                    <FollowStats />
                    <UserDetails />
                </Stack>
            </Grid>

            <Grid item xs={12} md={8}>
                <Stack spacing={3}>
                    <PeerOffers type="buy" />
                    <PeerOffers type="sell" />
                </Stack>
            </Grid>
        </Grid>
    );
};

export default Display;
