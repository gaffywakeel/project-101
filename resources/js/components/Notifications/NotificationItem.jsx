import React, {forwardRef, useCallback} from "react";
import {
    Avatar,
    Box,
    ListItem,
    ListItemAvatar,
    ListItemText,
    Skeleton,
    Typography
} from "@mui/material";
import {Icon} from "@iconify/react";
import clockFill from "@iconify/icons-eva/clock-fill";
import PropTypes from "prop-types";
import defaultIcon from "@iconify/icons-flat-color-icons/sms";
import currencyExchange from "@iconify/icons-flat-color-icons/currency-exchange";
import walletIcon from "@iconify/icons-flat-color-icons/sales-performance";
import userManager from "@iconify/icons-flat-color-icons/manager";
import {errorHandler, route, useRequest} from "@/services/Http";
import {startsWith as _startsWith} from "lodash";
import {useIntervalResult} from "@/hooks/useIntervalResult";
import {formatDateFromNow} from "@/utils/formatter";

const NotificationItem = ({
    notification,
    updateNotification,
    fetchTotalUnread
}) => {
    const [request, loading] = useRequest();

    const avatarContent = useCallback((notification, icon = defaultIcon) => {
        const startsWith = (value) => {
            return _startsWith(notification.type, value);
        };

        if (startsWith("App\\Notifications\\Wallet")) {
            icon = walletIcon;
        }

        if (startsWith("App\\Notifications\\Payment")) {
            icon = currencyExchange;
        }

        if (startsWith("App\\Notifications\\Auth")) {
            icon = userManager;
        }

        return <Icon icon={icon} width={20} height={20} />;
    }, []);

    const markAsRead = useCallback(() => {
        if (!notification.read_at) {
            const url = route("user.notification.mark-as-read", {
                id: notification.id
            });

            request
                .patch(url)
                .then(({data: item}) => {
                    updateNotification(item);
                    fetchTotalUnread();
                })
                .catch(errorHandler());
        }
    }, [notification, request, updateNotification, fetchTotalUnread]);

    const createdAt = useIntervalResult(() => {
        return formatDateFromNow(notification.created_at);
    });

    return (
        <ListItem
            disableGutters
            onClick={markAsRead}
            button={!notification.read_at}
            disabled={loading}
            sx={{
                ...(!notification.read_at && {
                    bgcolor: "action.selected"
                }),
                py: 1.5,
                px: 2.5,
                mt: "1px"
            }}>
            <ListItemAvatar>
                <Avatar sx={{bgcolor: "background.neutral"}}>
                    {avatarContent(notification)}
                </Avatar>
            </ListItemAvatar>

            <ListItemText
                primary={
                    <Typography
                        sx={{lineHeight: 1.35, fontSize: "0.8rem"}}
                        variant="body2">
                        {notification.data.content}
                    </Typography>
                }
                secondary={
                    <Typography
                        variant="caption"
                        sx={{
                            display: "flex",
                            color: "text.disabled",
                            alignItems: "center",
                            mt: 0.5
                        }}>
                        <Box
                            sx={{mr: 0.5, width: 16, height: 16}}
                            component={Icon}
                            icon={clockFill}
                        />

                        {createdAt}
                    </Typography>
                }
            />
        </ListItem>
    );
};

const NotificationLoader = forwardRef((props, ref) => {
    return (
        <ListItem ref={ref} disableGutters sx={{px: 2.5, mt: "1px"}} button>
            <ListItemAvatar>
                <Skeleton variant="circular">
                    <Avatar />
                </Skeleton>
            </ListItemAvatar>

            <ListItemText
                secondary={<Skeleton width="100%" height={20} />}
                primary={<Skeleton width="100%" height={40} />}
            />
        </ListItem>
    );
});

NotificationItem.propTypes = {notification: PropTypes.object.isRequired};

export {NotificationLoader};
export default NotificationItem;
