import React from "react";
import {Outlet} from "react-router-dom";
import Module from "@/components/Module";
import RequireUserSetup from "@/components/RequireUserSetup";

const Stake = () => {
    return (
        <Module module="stake">
            <RequireUserSetup>
                <Outlet />
            </RequireUserSetup>
        </Module>
    );
};

export {Stake as Component};
export default Stake;
