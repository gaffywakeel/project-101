import React, {useMemo} from "react";
import RouteHelper from "@/support/RouteHelper";
import {useIntl} from "react-intl";
import {createBrowserRouter, RouterProvider} from "react-router-dom";
import {RouteHelperProvider} from "@/contexts/RouteHelperContext";
import LoadingScreen from "../LoadingScreen";

const Router = ({getRoutes}) => {
    const intl = useIntl();

    const helper = useMemo(() => {
        return new RouteHelper(getRoutes(), intl);
    }, [intl, getRoutes]);

    const router = createBrowserRouter(helper.getRoutes());

    return (
        <RouteHelperProvider instance={helper}>
            <RouterProvider
                fallbackElement={<LoadingScreen />}
                router={router}
            />
        </RouteHelperProvider>
    );
};

export default Router;
