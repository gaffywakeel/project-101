import Model from "@/models/Model";
import Coin from "@/models/Coin";

export default class Wallet extends Model {
    /**
     * Get coin
     *
     * @returns {Coin}
     */
    get coin() {
        return new Coin(this.get("coin"));
    }
}
