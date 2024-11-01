import {matchPath, useLocation} from "react-router-dom";
import {useCallback, useMemo} from "react";
import {isNull} from "lodash";

const useHomePath = () => {
    const {pathname} = useLocation();

    const checkPath = useCallback(
        (path) => !isNull(matchPath(path, pathname)),
        [pathname]
    );

    return useMemo(() => {
        switch (true) {
            case checkPath("/main"):
                return "/main/home";
            case checkPath("/admin"):
                return "/admin/home";
            case checkPath("/auth"):
                return "/auth/login";
            default:
                return "/";
        }
    }, [checkPath]);
};

export default useHomePath;
