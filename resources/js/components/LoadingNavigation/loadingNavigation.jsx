import React from "react";
import {LinearProgress} from "@mui/material";
import {styled} from "@mui/material/styles";
import {useNavigation} from "react-router-dom";

const LoadingNavigation = () => {
    const navigation = useNavigation();
    if (navigation.state === "idle") return null;
    return <StyledLinearProgress />;
};

const StyledLinearProgress = styled(LinearProgress)(({theme}) => ({
    position: "fixed",
    width: "100%",
    zIndex: theme.zIndex.snackbar
}));

export default LoadingNavigation;
