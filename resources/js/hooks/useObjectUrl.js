import {useEffect, useMemo} from "react";
import {isString} from "lodash";

export function useObjectUrl(file) {
    const url = useMemo(() => {
        return file instanceof Blob ? URL.createObjectURL(file) : null;
    }, [file]);

    useEffect(() => {
        return () => isString(url) && URL.revokeObjectURL(url);
    }, [url]);

    return url;
}
