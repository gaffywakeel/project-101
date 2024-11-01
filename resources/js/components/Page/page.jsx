import React, {forwardRef, useMemo} from "react";
import PropTypes from "prop-types";
import {Helmet} from "react-helmet-async";
import {Box} from "@mui/material";
import {useMatch} from "react-router-dom";
import context from "@/contexts/AppContext";
import {defineMessages, useIntl} from "react-intl";

const messages = defineMessages({
    admin: {defaultMessage: "%s (Control Panel)"}
});

const Page = forwardRef(({children, title, ...other}, ref) => {
    const intl = useIntl();
    const isAdminPath = useMatch("/admin");

    const content = useMemo(() => {
        return isAdminPath ? intl.formatMessage(messages.admin) : "%s";
    }, [isAdminPath, intl]);

    return (
        <Box ref={ref} {...other}>
            <Helmet titleTemplate={`${content} | ${context.name}`}>
                {title && <title>{title}</title>}
            </Helmet>

            {children}
        </Box>
    );
});

Page.propTypes = {
    children: PropTypes.node.isRequired,
    title: PropTypes.string
};

export default Page;
