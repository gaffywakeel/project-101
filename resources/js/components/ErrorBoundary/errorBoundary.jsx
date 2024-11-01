import React from "react";
import {isRouteErrorResponse, useRouteError} from "react-router-dom";
import Result500 from "../Result500";
import Result404 from "../Result404";
import Result403 from "../Result403";

const ErrorBoundary = () => {
    const error = useRouteError();

    if (isRouteErrorResponse(error)) {
        switch (error.status) {
            case 404:
                return <Result404 />;
            case 403:
                return <Result403 />;
        }
    }

    console.error(error);

    return <Result500 />;
};

export default ErrorBoundary;
