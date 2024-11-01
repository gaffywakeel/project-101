import PriceChart from "./PriceChart";
import PaymentAccountChart from "./PaymentAccountChart";
import WalletAccountChart from "./WalletAccountChart";
import FeatureLimits from "./FeatureLimits";
import RecentTransaction from "./RecentTransaction";
import ActivePeerTradeSell from "./ActivePeerTradeSell";
import ActivePeerTradeBuy from "./ActivePeerTradeBuy";

export default [
    {
        name: "price_chart",
        component: PriceChart
    },
    {
        name: "payment_account_chart",
        component: PaymentAccountChart
    },
    {
        name: "wallet_account_chart",
        component: WalletAccountChart
    },
    {
        name: "recent_activity",
        component: RecentTransaction
    },
    {
        name: "feature_limits",
        component: FeatureLimits
    },
    {
        name: "active_peer_trade_buy",
        component: ActivePeerTradeBuy
    },
    {
        name: "active_peer_trade_sell",
        component: ActivePeerTradeSell
    }
];
