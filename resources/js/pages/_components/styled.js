import {Box} from "@mui/material";
import {styled} from "@mui/material/styles";
import {HEADER_CONFIG, NAVBAR_CONFIG} from "@/theme/layout";

export const Dashboard = styled(Box)(({theme}) => ({
    [theme.breakpoints.up("lg")]: {
        display: "flex",
        minHeight: "100%"
    }
}));
export const DashboardContent = styled("main", {
    shouldForwardProp: (prop) => prop !== "collapseClick"
})(({collapseClick, theme}) => ({
    overflow: "auto",
    paddingTop: HEADER_CONFIG.MOBILE_HEIGHT + 24,
    paddingBottom: HEADER_CONFIG.MOBILE_HEIGHT + 24,
    flexGrow: 1,
    minHeight: "100%",
    [theme.breakpoints.up("lg")]: {
        paddingRight: theme.spacing(2),
        paddingLeft: theme.spacing(2),
        paddingTop: HEADER_CONFIG.DASHBOARD_DESKTOP_HEIGHT + 24,
        paddingBottom: HEADER_CONFIG.DASHBOARD_DESKTOP_HEIGHT + 24,
        width: `calc(100% - ${NAVBAR_CONFIG.DASHBOARD_WIDTH}px)`,
        ...(collapseClick && {
            marginLeft: NAVBAR_CONFIG.DASHBOARD_COLLAPSE_WIDTH
        })
    }
}));
