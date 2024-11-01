import React from "react";
import {styled} from "@mui/material/styles";
import {CircularProgress} from "@mui/material";

const Container = styled("div")(({size}) => ({
    display: "flex",
    minHeight: size * 2.5,
    height: "100%",
    justifyContent: "center",
    alignItems: "center"
}));

function LoadingScreen({size = 40, thickness = 3.6, ...other}) {
    return (
        <Container size={size} {...other}>
            <CircularProgress size={size} thickness={thickness} />
        </Container>
    );
}

export default LoadingScreen;
