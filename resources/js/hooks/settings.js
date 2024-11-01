import {useSelector} from "react-redux";
import {get} from "lodash";

export function useRecaptcha() {
    return useSelector((state) => {
        return get(state, "settings.recaptcha");
    });
}

export function useBrand() {
    return useSelector((state) => {
        return get(state, "settings.brand");
    });
}

export function useExchangeBaseCurrency() {
    return useSelector((state) => {
        return get(state, "settings.currency");
    });
}

export function useInstaller() {
    return useSelector((state) => {
        return get(state, "settings.installer");
    });
}

export function useDemo() {
    return useSelector((state) => {
        return get(state, "settings.demo");
    });
}
