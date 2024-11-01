import Model from "@/models/Model";
import Wallet from "@/models/Wallet";

export default class StakePlan extends Model {
    /**
     * Get related Wallet
     *
     * @returns {Wallet}
     */
    get wallet() {
        return new Wallet(this.get("wallet"));
    }
}
