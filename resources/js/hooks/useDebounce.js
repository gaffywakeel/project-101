import {useEffect, useMemo, useRef} from "react";
import {debounce} from "lodash";

const useDebounce = (action, timeout = 0) => {
    const actionRef = useRef(action);

    useEffect(() => {
        actionRef.current = action;
    }, [action]);

    return useMemo(() => {
        return debounce((...args) => {
            return actionRef.current(...args);
        }, timeout);
    }, [timeout]);
};

export default useDebounce;
