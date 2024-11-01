import React, {useMemo} from "react";
import {Box, Stack, Typography} from "@mui/material";
import PropTypes from "prop-types";
import {splitNestedKeys} from "@/utils/helpers";
import {useLocation, useParams} from "react-router-dom";
import {defaultTo, isEmpty} from "lodash";
import Breadcrumbs from "@/components/Breadcrumbs";
import useScreenType from "@/hooks/useScreenType";
import {useRouteHelper} from "@/support/RouteHelper";

const HeaderBreadcrumbs = ({action, title, ...otherProps}) => {
    const params = useParams();
    const helper = useRouteHelper();
    const {isMobile} = useScreenType();
    const {pathname} = useLocation();

    const key = useMemo(() => {
        return helper.getKeyByUrl(pathname);
    }, [pathname, helper]);

    const links = useMemo(() => {
        return splitNestedKeys(key).map((k) => ({
            name: helper.getName(k),
            href: helper.generatePath(k, params),
            icon: helper.getIcon(k),
            ...{key: k}
        }));
    }, [key, params, helper]);

    return (
        <Stack direction="row" alignItems="flex-start" mb={5} spacing={2}>
            <Stack spacing={1} sx={{flexGrow: 1, minWidth: 100}}>
                <Typography variant="h4" noWrap>
                    {defaultTo(title, helper.getName(key))}
                </Typography>

                {!isEmpty(links) && !isMobile && (
                    <Breadcrumbs links={links} {...otherProps} />
                )}
            </Stack>

            {action && <Box sx={{minWidth: 0}}>{action}</Box>}
        </Stack>
    );
};

HeaderBreadcrumbs.propTypes = {
    action: PropTypes.node,
    sx: PropTypes.object
};

export default HeaderBreadcrumbs;
