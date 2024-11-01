import Model from "@/models/Model";
import {url} from "@/utils/helpers";

export default class CommercePayment extends Model {
    get link() {
        return url("/payments/" + this.get("id"));
    }
}
