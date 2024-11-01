import {defineMessages, useIntl} from "react-intl";
import {useMemo} from "react";

/* eslint-disable require-await */

const messages = defineMessages({
    confirmed: {
        defaultMessage: "`$'{name}'` confirmation does not match"
    },
    requiredWithout: {
        defaultMessage: "`$'{name}'` is required when `{other}` is not present"
    },
    requiredWith: {
        defaultMessage: "`$'{name}'` is required when `{other}` is present"
    }
});

const useFormRules = () => {
    const intl = useIntl();

    return useMemo(
        () => ({
            confirmed(field) {
                return (form) => ({
                    validator(rule, value) {
                        if (form.getFieldValue(field) !== value) {
                            return Promise.reject();
                        } else {
                            return Promise.resolve();
                        }
                    },
                    message: intl.formatMessage(messages.confirmed)
                });
            },
            requiredWithout(field) {
                return (form) => ({
                    required: !form.getFieldValue(field),
                    message: intl.formatMessage(messages.requiredWithout, {
                        other: field
                    })
                });
            },
            requiredWith(field) {
                return (form) => ({
                    required: Boolean(form.getFieldValue(field)),
                    message: intl.formatMessage(messages.requiredWith, {
                        other: field
                    })
                });
            }
        }),
        [intl]
    );
};

export default useFormRules;
