import context from "@/contexts/AppContext";
import {notify} from "@/utils/index";

export function recaptchaSubmit(form, ref) {
    return () => {
        const recaptcha = ref.current;
        const size = context.settings.recaptcha.size;

        if (size === "invisible") {
            recaptcha
                ?.executeAsync?.()
                .then(() => {
                    recaptcha.resetCaptcha();
                    form.submit();
                })
                .catch((err) => {
                    notify.error(err);
                });
        } else {
            recaptcha?.resetCaptcha();
            form.submit();
        }
    };
}

export {default} from "./reCaptcha";
export * from "./reCaptcha";
