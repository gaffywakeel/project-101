import React from "react";
import {useAuth} from "@/models/Auth";
import {Navigate} from "react-router-dom";

const Unauthenticated = ({children}) => {
    const auth = useAuth();

    if (!auth.check()) {
        return children;
    }

    return <Navigate to="/main" replace />;
};

export default Unauthenticated;
