import React from "react";
import PropTypes from "prop-types";
import SimpleBarReact from "simplebar-react";
import {alpha, styled} from "@mui/material/styles";
import {Box} from "@mui/material";

function Scrollbar({...otherProps}) {
    return (
        <StyledBox>
            <StyledSimpleBar
                timeout={500}
                clickOnTrack={false}
                {...otherProps}
            />
        </StyledBox>
    );
}

Scrollbar.propTypes = {
    children: PropTypes.node.isRequired,
    sx: PropTypes.object
};

// TODO: remove maxHeight here which makes scrollbar useful only for y-axis

const StyledSimpleBar = styled(SimpleBarReact)(({theme}) => ({
    "& .simplebar-scrollbar": {
        "&:before": {backgroundColor: alpha(theme.palette.grey[600], 0.48)},
        "&.simplebar-visible:before": {opacity: 1}
    },
    "& .simplebar-content-wrapper::-webkit-scrollbar": {display: "none"},
    "& .simplebar-track.simplebar-vertical": {width: 10},
    "& .simplebar-track.simplebar-horizontal .simplebar-scrollbar": {height: 6},
    "& .simplebar-mask": {zIndex: "inherit"},
    maxHeight: "100%"
}));

const StyledBox = styled(Box)({
    flexGrow: 1,
    overflow: "hidden",
    height: "100%"
});

export default Scrollbar;
