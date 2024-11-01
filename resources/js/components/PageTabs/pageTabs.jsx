import React, {Fragment, useCallback, useEffect, useMemo} from "react";
import Page from "@/components/Page";
import HeaderBreadcrumbs from "@/components/HeaderBreadcrumbs";
import {Box, Container, Tab, Tabs} from "@mui/material";
import {useLocation} from "react-router-dom";
import {Icon} from "@iconify/react";
import useSessionStorage from "@/hooks/useSessionStorage";
import PageSlider from "@/components/PageSlider";
import useScreenType from "@/hooks/useScreenType";

const PageTabs = ({initial, tabs, title}) => {
    const location = useLocation();
    const storageKey = `tab.${location.pathname}`;
    const [current, setCurrent] = useSessionStorage(storageKey, initial);
    const {isMobile} = useScreenType();

    const viewIndex = useMemo(() => {
        const index = tabs.findIndex((tab) => tab.value === current);
        return index >= 0 ? index : 0;
    }, [tabs, current]);

    const handleChangeTab = useCallback(
        (event, value) => setCurrent(value),
        [setCurrent]
    );

    useEffect(() => {
        if (!location.state?.tab) return;

        const item = tabs.find((tab) => {
            return tab.value === location.state?.tab;
        });

        if (item) setCurrent(item.value);
    }, [location, tabs, setCurrent]);

    const tabHeader = useMemo(() => {
        return tabs.map((tab) => (
            <Tab
                key={tab.value}
                value={tab.value}
                icon={<Icon width={20} icon={tab.icon} height={20} />}
                label={tab.label}
                disableRipple
            />
        ));
    }, [tabs]);

    const tabContent = useMemo(() => {
        return tabs.map((tab) => (
            <Fragment key={tab.value}>{tab.component}</Fragment>
        ));
    }, [tabs]);

    return (
        <Page title={title}>
            <Container>
                <HeaderBreadcrumbs />

                <Tabs
                    allowScrollButtonsMobile
                    value={tabs[viewIndex].value}
                    onChange={handleChangeTab}
                    variant="scrollable"
                    scrollButtons="auto">
                    {tabHeader}
                </Tabs>

                <Box sx={{py: 4, mt: 1}}>
                    <PageSlider
                        index={viewIndex}
                        offsetX={isMobile ? 2 : 3}
                        offsetY={4}>
                        {tabContent}
                    </PageSlider>
                </Box>
            </Container>
        </Page>
    );
};

export default PageTabs;
