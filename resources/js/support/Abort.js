import {useEffect, useState} from "react";

class Abort {
    constructor() {
        this.reset();
    }

    reset() {
        this.controller = new AbortController();
    }

    isAborted() {
        return this.controller.signal.aborted;
    }

    abort() {
        this.controller.abort();
    }
}

/**
 * Use Abort
 *
 * @returns {Abort}
 */
export function useAbort() {
    const [abort] = useState(() => new Abort());

    useEffect(() => {
        if (abort.isAborted()) {
            abort.reset();
        }

        return () => abort.abort();
    }, [abort]);

    return abort;
}
