import React from "react";
import {Link} from "react-router-dom";
import {Box} from "@mui/material";

const ProfileLink = ({user, sx, ...otherProps}) => {
    return (
        <Box
            component={Link}
            sx={{textDecoration: "none", color: "text.primary", ...sx}}
            to={`/main/profile/${user.name}`}
            {...otherProps}
        />
    );
};

export default ProfileLink;
