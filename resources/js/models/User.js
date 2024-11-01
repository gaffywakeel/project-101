import Model from "./Model";
import UserProfile from "./UserProfile";
import {dayjs, parseDate} from "@/utils/index";

class User extends Model {
    /**
     * Determine if user has Administrator role
     */
    isAdministrator() {
        return this.get("is_administrator", false);
    }

    /**
     * Get all roles
     */
    roles() {
        return this.get("all_roles", []);
    }

    /**
     * Get all permissions
     */
    permissions() {
        return this.get("all_permissions", []);
    }

    /**
     * Check if user has verified email
     */
    hasVerifiedEmail() {
        return Boolean(this.get("email_verified_at"));
    }

    /**
     * Check if user has verified phone
     */
    hasVerifiedPhone() {
        return Boolean(this.get("phone_verified_at"));
    }

    /**
     * Check if user has enabled two factor
     */
    enabledTwoFactor() {
        return Boolean(this.get("two_factor_enable"));
    }

    /**
     * Determine if profile is complete
     */
    isProfileComplete() {
        return this.profile.isComplete();
    }

    /**
     * Get profile picture url
     */
    getProfilePicture() {
        return this.profile.get("picture");
    }

    /**
     * Deactivated until
     */
    deactivatedUntil() {
        return parseDate(this.get("deactivated_until"));
    }

    /**
     * Check if active
     */
    isActive() {
        if (!this.get("deactivated_until")) return true;

        return this.deactivatedUntil().isBefore(dayjs());
    }

    /**
     * Check if user has completed setup
     */
    hasCompleteSetup() {
        return (
            this.enabledTwoFactor() &&
            this.hasVerifiedEmail() &&
            this.isProfileComplete()
        );
    }

    /**
     * Determine if country is supported.
     */
    countrySupport() {
        return this.get("country_operation", false);
    }

    /**
     * Get userProfile object model
     */
    get profile() {
        return new UserProfile(this.get("profile"));
    }
}

export default User;
