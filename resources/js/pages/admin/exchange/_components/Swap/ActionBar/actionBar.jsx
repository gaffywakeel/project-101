import React from "react";
import {defineMessages, useIntl} from "react-intl";
import SearchTable from "@/components/SearchTable";
import ActionToolbar from "@/components/ActionToolbar";

const messages = defineMessages({
    search: {defaultMessage: "Search by user..."}
});

const ActionBar = () => {
    const intl = useIntl();

    return (
        <ActionToolbar>
            <SearchTable
                field="search_user"
                placeholder={intl.formatMessage(messages.search)}
                withParams={true}
            />
        </ActionToolbar>
    );
};

export default ActionBar;
