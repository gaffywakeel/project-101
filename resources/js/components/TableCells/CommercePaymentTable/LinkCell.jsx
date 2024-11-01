import React, {Fragment, useCallback, useMemo, useRef} from "react";
import {Box, Button, Stack, Typography} from "@mui/material";
import QrCodeScannerIcon from "@mui/icons-material/QrCodeScanner";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import useModal from "@/hooks/useModal";
import ModalContent from "@/components/ModalContent";
import ModalActions from "@/components/ModalActions";
import PrintIcon from "@mui/icons-material/Print";
import {truncate} from "lodash";
import {useReactToPrint} from "react-to-print";
import {styled} from "@mui/material/styles";
import CopyableButton from "@/components/CopyableButton";
import QRCode from "@/components/QRCode";
import {useBrand} from "@/hooks/settings";
import CommercePayment from "@/models/CommercePayment";

const messages = defineMessages({
    title: {defaultMessage: "Payment Link"}
});

const LinkCell = ({payment: data}) => {
    const intl = useIntl();
    const [modal, modalElements] = useModal();

    const payment = useMemo(() => {
        return CommercePayment.use(data);
    }, [data]);

    const displayLink = useCallback(() => {
        modal.confirm({
            title: intl.formatMessage(messages.title),
            content: <LinkCard payment={payment} />,
            dialogProps: {fullWidth: true, maxWidth: "xs"}
        });
    }, [modal, intl, payment]);

    return (
        <Fragment>
            <Button
                onClick={displayLink}
                startIcon={<QrCodeScannerIcon />}
                size="small">
                <FormattedMessage defaultMessage="View Link" />
            </Button>

            {modalElements}
        </Fragment>
    );
};

const LinkCard = ({payment}) => {
    const brand = useBrand();
    const contentRef = useRef();

    const print = useReactToPrint({
        suppressErrors: true,
        content: () => contentRef.current,
        bodyClass: "print-container"
    });

    return (
        <Stack spacing={3}>
            <ModalContent
                ref={contentRef}
                sx={{textAlign: "center"}}
                spacing={2}>
                <Typography variant="h5" noWrap>
                    {payment.title}
                </Typography>

                <Typography variant="caption">
                    {truncate(payment.description, {length: 200})}
                </Typography>

                <CodeBox
                    component={QRCode}
                    imageSrc={brand.logo_url}
                    value={payment.link}
                />
            </ModalContent>

            <ModalActions
                alignItems="center"
                justifyContent="center"
                spacing={2}>
                <Button
                    variant="contained"
                    startIcon={<PrintIcon />}
                    onClick={print}>
                    <FormattedMessage defaultMessage="Print" />
                </Button>

                <CopyableButton variant="contained" text={payment.link}>
                    <FormattedMessage defaultMessage="Copy" />
                </CopyableButton>
            </ModalActions>
        </Stack>
    );
};

const CodeBox = styled(Box)(({theme}) => ({
    maxWidth: 256,
    padding: theme.spacing(1),
    border: `1px dashed ${theme.palette.grey[500_32]}`,
    borderRadius: "5px",
    alignSelf: "center",
    width: "80%",
    height: "auto"
}));

export default LinkCell;
