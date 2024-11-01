import {joinPath} from "@/utils/helpers";
import RouteHelperContext from "@/contexts/RouteHelperContext";
import {generatePath, matchPath} from "react-router-dom";
import {map, pick} from "lodash";
import {useContext} from "react";

class RouteHelper {
    #cache = new Map();
    #routes = [];

    constructor(data, intl) {
        this.#routes = this.#parse(data);
        this.intl = intl;
    }

    #parse(dataset, keyChain = [], pathChain = []) {
        return map(dataset, (record) => {
            const cache = pick(record, cacheProps);
            const route = pick(record, routeProps);

            if (record.index === true) return route;
            const activePathChain = pathChain.concat(record.path ?? []);
            const activeKeyChain = keyChain.concat(record.key ?? []);

            if (record.children) {
                route.children = this.#parse(
                    record.children,
                    activeKeyChain,
                    activePathChain
                );
            }

            if (record.key && record.path) {
                route.id = activeKeyChain.join(".");
                cache.fullPath = joinPath(...activePathChain);
                this.#cache.set(route.id, cache);
            }

            return route;
        });
    }

    get(key) {
        if (!this.#cache.has(key)) {
            throw new Error("Route does not exists.");
        }

        return this.#cache.get(key);
    }

    generatePath(key, params) {
        const path = this.get(key).fullPath;
        return generatePath(path, params);
    }

    getName(key) {
        const message = this.get(key).descriptor;
        return this.intl.formatMessage(message);
    }

    getIcon(key) {
        return this.get(key).icon;
    }

    getPath(key) {
        return this.get(key).fullPath;
    }

    getRoutePath(key) {
        return this.get(key).path;
    }

    getKeyByUrl(url) {
        for (const [key, o] of this.#cache.entries()) {
            if (matchPath(o.fullPath, url)) return key;
        }
    }

    getRoutes() {
        return this.#routes;
    }
}

const cacheProps = ["icon", "path", "descriptor"];
const routeProps = [
    "index",
    "path",
    "loader",
    "action",
    "shouldRevalidate",
    "element",
    "errorElement",
    "lazy",
    "handle"
];

export function useRouteHelper() {
    return useContext(RouteHelperContext);
}

export default RouteHelper;
