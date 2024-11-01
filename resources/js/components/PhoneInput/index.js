import {defineMessages} from "react-intl";

const messages = defineMessages({
    invalidPhone: {defaultMessage: "{field} is an invalid phone number"}
});

export function phoneValidator(phoneRef, intl) {
    return () => ({
        validator(rule, value) {
            const phone = phoneRef.current;

            if (value && !phone?.isValidNumber()) {
                const message = intl.formatMessage(messages.invalidPhone, {
                    field: rule.field
                });

                return Promise.reject(new Error(message));
            }

            return Promise.resolve();
        }
    });
}

export * from "./phoneInput";
export {default} from "./phoneInput";
