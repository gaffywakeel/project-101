import User from "@/models/User";
import Model from "./Model";
import {useSelector} from "react-redux";
import {useMemo} from "react";

export default class Auth extends Model {
    /**
     * Check if user is authenticated
     */
    check() {
        return this.user.isNotEmpty();
    }

    /**
     * Get system permissions
     */
    permissions() {
        return this.get("permissions", []);
    }

    /**
     * Check if system permission exist.
     */
    permissionExist(name) {
        return this.permissions().findIndex((o) => o.name === name) >= 0;
    }

    /**
     * Check if user has permission
     */
    can(name) {
        if (!this.permissionExist(name)) return false;

        if (this.user.isAdministrator()) return true;

        return this.user.permissions().includes(name);
    }

    /**
     * Check if user does not have permission
     */
    cannot(name) {
        return !this.can(name);
    }

    /**
     * Get authentication credential
     */
    credential() {
        return this.get("credential");
    }

    /**
     * Should require Two Factor
     */
    requireTwoFactor() {
        return this.user.enabledTwoFactor();
    }

    /**
     * Check if user setup is required
     */
    requireUserSetup() {
        return this.get("setup") && !this.user.hasCompleteSetup();
    }

    /**
     * Get user object model
     */
    get user() {
        return new User(this.get("user"));
    }
}

/**
 * Auth Custom Hook
 *
 * @returns {Auth|Model}
 */
export function useAuth() {
    const auth = useSelector((state) => state.auth);
    return useMemo(() => new Auth(auth), [auth]);
}
