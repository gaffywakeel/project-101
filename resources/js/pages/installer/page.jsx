import React from "react";
import {useInstaller} from "@/hooks/settings";
import License from "./_components/License";
import Register from "./_components/Register";
import Result403 from "@/components/Result403";
import {isEmpty} from "lodash";

const Installer = () => {
    const installer = useInstaller();

    if (isEmpty(installer)) {
        return <Result403 />;
    }

    return installer.license ? <Register /> : <License />;
};

export {Installer as Component};
export default Installer;
