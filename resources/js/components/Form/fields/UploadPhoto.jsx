import React, {useContext} from "react";
import {FormInputContext} from "@/components/Form/contexts";
import BaseUploadPhoto from "@/components/UploadPhoto";
import {isEmpty} from "lodash";

const UploadPhoto = ({caption, ...baseProps}) => {
    const {
        isRequired,
        validateStatus,
        errors = []
    } = useContext(FormInputContext);

    caption = isEmpty(errors) ? caption : errors.join(", ");

    return (
        <BaseUploadPhoto
            caption={caption}
            error={validateStatus === "error"}
            required={isRequired}
            {...baseProps}
        />
    );
};

export default UploadPhoto;
