import React from "react";
import {Outlet} from "react-router-dom";
import Module from "@/components/Module";
import RequireUserSetup from "@/components/RequireUserSetup";

const Commerce = () => {
    return (
        <Module module="commerce">
            <RequireUserSetup>
                <Outlet />
            </RequireUserSetup>
        </Module>
    );
};

export {Commerce as Component};
export default Commerce;
