import React, {useMemo, useRef} from "react";
import {usePatchElement} from "./usePatchElement";
import Modal from "@/components/Modal";

function useModal() {
    const modelKeyRef = useRef(0);
    const [elements, patch] = usePatchElement();

    const api = useMemo(
        () => ({
            confirm({key, ...modalProps}) {
                modelKeyRef.current = modelKeyRef.current + 1;
                const resetKey = `modal-${modelKeyRef.current}`;

                let removeModal = () => {};

                const content = (
                    <Modal
                        {...modalProps}
                        key={key ?? resetKey}
                        resetKey={resetKey}
                        afterClose={() => {
                            removeModal();
                        }}
                    />
                );

                removeModal = patch(content, key);
            }
        }),
        [patch]
    );

    return [api, elements];
}

export default useModal;
