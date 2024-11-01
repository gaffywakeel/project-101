import React from "react";
import PropTypes from "prop-types";
import {styled} from "@mui/material/styles";
import {Typography} from "@mui/material";
import Logo from "@/components/Logo";

function HintLayout(props) {
    return (
        <HintContainer>
            <Logo to={"/"} />
            <HintContent variant="body2" {...props} />
        </HintContainer>
    );
}

const HintContainer = styled("header")(({theme}) => ({
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
    position: "absolute",
    top: 0,
    lineHeight: 0,
    width: "100%",
    zIndex: 9,
    padding: theme.spacing(3),
    [theme.breakpoints.up("md")]: {
        alignItems: "flex-start",
        padding: theme.spacing(7, 5, 0, 7)
    }
}));

const HintContent = styled(Typography)(({theme}) => ({
    display: "none",
    [theme.breakpoints.up("md")]: {
        marginTop: theme.spacing(-2)
    },
    [theme.breakpoints.up("sm")]: {
        display: "block"
    }
}));

HintLayout.propTypes = {children: PropTypes.node};

export default HintLayout;
