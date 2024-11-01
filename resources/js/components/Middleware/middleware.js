import {useAuth} from "@/models/Auth";
import {pipe} from "@/utils/helpers";
import PropTypes from "prop-types";

const Middleware = ({rules = [], children: node}) => {
    const auth = useAuth();
    const show = () => node;
    const context = {auth};

    rules = [].concat(rules).reverse();

    return pipe(...rules)(show)(context);
};

Middleware.propTypes = {
    rules: PropTypes.oneOfType([PropTypes.array, PropTypes.func]).isRequired,
    children: PropTypes.node.isRequired
};

export default Middleware;
