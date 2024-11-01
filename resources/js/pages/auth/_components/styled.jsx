import React from "react";
import {Card, Stack} from "@mui/material";
import {styled} from "@mui/material/styles";
import Page from "@/components/Page";

const SectionCard = ({children, ...props}) => {
    return (
        <StyledCard {...props}>
            <StyledStack justifyContent="center">{children}</StyledStack>
        </StyledCard>
    );
};

export const StyledPage = styled(Page)(({theme}) => ({
    [theme.breakpoints.up("md")]: {display: "flex"}
}));

export const AuthContent = styled("div")(({theme}) => ({
    maxWidth: 480,
    display: "flex",
    margin: "auto",
    padding: theme.spacing(12, 0),
    flexDirection: "column",
    justifyContent: "center",
    minHeight: "100vh"
}));

const StyledStack = styled(Stack)({
    position: "relative"
});

const StyledCard = styled(Card)(({theme}) => ({
    width: "100%",
    position: "relative",
    maxWidth: 464,
    margin: theme.spacing(2, 0, 2, 2),
    padding: theme.spacing(15, 5)
}));

export {SectionCard};
