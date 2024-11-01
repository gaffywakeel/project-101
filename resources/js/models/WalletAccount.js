import Model from "@/models/Model";
import Wallet from "@/models/Wallet";
import User from "@/models/User";

export default class WalletAccount extends Model {
    /**
     * Format wallet value
     *
     * @param {number} value
     * @returns {string}
     */
    formatValue(value) {
        return `${value} ${this.wallet.coin.symbol}`;
    }

    /**
     * Get wallet
     *
     * @returns {Wallet}
     */
    get wallet() {
        return new Wallet(this.get("wallet"));
    }

    /**
     * Get wallet
     *
     * @returns {User}
     */
    get user() {
        return new User(this.get("user"));
    }
}
