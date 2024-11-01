import React, {Fragment, useMemo, useState} from "react";
import PropTypes from "prop-types";
import {Box, Divider, Tab, Tabs} from "@mui/material";
import PageSlider from "@/components/PageSlider";

const CardTabs = ({tabs, variant = "fullWidth", ...otherProps}) => {
    const [value, setValue] = useState(0);

    const tabHeader = useMemo(
        () =>
            tabs.map(({component, ...props}, key) => (
                <Tab key={key} sx={{px: 2}} {...props} />
            )),
        [tabs]
    );

    const tabContent = useMemo(
        () =>
            tabs.map((tab, key) => (
                <Box key={key} component="div">
                    {tab.component}
                </Box>
            )),
        [tabs]
    );

    return (
        <Fragment>
            <Tabs
                {...otherProps}
                variant={variant}
                sx={{bgcolor: "background.neutral"}}
                onChange={(e, v) => setValue(v)}
                value={value}>
                {tabHeader}
            </Tabs>

            <Divider />

            <PageSlider index={value}>{tabContent}</PageSlider>
        </Fragment>
    );
};

CardTabs.propTypes = {
    tabs: PropTypes.arrayOf(PropTypes.object).isRequired,
    variant: PropTypes.string,
    disableSwipe: PropTypes.bool
};

export default CardTabs;
