import {useAuth} from "@/models/Auth";
import PropTypes from "prop-types";

const Authorized = ({permission, children, fallback = null}) => {
    const auth = useAuth();

    if (auth.can(permission)) {
        return children;
    } else {
        return fallback;
    }
};

Authorized.propTypes = {
    permission: PropTypes.string.isRequired,
    children: PropTypes.node,
    fallback: PropTypes.node
};

export default Authorized;
