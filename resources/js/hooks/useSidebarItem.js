import {useCallback} from "react";
import {useAuth} from "@/models/Auth";
import {useRouteHelper} from "@/support/RouteHelper";
import useModules from "@/hooks/useModules";
import {isString} from "lodash";

/**
 * @returns {(function({key: *, params: *, permission: *, module: *, children: *}): ({path: *, children: *, icon: *, title: *, key: *}))}
 */
const useSidebarItem = () => {
    const auth = useAuth();
    const helper = useRouteHelper();
    const modules = useModules();

    return useCallback(
        ({key, params, permission, module, children}) => {
            if (isString(module) && !modules[module]) {
                return null;
            }

            if (permission && !auth.can(permission)) {
                return null;
            }

            return {
                title: helper.getName(key),
                path: helper.generatePath(key, params),
                icon: helper.getIcon(key),
                ...{key, children}
            };
        },
        [auth, modules, helper]
    );
};

export default useSidebarItem;
