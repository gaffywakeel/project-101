import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import fileList from "@iconify-icons/ri/file-list-2-fill";
import shieldStar from "@iconify-icons/ri/shield-star-fill";
import award from "@iconify-icons/ri/award-fill";
import UsersTab from "./_components/Users";
import Roles from "./_components/Roles";
import Verification from "./_components/Verification";
import PageTabs from "@/components/PageTabs";
import {useAuth} from "@/models/Auth";

const messages = defineMessages({
    title: {defaultMessage: "Users"},
    users: {defaultMessage: "Users"},
    verification: {defaultMessage: "Verification"},
    roles: {defaultMessage: "Roles"}
});

const Users = () => {
    const intl = useIntl();
    const auth = useAuth();

    const tabs = useMemo(() => {
        const stack = [
            {
                value: "users",
                label: intl.formatMessage(messages.users),
                icon: fileList,
                component: <UsersTab />
            }
        ];

        stack.push({
            value: "verification",
            label: intl.formatMessage(messages.verification),
            icon: shieldStar,
            component: <Verification />
        });

        if (auth.can("view:roles")) {
            stack.push({
                value: "roles",
                label: intl.formatMessage(messages.roles),
                icon: award,
                component: <Roles />
            });
        }
        return stack;
    }, [intl, auth]);

    return (
        <PageTabs
            initial="users"
            title={intl.formatMessage(messages.title)}
            tabs={tabs}
        />
    );
};

export {Users as Component};
export default Users;
