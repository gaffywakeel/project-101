import React from "react";
import {useAuth} from "@/models/Auth";
import UserSetup from "./UserSetup";

const RequireUserSetup = ({children}) => {
    const auth = useAuth();

    if (!auth.requireUserSetup()) return children;

    return <UserSetup />;
};

export default RequireUserSetup;
