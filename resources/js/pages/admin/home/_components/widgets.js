import SystemStatus from "./SystemStatus";
import RegistrationChart from "./RegistrationChart";
import LatestUsers from "./LatestUsers";
import PendingVerification from "./PendingVerification";
import PendingDeposits from "./PendingDeposits";
import PendingWithdrawals from "./PendingWithdrawals";
import EarningSummary from "./EarningSummary";

export default [
    {
        name: "earning_summary",
        component: EarningSummary
    },
    {
        name: "system_status",
        component: SystemStatus
    },
    {
        name: "pending_verification",
        component: PendingVerification
    },
    {
        name: "pending_deposits",
        component: PendingDeposits
    },
    {
        name: "pending_withdrawals",
        component: PendingWithdrawals
    },
    {
        name: "latest_users",
        component: LatestUsers
    },
    {
        name: "registration_chart",
        component: RegistrationChart
    }
];
