import React, {useMemo, useState} from "react";
import {useLoaderData, useRevalidator} from "react-router-dom";
import {route, routeRequest} from "@/services/Http";
import {UserProvider} from "@/contexts/UserContext";
import User from "@/models/User";
import {defineMessages, useIntl} from "react-intl";
import Iconify from "@/components/Iconify";
import account from "@iconify/icons-ic/round-account-box";
import Display from "./_components/Display";
import people from "@iconify/icons-eva/people-fill";
import Followers from "./_components/Followers";
import heart from "@iconify/icons-eva/heart-fill";
import Following from "./_components/Following";
import shield from "@iconify/icons-eva/shield-fill";
import Manage from "./_components/Manage";
import {Box, Card, Container, Tab, Tabs} from "@mui/material";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import Background from "./_components/Background";
import PageSlider from "@/components/PageSlider";
import Page from "@/components/Page";
import {styled} from "@mui/material/styles";
import useScreenType from "@/hooks/useScreenType";

const messages = defineMessages({
    title: {defaultMessage: "{name}'s profile"},
    display: {defaultMessage: "Display"},
    followers: {defaultMessage: "Followers"},
    following: {defaultMessage: "Following"},
    manage: {defaultMessage: "Manage"}
});

const Profile = () => {
    const intl = useIntl();
    const data = useLoaderData();
    const {isMobile} = useScreenType();
    const [value, setValue] = useState(0);
    const validator = useRevalidator();

    const user = useMemo(() => {
        return User.use(data);
    }, [data]);

    const tabs = useMemo(() => {
        const stack = [
            {
                label: intl.formatMessage(messages.display),
                icon: <Iconify icon={account} width={20} height={20} />,
                component: <Display />
            },
            {
                label: intl.formatMessage(messages.followers),
                icon: <Iconify icon={people} width={20} height={20} />,
                component: <Followers />
            },
            {
                label: intl.formatMessage(messages.following),
                icon: <Iconify icon={heart} width={20} height={20} />,
                component: <Following />
            }
        ];

        if (user.manage_policy) {
            stack.push({
                label: intl.formatMessage(messages.manage),
                icon: <Iconify icon={shield} width={20} height={20} />,
                component: <Manage />
            });
        }

        return stack;
    }, [intl, user]);

    const tabHeader = useMemo(
        () =>
            tabs.map((tab, i) => (
                <Tab icon={tab.icon} label={tab.label} disableRipple key={i} />
            )),
        [tabs]
    );

    const tabContent = useMemo(
        () =>
            tabs.map((tab, i) => (
                <Box component="div" key={i}>
                    {tab.component}
                </Box>
            )),
        [tabs]
    );

    const fetchUser = () => {
        validator.revalidate();
    };

    return (
        <Page title={intl.formatMessage(messages.title, {name: user.name})}>
            <Container>
                <UserProvider user={user} fetchUser={fetchUser}>
                    <HeaderBreadcrumbs />

                    <StyledCard>
                        <Background />

                        <TabsContainer>
                            <Tabs
                                value={value}
                                allowScrollButtonsMobile
                                variant="scrollable"
                                onChange={(e, v) => setValue(v)}
                                scrollButtons="auto"
                                sx={{zIndex: 999}}>
                                {tabHeader}
                            </Tabs>
                        </TabsContainer>
                    </StyledCard>

                    <Box sx={{py: 4, mt: 3}}>
                        <PageSlider
                            index={value}
                            offsetX={isMobile ? 2 : 3}
                            offsetY={4}>
                            {tabContent}
                        </PageSlider>
                    </Box>
                </UserProvider>
            </Container>
        </Page>
    );
};

/* eslint-disable react-refresh/only-export-components */
export async function loader({request, params}) {
    const url = route("user-profile.get", {user: params.name});
    return await routeRequest(request).get(url);
}

const StyledCard = styled(Card)({
    height: 280,
    position: "relative"
});

const TabsContainer = styled("div")(({theme}) => ({
    display: "flex",
    backgroundColor: theme.palette.background.paper,
    position: "absolute",
    width: "100%",
    bottom: 0,
    paddingLeft: theme.spacing(3),
    paddingRight: theme.spacing(3),
    [theme.breakpoints.up("sm")]: {
        justifyContent: "center"
    },
    [theme.breakpoints.up("md")]: {
        justifyContent: "flex-end"
    }
}));

export {Profile as Component};
export default Profile;
