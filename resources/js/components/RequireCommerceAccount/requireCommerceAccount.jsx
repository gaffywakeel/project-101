import React from "react";
import {useCommerceAccount} from "@/hooks/accounts";
import CreateAccount from "./CreateAccount";

const RequireCommerceAccount = ({children}) => {
    const {account} = useCommerceAccount();

    if (account.isNotEmpty()) return children;

    return <CreateAccount />;
};

export default RequireCommerceAccount;
