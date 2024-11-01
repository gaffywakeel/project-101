import React from "react";
import {styled} from "@mui/material/styles";
import Logo from "@/components/Logo";

const LogoContainer = styled("header")(({theme}) => ({
    lineHeight: 0,
    position: "absolute",
    left: 0,
    top: 0,
    width: "100%",
    padding: theme.spacing(3, 3, 0),
    [theme.breakpoints.up("sm")]: {
        padding: theme.spacing(5, 5, 0)
    }
}));

const LogoLayout = () => {
    return (
        <LogoContainer>
            <Logo to="/auth/login" />
        </LogoContainer>
    );
};

export default LogoLayout;
