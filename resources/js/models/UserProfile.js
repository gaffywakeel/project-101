import Model from "./Model";

class UserProfile extends Model {
    /**
     * Check if profile is complete
     */
    isComplete() {
        return this.get("is_complete");
    }
}

export default UserProfile;
