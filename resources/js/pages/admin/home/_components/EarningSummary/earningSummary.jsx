import React from "react";
import ResponsiveCard from "@/components/ResponsiveWidgets/responsiveCard";
import {Divider, Stack} from "@mui/material";
import TotalUsers from "./TotalUsers";
import TotalEarnings from "./TotalEarnings";

const EarningSummary = () => {
    return (
        <ResponsiveCard>
            <Stack
                direction="row"
                divider={<Divider orientation="vertical" flexItem />}
                sx={{height: "100%"}}
                spacing={2}>
                <TotalEarnings />
                <TotalUsers />
            </Stack>
        </ResponsiveCard>
    );
};

EarningSummary.dimensions = {
    lg: {w: 6, h: 2, isResizable: false},
    md: {w: 6, h: 2, isResizable: false},
    sm: {w: 2, h: 2, isResizable: false},
    xs: {w: 1, h: 2, isResizable: false}
};

export default EarningSummary;
