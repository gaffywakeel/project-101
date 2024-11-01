import React, {Fragment, useEffect} from "react";
import {Outlet, ScrollRestoration} from "react-router-dom";
import LoadingNavigation from "@/components/LoadingNavigation";
import Unauthenticated from "@/components/Unauthenticated";
import {useDispatch} from "react-redux";
import {
    fetchCountries,
    fetchOperatingCountries,
    fetchSupportedCurrencies,
    fetchWallets
} from "@/redux/slices/global";

const Auth = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(fetchCountries());
        dispatch(fetchSupportedCurrencies());
        dispatch(fetchOperatingCountries());
        dispatch(fetchWallets());
    }, [dispatch]);

    return (
        <Fragment>
            <LoadingNavigation />
            <Outlet />
            <ScrollRestoration />
        </Fragment>
    );
};

const Component = () => {
    return (
        <Unauthenticated>
            <Auth />
        </Unauthenticated>
    );
};

export {Component};
export default Auth;
