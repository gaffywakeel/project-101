import React from "react";
import PropTypes from "prop-types";
import {Stack, Typography} from "@mui/material";

const ItemSummary = ({title, content}) => {
    return (
        <Stack direction="row" justifyContent="space-between">
            <Typography variant="caption" color="text.secondary">
                {title}
            </Typography>
            <Typography variant="body2">{content}</Typography>
        </Stack>
    );
};

ItemSummary.propTypes = {
    title: PropTypes.oneOfType([PropTypes.node, PropTypes.string]),
    content: PropTypes.oneOfType([PropTypes.node, PropTypes.string])
};

export default ItemSummary;
