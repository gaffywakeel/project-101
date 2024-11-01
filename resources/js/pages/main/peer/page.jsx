import React, {useEffect} from "react";
import {useDispatch} from "react-redux";
import {fetchWalletAccounts} from "@/redux/slices/wallet";
import {fetchPaymentAccount} from "@/redux/slices/payment";
import {useWalletAccountSelector} from "@/hooks/accounts";
import Module from "@/components/Module";
import {Outlet} from "react-router-dom";
import RequireUserSetup from "@/components/RequireUserSetup";

const Peer = () => {
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
        <Module module="peer">
            <RequireUserSetup>
                <Peer />
            </RequireUserSetup>
        </Module>
    );
};

export {Component};
export default Peer;
