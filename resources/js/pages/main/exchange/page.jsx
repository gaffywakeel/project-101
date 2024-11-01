import React, {useEffect} from "react";
import {Outlet} from "react-router-dom";
import {useWalletAccountSelector} from "@/hooks/accounts";
import {fetchWalletAccounts} from "@/redux/slices/wallet";
import {fetchPaymentAccount} from "@/redux/slices/payment";
import {useDispatch} from "react-redux";
import Module from "@/components/Module";
import RequireUserSetup from "@/components/RequireUserSetup";

const Exchange = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(fetchWalletAccounts());
        dispatch(fetchPaymentAccount());
    }, [dispatch]);

    useWalletAccountSelector();

    return <Outlet />;
};

const Component = () => {
    return (
        <Module module="exchange">
            <RequireUserSetup>
                <Exchange />
            </RequireUserSetup>
        </Module>
    );
};

export {Component};
export default Exchange;
