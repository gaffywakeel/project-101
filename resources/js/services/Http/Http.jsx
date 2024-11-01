import axios, {isCancel as isCancelError} from "axios";
import context from "@/contexts/AppContext";
import React, {useEffect, useMemo, useState} from "react";
import useMountHandler from "@/hooks/useMountHandler";
import {
    assign,
    castArray,
    first,
    flatten,
    forOwn,
    get,
    isEmpty,
    isFunction,
    isString,
    values
} from "lodash";
import {getNamePath, notify} from "@/utils/index";
import {Button} from "@mui/material";
import {FormattedMessage} from "react-intl";
import {json} from "react-router-dom";

const csrfToken = context.csrfToken;
axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
axios.defaults.paramsSerializer = {indexes: true};

export {axios, csrfToken};

export default class Http {
    constructor(config) {
        this.request = axios.create(config);

        if (!config?.signal) {
            this.resetSignal();
        }
    }

    resetSignal() {
        this.controller = new AbortController();
        this.request.defaults.signal = this.controller.signal;
    }

    abort() {
        this.controller?.abort();
    }

    isAborted() {
        return this.request.defaults.signal.aborted;
    }
}

const refreshPage = (key) => {
    notify.close(key);
    window.location.reload();
};

const triggerUnavailable = (data) => {
    notify.warning(data.message, {
        persist: true,
        anchorOrigin: {vertical: "top", horizontal: "center"},
        action: (key) => (
            <Button
                size="small"
                variant="contained"
                onClick={() => refreshPage(key)}
                color="warning">
                <FormattedMessage defaultMessage="Refresh" />
            </Button>
        )
    });
};

const isUnavailable = (status) => {
    return [401, 419].includes(status);
};

/**
 * Request hook
 *
 * @returns {[axios, boolean]}
 */
export const useRequest = () => {
    const handler = useMountHandler();
    const [loading, setLoading] = useState(false);
    const [service] = useState(() => new Http());

    useEffect(() => {
        if (service.isAborted()) {
            service.resetSignal();
        }

        return () => service.abort();
    }, [service]);

    useEffect(() => {
        const interceptors = service.request.interceptors;

        const requestInterceptor = interceptors.request.use((config) => {
            handler.execute(() => setLoading(true));

            return config;
        });

        const responseInterceptor = interceptors.response.use(
            (response) => {
                handler.execute(() => setLoading(false));

                return response;
            },
            (error) => {
                handler.execute(() => setLoading(false));

                error.canceled = isCancelError(error);

                return Promise.reject(error);
            }
        );

        return () => {
            interceptors.response.eject(responseInterceptor);
            interceptors.request.eject(requestInterceptor);
        };
    }, [service, handler]);

    return [service.request, loading];
};

/**
 * Form request hook
 *
 * @param form
 * @returns {[axios, boolean]}
 */
export const useFormRequest = (form) => {
    const handler = useMountHandler();
    const [loading, setLoading] = useState(false);
    const [service] = useState(() => new Http());

    useEffect(() => {
        if (service.isAborted()) {
            service.resetSignal();
        }

        return () => service.abort();
    }, [service]);

    useEffect(() => {
        const interceptors = service.request.interceptors;

        const requestInterceptor = interceptors.request.use((config) => {
            handler.execute(() => setLoading(true));

            return config;
        });

        const responseInterceptor = interceptors.response.use(
            (response) => {
                handler.execute(() => setLoading(false));

                return response;
            },
            (error) => {
                handler.execute(() => setLoading(false));

                error.canceled = isCancelError(error);
                error.form = form;

                return Promise.reject(error);
            }
        );

        return () => {
            interceptors.response.eject(responseInterceptor);
            interceptors.request.eject(requestInterceptor);
        };
    }, [service, form, handler]);

    return [service.request, loading];
};

export function useUploadRequest() {
    const handler = useMountHandler();
    const [loading, setLoading] = useState(false);
    const [service] = useState(() => new Http());

    useEffect(() => {
        if (service.isAborted()) {
            service.resetSignal();
        }

        return () => service.abort();
    }, [service]);

    useEffect(() => {
        const interceptors = service.request.interceptors;

        const requestInterceptor = interceptors.request.use((config) => {
            handler.execute(() => setLoading(true));

            return config;
        });

        const responseInterceptor = interceptors.response.use(
            (response) => {
                handler.execute(() => setLoading(false));

                return response;
            },
            (error) => {
                handler.execute(() => setLoading(false));

                if (error.response) {
                    const {status, data} = error.response;

                    if (isUnavailable(status)) {
                        triggerUnavailable(data);
                    }
                }

                error.canceled = isCancelError(error);

                return Promise.reject(error);
            }
        );
        return () => {
            interceptors.request.eject(requestInterceptor);
            interceptors.response.eject(responseInterceptor);
        };
    }, [service, handler]);

    const request = useMemo(() => {
        return function (options) {
            const form = new FormData();
            form.set(options.filename, options.file);

            forOwn(options.data, (value, key) => {
                form.append(key, value);
            });

            service.request
                .post(options.action, form, {
                    headers: assign(
                        {"Content-Type": "multipart/form-data"},
                        options.headers
                    ),

                    onUploadProgress(progress) {
                        const {onProgress} = options;

                        if (isFunction(onProgress)) {
                            const {loaded, total} = progress;
                            const percent = Math.round((loaded * 100) / total);
                            return onProgress({percent});
                        }
                    }
                })
                .then((response) => {
                    const {onSuccess} = options;

                    if (isFunction(onSuccess)) {
                        const {data, request} = response;
                        return onSuccess(data, request);
                    }
                })
                .catch((error) => {
                    const {onError} = options;

                    if (!error.canceled && error.response) {
                        const data = error.response.data;

                        if (isFunction(onError)) {
                            return onError(error, data);
                        }
                    }
                });
        };
    }, [service]);

    return [request, loading];
}

export function notifyError(error) {
    const {errors, message} = get(error, "response.data", {});

    if (!isEmpty(errors)) {
        flatten(values(errors)).forEach((error) => {
            notify.error(error);
        });
    } else if (isString(message)) {
        notify.error(message);
    }
}

export function errorHandler(callback) {
    return (error) => {
        if (error.response) {
            const {status, data} = error.response;

            if (isUnavailable(status)) {
                triggerUnavailable(data);
            } else {
                const {errors, message} = data;
                const form = error.form;

                if (!isEmpty(errors)) {
                    const fields = [];

                    forOwn(errors, (errorList, key) => {
                        const name = getNamePath(key);
                        fields.push({name, errors: errorList});

                        if (!form?.getFieldValue(name)) {
                            castArray(errorList).forEach((e) => {
                                return notify.error(e);
                            });
                        }
                    });

                    form?.setFields(fields);
                    form?.scrollToField(first(fields).name);
                } else if (isString(message)) {
                    notify.error(message);
                }
            }
        }

        if (!error.canceled && callback) {
            return callback(error);
        }
    };
}

export function routeRequest(request) {
    const service = new Http({
        signal: request.signal
    });

    service.request.interceptors.response.use(
        (response) => {
            return json(response.data, {
                headers: response.headers,
                statusText: response.statusText,
                status: response.status
            });
        },
        (error) => {
            if (error.response) {
                return Promise.reject(
                    json(error.response.data, {
                        headers: error.response.headers,
                        statusText: error.response.statusText,
                        status: error.response.status
                    })
                );
            } else {
                return Promise.reject(error);
            }
        }
    );

    return service.request;
}

export function thunkRequest() {
    const service = new Http();

    service.request.interceptors.response.use(
        (response) => {
            return response.data;
        },
        (error) => {
            if (error.response) {
                const {status, data} = error.response;

                if (isUnavailable(status)) {
                    triggerUnavailable(data);
                } else if (data.message) {
                    notify.error(data.message);
                }
            }

            return Promise.reject(error);
        }
    );

    return service.request;
}
