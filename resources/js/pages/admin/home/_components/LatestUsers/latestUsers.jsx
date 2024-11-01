import React, {useCallback, useEffect, useState} from "react";
import ResponsiveCard from "@/components/ResponsiveWidgets/responsiveCard";
import {FormattedMessage} from "react-intl";
import {CardContent, CardHeader, Stack, Typography} from "@mui/material";
import FlagIcon from "@/components/FlagIcon";
import UserAvatar from "@/components/UserAvatar";
import {isEmpty, toLower} from "lodash";
import Spin from "@/components/Spin";
import Scrollbar from "@/components/Scrollbar";
import {errorHandler, route, useRequest} from "@/services/Http";
import ProfileLink from "@/components/ProfileLink";
import {formatDateFromNow} from "@/utils/formatter";

const LatestUsers = () => {
    const [data, setData] = useState([]);
    const [request, loading] = useRequest();

    const fetchData = useCallback(() => {
        request
            .get(route("admin.statistics.latest-users"))
            .then(({data}) => setData(data))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    return (
        <ResponsiveCard>
            <CardHeader
                title={<FormattedMessage defaultMessage="Latest Users" />}
            />

            <Scrollbar>
                <CardContent>
                    <Spin spinning={loading}>
                        <Stack spacing={3}>
                            {data.map((user, key) => (
                                <UserListItem key={key} user={user} />
                            ))}
                        </Stack>
                    </Spin>
                </CardContent>
            </Scrollbar>
        </ResponsiveCard>
    );
};

function UserListItem({user}) {
    const location = user.location;

    return (
        <Stack direction="row" alignItems="center" spacing={2}>
            <UserAvatar user={user} />

            <Stack sx={{flexGrow: 1, minWidth: 0}}>
                <Stack
                    direction="row"
                    alignItems="center"
                    sx={{minWidth: 0}}
                    spacing={1}>
                    <Typography
                        user={user}
                        component={ProfileLink}
                        variant="subtitle2"
                        noWrap>
                        {user.name}
                    </Typography>

                    {user.country && <FlagIcon code={toLower(user.country)} />}
                </Stack>

                {user.email && (
                    <Typography
                        variant="caption"
                        sx={{color: "text.secondary"}}
                        noWrap>
                        {user.email}
                    </Typography>
                )}
            </Stack>

            <Stack sx={{minWidth: 0, textAlign: "right"}}>
                <Typography variant="body2" noWrap>
                    {formatDateFromNow(user.created_at)}
                </Typography>

                {!isEmpty(location) && (
                    <Typography variant="caption" color="text.secondary" noWrap>
                        {`${location.state_name} (${location.iso_code})`}
                    </Typography>
                )}
            </Stack>
        </Stack>
    );
}

LatestUsers.dimensions = {
    lg: {w: 4, h: 3, minW: 4, minH: 3},
    md: {w: 4, h: 3, minW: 4, minH: 3},
    sm: {w: 2, h: 3, minW: 2, minH: 3},
    xs: {w: 1, h: 3, minW: 1, minH: 3}
};

export default LatestUsers;
