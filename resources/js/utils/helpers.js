import {createElement, useEffect, useRef} from "react";
import {
    ceil,
    flattenDeep,
    isArray,
    isFunction,
    isString,
    keyBy,
    map,
    mapValues,
    mergeWith,
    reduce
} from "lodash";
import urlJoin from "proper-url-join";
import dayjs from "@/utils/dayjs";

export function pipe(...arg) {
    return (x) => arg.reduce((v, f) => f(v), x);
}

function isValidHex(color) {
    return isString(color) && /^#[0-9A-F]{6}$/i.test(color);
}

function buildHex(R, B, G) {
    return (
        "#" +
        (
            0x1000000 +
            (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
            (B < 255 ? (B < 1 ? 0 : B) : 255) * 0x100 +
            (G < 255 ? (G < 1 ? 0 : G) : 255)
        )
            .toString(16)
            .slice(1)
    );
}

export function lightenColor(color, percent) {
    if (!isValidHex(color)) {
        return color;
    }
    const amt = Math.round(2.55 * percent),
        num = parseInt(color.replace("#", ""), 16),
        R = (num >> 16) + amt,
        B = ((num >> 8) & 0x00ff) + amt,
        G = (num & 0x0000ff) + amt;

    return buildHex(R, B, G);
}

export function darkenColor(color, percent) {
    if (!isValidHex(color)) {
        return color;
    }
    const amt = Math.round(2.55 * percent),
        num = parseInt(color.replace("#", ""), 16),
        R = (num >> 16) - amt,
        B = ((num >> 8) & 0x00ff) - amt,
        G = (num & 0x0000ff) - amt;

    return buildHex(R, B, G);
}

export function normalizeAttrs(attrs) {
    return Object.keys(attrs).reduce((acc, key) => {
        const val = attrs[key];
        switch (key) {
            case "class":
                acc.className = val;
                delete acc.class;
                break;
            default:
                acc[key] = val;
        }
        return acc;
    }, {});
}

export function generateIcon(node, key, props = {}) {
    return createElement(
        node.tag,
        {
            key,
            ...normalizeAttrs(node.attributes || {}),
            ...props
        },
        (node.children || []).map((o, i) => {
            return generateIcon(o, `${key}-${node.tag}-${i}`);
        })
    );
}

export function insertBetween(arr, value) {
    return flattenDeep(reduce(arr, (p, c) => [p, value, c]));
}

export function joinPath(...parts) {
    return urlJoin(...parts);
}

export function useVar(value) {
    const ref = useRef(null);

    if (ref.current === null) {
        if (isFunction(value)) {
            ref.current = value();
        } else {
            ref.current = value;
        }
    }

    return ref.current;
}

export function usePrevious(value) {
    const ref = useRef();
    useEffect(() => {
        ref.current = value;
    });
    return ref.current;
}

export function stringToHex(value) {
    let color = "#",
        hash = 0;

    if (value.length === 0) {
        return "#ccc";
    }
    for (let i = 0; i < value.length; i++) {
        hash = value.charCodeAt(i) + ((hash << 5) - hash);
        hash = hash & hash;
    }

    for (let i = 0; i < 3; i++) {
        const sub = (hash >> (i * 8)) & 255;
        color += ("00" + sub.toString(16)).substr(-2);
    }
    return color;
}

export function hexToRgb(hex) {
    if (hex.charAt(0) === "#") {
        hex = hex.substring(1);
    }

    if (hex.length === 3) {
        hex = [
            hex.charAt(0),
            hex.charAt(0),
            hex.charAt(1),
            hex.charAt(1),
            hex.charAt(2),
            hex.charAt(2)
        ].join("");
    }

    if (hex.length !== 6) {
        throw new Error("Invalid color format.");
    }

    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);

    return [r, g, b];
}

export function mergeArray(...sources) {
    return mergeWith(...sources, (a, b) => {
        if (isArray(a)) {
            return b.concat(a);
        }
    });
}

export function pluck(data, value, key = null) {
    return key ? mapValues(keyBy(data, key), value) : map(data, value);
}

export function url(path) {
    return createUrl(path).toString();
}

export function createUrl(url, base = null) {
    return new URL(url, base ?? window.location.origin);
}

export function isNumeric(value) {
    if (value === "") return false;
    return !Number.isNaN(value);
}

export function getNamePath(key) {
    return key.split(".").map((k) => (isNaN(k) ? k : Number(k)));
}

export function calculatePercent(value, total) {
    const divisor = total > 0 ? total : 1;
    return ceil((value * 100) / divisor);
}

export function parseDate(value, format) {
    return dayjs.utc(value, format).local();
}

export function splitNestedKeys(key, keyChain = []) {
    return key.split(".").map((path) => {
        keyChain.push(path);
        return keyChain.join(".");
    });
}

export function toArray(subject) {
    if (subject === undefined || subject === null) return [];
    return Array.isArray(subject) ? subject : [subject];
}
