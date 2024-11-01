import React, {Fragment, useCallback, useMemo} from "react";
import data from "@emoji-mart/data";
import EmojiPicker from "@emoji-mart/react";
import {isUndefined} from "lodash";
import {defineMessages, useIntl} from "react-intl";
import {GlobalStyles} from "@mui/material";
import {hexToRgb} from "@/utils/helpers";

const messages = defineMessages({
    search: {defaultMessage: "Search"},
    search_no_results_1: {defaultMessage: "Oh no!"},
    search_no_results_2: {defaultMessage: "That emoji couldn't be found"},
    pick: {defaultMessage: "Pick an emoji…"},
    add_custom: {defaultMessage: "Add custom emoji"},
    category_activity: {defaultMessage: "Activity"},
    category_custom: {defaultMessage: "Custom"},
    category_flags: {defaultMessage: "Flags"},
    category_foods: {defaultMessage: "Food & Drink"},
    category_recent: {defaultMessage: "Frequently Used"},
    category_nature: {defaultMessage: "Animals & Nature"},
    category_objects: {defaultMessage: "Objects"},
    category_people: {defaultMessage: "Smileys & Emotion"},
    category_places: {defaultMessage: "Travel & Places"},
    category_search: {defaultMessage: "Search Results"},
    category_symbols: {defaultMessage: "Symbols"},
    skintone_choose: {defaultMessage: "Choose default skin tone"},
    skintone_1: {defaultMessage: "Default"},
    skintone_2: {defaultMessage: "Light"},
    skintone_3: {defaultMessage: "Medium-Light"},
    skintone_4: {defaultMessage: "Medium"},
    skintone_5: {defaultMessage: "Medium-Dark"},
    skintone_6: {defaultMessage: "Dark"}
});

const Picker = ({value, onChange, ...otherProps}) => {
    const intl = useIntl();

    const content = useMemo(() => {
        return isUndefined(value) ? "" : value;
    }, [value]);

    const update = useCallback(
        (e) => onChange?.(content + e?.native),
        [onChange, content]
    );

    const i18n = useMemo(
        () => ({
            search: intl.formatMessage(messages.search),
            search_no_results_1: intl.formatMessage(
                messages.search_no_results_1
            ),
            search_no_results_2: intl.formatMessage(
                messages.search_no_results_2
            ),
            pick: intl.formatMessage(messages.pick),
            add_custom: intl.formatMessage(messages.add_custom),
            categories: {
                activity: intl.formatMessage(messages.category_activity),
                custom: intl.formatMessage(messages.category_custom),
                flags: intl.formatMessage(messages.category_flags),
                foods: intl.formatMessage(messages.category_foods),
                frequent: intl.formatMessage(messages.category_recent),
                nature: intl.formatMessage(messages.category_nature),
                objects: intl.formatMessage(messages.category_objects),
                people: intl.formatMessage(messages.category_people),
                places: intl.formatMessage(messages.category_places),
                search: intl.formatMessage(messages.category_search),
                symbols: intl.formatMessage(messages.category_symbols)
            },
            skins: {
                choose: intl.formatMessage(messages.skintone_choose),
                1: intl.formatMessage(messages.skintone_1),
                2: intl.formatMessage(messages.skintone_2),
                3: intl.formatMessage(messages.skintone_3),
                4: intl.formatMessage(messages.skintone_4),
                5: intl.formatMessage(messages.skintone_5),
                6: intl.formatMessage(messages.skintone_6)
            }
        }),
        [intl]
    );

    return (
        <Fragment>
            <EmojiPicker
                {...otherProps}
                i18n={i18n}
                onEmojiSelect={update}
                icons="solid"
                data={data}
            />

            {globalStyle}
        </Fragment>
    );
};

const rgb = (color) => hexToRgb(color).join(", ");

const globalStyle = (
    <GlobalStyles
        styles={(theme) => ({
            "em-emoji-picker": {
                "--shadow": "none",
                "--category-icon-size": "24px",
                "--font-size": "16px",
                "--rgb-accent": rgb(theme.palette.primary.main),
                "--background-rgb": rgb(theme.palette.primary.main),
                "--border-radius": theme.shape.borderRadius * 1.5,
                "--rgb-background": rgb(theme.palette.background.paper),
                "--rgb-input": rgb(theme.palette.background.neutral),
                "--rgb-color": rgb(theme.palette.text.primary),
                "--color-border-over": "rgba(0, 0, 0, 0.1)",
                "--color-border": "rgba(0, 0, 0, 0.05)",
                height: "50vh",
                minHeight: "300px",
                maxHeight: "600px"
            }
        })}
    />
);

export default Picker;
