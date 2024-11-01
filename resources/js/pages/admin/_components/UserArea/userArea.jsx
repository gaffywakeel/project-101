import React from "react";
import {Icon} from "@iconify/react";
import globalFill from "@iconify-icons/ri/global-fill";
import {Link as RouterLink} from "react-router-dom";
import {IconButton} from "@mui/material";

const UserArea = () => {
    return (
        <IconButton component={RouterLink} to={"/main/home"}>
            <Icon icon={globalFill} />
        </IconButton>
    );
};

export default UserArea;
