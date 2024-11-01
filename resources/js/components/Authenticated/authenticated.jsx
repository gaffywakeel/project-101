import React from "react";
import {useAuth} from "@/models/Auth";
import {Navigate} from "react-router-dom";
import PropTypes from "prop-types";

const Authenticated = ({children}) => {
    const auth = useAuth();

    if (auth.check()) {
        return children;
    }

    return <Navigate to="/auth" replace />;
};

Authenticated.propTypes = {
    children: PropTypes.node.isRequired
};

export default Authenticated;
