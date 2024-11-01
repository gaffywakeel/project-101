import React, {useCallback, useContext} from "react";
import {IconButton} from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";
import {defineMessages, useIntl} from "react-intl";
import PopConfirm from "@/components/PopConfirm";
import {errorHandler, route, useRequest} from "@/services/Http";
import LoadingIcon from "@/components/LoadingIcon";
import TableContext from "@/contexts/TableContext";

const messages = defineMessages({
    confirm: {defaultMessage: "Are you sure?"}
});

const RoleDelete = ({role}) => {
    const intl = useIntl();
    const {reload: reloadTable} = useContext(TableContext);
    const [request, loading] = useRequest();

    const deleteRole = useCallback(() => {
        request
            .delete(route("admin.roles.destroy", {role: role.id}))
            .then(() => reloadTable())
            .catch(errorHandler());
    }, [request, role, reloadTable]);

    if (!role.delete_policy) {
        return null;
    }

    return (
        <PopConfirm
            component={IconButton}
            content={intl.formatMessage(messages.confirm)}
            onClick={deleteRole}>
            <LoadingIcon
                color="error"
                component={DeleteIcon}
                loading={loading}
            />
        </PopConfirm>
    );
};

export default RoleDelete;
