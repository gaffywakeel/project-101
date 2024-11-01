import {useAuth} from "@/models/Auth";
import {useMemo} from "react";

export function useTokenField() {
    const auth = useAuth();

    return useMemo(() => {
        return auth.requireTwoFactor() ? "token" : "password";
    }, [auth]);
}
