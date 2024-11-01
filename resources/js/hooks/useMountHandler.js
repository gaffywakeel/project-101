import {useEffect, useState} from "react";

function mountHandler() {
    let mounted = true;

    const execute = (callback) => {
        if (mounted) {
            return callback();
        }
    };

    const mount = () => {
        mounted = true;

        return () => {
            mounted = false;
        };
    };

    return {execute, mount};
}

const useMountHandler = () => {
    const [handler] = useState(() => mountHandler());

    useEffect(() => {
        return handler.mount();
    }, [handler]);

    return handler;
};

export default useMountHandler;
